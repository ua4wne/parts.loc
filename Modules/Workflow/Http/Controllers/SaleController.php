<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Car;
use App\Models\Currency;
use App\Models\Delivery;
use App\Models\DeliveryMethod;
use App\Models\Organisation;
use App\Models\Priority;
use App\Models\Statuse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Location;
use Modules\Warehouse\Entities\Reservation;
use Modules\Warehouse\Entities\Specification;
use Modules\Warehouse\Entities\Stock;
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Workflow\Entities\Agreement;
use Modules\Workflow\Entities\Application;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Sale;
use Modules\Workflow\Entities\Shipment;
use Modules\Workflow\Entities\TblApplication;
use Modules\Workflow\Entities\TblSale;
use Validator;

class  SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!User::hasRole('manager') && !User::hasRole('director')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр данных!');
        }
        if (view()->exists('workflow::sales_area')) {
            $title = 'Рабочее место менеджера';
            $rows = Good::offset(0)->limit(10)->get();
            $sales = Sale::where('state','<',3)->orderBy('created_at','desc')->get();
            $firms = Firm::all();
            $firmsel = array();
            foreach ($firms as $val) {
                $firmsel[$val->id] = $val->title;
            }
            $data = [
                'title' => $title,
                'head' => 'Помощник продаж',
                'firmsel' => $firmsel,
                'rows' => $rows,
                'sales' => $sales,
            ];
            return view('workflow::sales_area', $data);
        }
        abort(404);
    }

    public function orders()
    {
        if (view()->exists('workflow::sale_orders')) {
            $rows = Sale::all();
            $data = [
                'title' => 'Заказы клиентов',
                'head' => 'Заказы клиентов',
                'rows' => $rows,
            ];

            return view('workflow::sale_orders', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            $msg = 'Попытка создания нового заказа клиента!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;
            if (empty($input['has_vat'])) $input['has_vat'] = 0;
            if (empty($input['to_door'])) $input['to_door'] = 0;
            if (empty($input['delivery_in_price'])) $input['delivery_in_price'] = 0;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|unique:orders|string|max:15',
                'firm_id' => 'required|integer',
                'organisation_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'warehouse_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'delivery_method_id' => 'required|integer',
                'delivery_id' => 'required|integer',
                'agreement_id' => 'required|integer',
                'destination' => 'required|string|max:150',
                'contact' => 'nullable|string|max:100',
                'to_door' => 'nullable|integer',
                'delivery_in_price' => 'nullable|integer',
                'user_id' => 'required|integer',
                'date_agreement' => 'nullable|date',
                'has_vat' => 'nullable|integer',
                'doc_num_firm' => 'nullable|string|max:15',
                'date_firm' => 'nullable|date',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $sale = new Sale();
            $sale->fill($input);
            $sale->user_id = $input['user_id'];
            $sale->created_at = date('Y-m-d H:i:s');
            if ($sale->save()) {
                $msg = 'Заказ клиента № ' . $input['doc_num'] . ' был успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/sales/orders')->with('status', $msg);
            }
        }
        if (view()->exists('workflow::sale_add')) {
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::select('id', 'title')->where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $wxs = Warehouse::all();
            $wxsel = array();
            foreach ($wxs as $val) {
                $wxsel[$val->id] = $val->title;
            }
            $doc_num = LibController::GenNumberDoc('sales');
            $vat = env('VAT');
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $agreements = Agreement::select('id', 'title')->get();
            $agrsel = array();
            foreach ($agreements as $val) {
                $agrsel[$val->id] = $val->title;
            }
            $methods = DeliveryMethod::all();
            $dmethods = array();
            foreach ($methods as $val) {
                $dmethods[$val->id] = $val->title;
            }
            $deliveries = Delivery::all();
            $delivs = array();
            foreach ($deliveries as $val) {
                $delivs[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Заказы клиентов',
                'head' => 'Новый заказ клиента',
                'usel' => $usel,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
                'doc_num' => $doc_num,
                'wxsel' => $wxsel,
                'vat' => $vat,
                'unsel' => $unsel,
                'agrsel' => $agrsel,
                'dmethods' => $dmethods,
                'delivs' => $delivs,
            ];
            return view('workflow::sale_add', $data);
        }
        abort(404);
    }

    public function findGoodAnalogs(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $filter = $input['filter'];
            $content = '';
            if (isset($filter) && isset($input['name'])) {
                switch ($filter) {
                    case 'name':
                        $arr = explode('(', $input['name']);
                        $name = trim($arr[0]);
                        $good = Good::where('title', $name)->first();
                        break;
                    case 'vendor':
                        $good = Good::where('vendor_code', $input['name'])->first();
                        break;
                    case 'analog':
                        $good = Good::where('analog_code', 'LIKE', '%' . $input['name'] . '%')->first();
                        break;
                    case 'catalog':
                        $good = Good::where('catalog_num', $input['name'])->first();
                        break;
                }
                if (!empty($good)) {
                    $content .= '<tr id="' . $good->id . '" class="text-bold text-green clicable"><td>' . $good->code . '</td><td>' . $good->vendor_code . '</td>
                                <td>' . $good->title . '</td><td>' . $good->category->category . '</td></tr>';
                    if (!empty($good->catalog_num)) {
                        $analogs = Good::where('catalog_num', $good->catalog_num)->where('id', '!=', $good->id)->get();
                        if (!empty($analogs)) {
                            foreach ($analogs as $row) {
                                $content .= '<tr id="' . $row->id . '" class="clicable"><td>' . $row->code . '</td><td>' . $row->vendor_code . '</td>
                            <td>' . $row->title . '</td><td>' . $row->category->category . '</td></tr>';
                            }
                        }
                    }
                }
            }
            return $content;
        }
    }

    public function GoodParams(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $tab = $input['name'];
            $id = $input['good_id'];
            $content = '';
            if (!empty($tab) && !empty($id)) {
                switch ($tab) {
                    case 'common':
                        $good = Good::find($id);
                        if (!empty($good)) {
                            $content .= '<tr><td>Код 1С</td><td>' . $good->code . '</td></tr>';
                            $content .= '<tr><td>Наименование</td><td>' . $good->title . '</td></tr>';
                            $content .= '<tr><td>Описание</td><td>' . $good->descr . '</td></tr>';
                            $content .= '<tr><td>Артикул</td><td>' . $good->vendor_code . '</td></tr>';
                            $content .= '<tr><td>Аналоги</td><td>' . $good->analog_code . '</td></tr>';
                            $content .= '<tr><td>Каталожный №</td><td>' . $good->catalog_num . '</td></tr>';
                            $content .= '<tr><td>Бренд</td><td>' . $good->brand . '</td></tr>';
                            $content .= '<tr><td>Группа товара</td><td>' . $good->category->category . '</td></tr>';
                            $content .= '<tr><td>Складская группа</td><td>' . $good->group->title . '</td></tr>';
                            $unit = Unit::find($good->unit_id)->title;
                            $content .= '<tr><td>Основная ед. изм.</td><td>' . $unit . '</td></tr>';
                            $content .= '<tr><td>Штрих-код</td><td>' . $good->barcode . '</td></tr>';
                            $content .= '<tr><td>Ставка НДС</td><td>' . $good->vat . '</td></tr>';
                        }
                        break;
                    case 'stock':
                        $rows = Stock::where('good_id', $id)->get();
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                $content .= '<tr><td>' . $row->warehouse->title . '</td>';
                                $content .= '<td>' . $row->good->title . '</td>';
                                $content .= '<td>' . $row->location->title . '</td>';
                                $content .= '<td>' . $row->qty . '</td>';
                                $content .= '<td>' . $row->unit->title . '</td>';
                                $content .= '<td>' . $row->cost . '</td>';
                                $content .= '<td>' . $row->consignment . '</td></tr>';
                            }
                        }
                        break;
                }

            }
            return $content;
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        if (view()->exists('workflow::sale_view')) {
            $stats = Statuse::all();
            $statsel = array();
            foreach ($stats as $val) {
                $statsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::select('id', 'title')->where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $wxs = Warehouse::all();
            $wxsel = array();
            foreach ($wxs as $val) {
                $wxsel[$val->id] = $val->title;
            }
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $sale = Sale::find($id);
            $contracts = Contract::select('id', 'title')->where(['firm_id' => $sale->firm_id, 'organisation_id' => $sale->organisation_id])->get();
            $contsel = array();
            foreach ($contracts as $val) {
                $contsel[$val->id] = $val->title;
            }
            $agreements = Agreement::select('id', 'title')->get();
            $agrsel = array();
            foreach ($agreements as $val) {
                $agrsel[$val->id] = $val->title;
            }
            $methods = DeliveryMethod::all();
            $dmethods = array();
            foreach ($methods as $val) {
                $dmethods[$val->id] = $val->title;
            }
            $deliveries = Delivery::all();
            $delivs = array();
            foreach ($deliveries as $val) {
                $delivs[$val->id] = $val->title;
            }
            $rows = TblSale::where('sale_id', $id)->get();
            $vat = 0;
            if ($sale->has_vat) $vat = env('VAT');
            $tbody = '';
            //цепочка связанных документов
            $links = Application::where('sale_id', $id)->get();
            if (!empty($links)) {
                foreach ($links as $row) {
                    $tbody .= '<tr><td class="text-bold"><a href="/applications/view/' . $row->id . '" target="_blank">
                    Запрос по ценам №' . $row->doc_num . '</a></td>';
                    if (isset($row->statuse_id)) {
                        $tbody .= '<td>' . $row->statuse->title . '</td>';
                    } else {
                        $tbody .= '<td></td>';
                    }
                    $tbody .= '<td>'
                        . $row->created_at . '</td><td>' . $row->user->name . '</td></tr>';
                }
            }
            $links = Shipment::where('sale_id', $id)->get();
            if (!empty($links)) {
                foreach ($links as $row) {
                    $tbody .= '<tr><td class="text-bold"><a href="/shipments/view/' . $row->id . '" target="_blank">
                    Наряд на сборку №' . $row->doc_num . '</a></td>';
                    $tbody .= '<td>' . $row->status . '</td>';

                    $tbody .= '<td>'
                        . $row->created_at . '</td><td>' . $row->user->name . '</td></tr>';
                }
            }
            $data = [
                'title' => 'Заказы клиентов',
                'head' => 'Заказ клиента № ' . $sale->doc_num,
                'statsel' => $statsel,
                'usel' => $usel,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
                'wxsel' => $wxsel,
                'unsel' => $unsel,
                'sale' => $sale,
                'contsel' => $contsel,
                'vat' => $vat,
                'rows' => $rows,
                'agrsel' => $agrsel,
                'dmethods' => $dmethods,
                'delivs' => $delivs,
                'tbody' => $tbody,
            ];
            return view('workflow::sale_view', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public
    function edit($id, Request $request)
    {
        $sale = Sale::find($id);
        if (!Role::granted('orders')) {//вызываем event
            $msg = 'Попытка редактирования заявки клиента №' . $sale->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование заявок от клиентов!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if (empty($input['has_vat'])) $input['has_vat'] = 0;
            if (empty($input['to_door'])) $input['to_door'] = 0;
            if (empty($input['delivery_in_price'])) $input['delivery_in_price'] = 0;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|unique:orders|string|max:15',
                'firm_id' => 'required|integer',
                'organisation_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'warehouse_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'delivery_method_id' => 'required|integer',
                'delivery_id' => 'required|integer',
                'agreement_id' => 'required|integer',
                'destination' => 'required|string|max:150',
                'contact' => 'nullable|string|max:100',
                'to_door' => 'nullable|integer',
                'delivery_in_price' => 'nullable|integer',
                'user_id' => 'required|integer',
                'date_agreement' => 'nullable|date',
                'has_vat' => 'nullable|integer',
                'doc_num_firm' => 'nullable|string|max:15',
                'date_firm' => 'nullable|date',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $sale->fill($input);
            if ($sale->update()) {
                $msg = 'Данные заказа клиента № ' . $sale->doc_num . ' были успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public
    function getSale(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select id, `doc_num` as `name` from sales where `doc_num` like '%$query%'");
        return response()->json($codes);
    }

    public
    function addPosition(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (!empty($input['comment']))
                $input['comment'] = Specification::find($input['comment'])->title;
            $model = new TblSale();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            $model->reserved = 0;
            if ($model->save()) {
                $msg = 'Добавлена новая позиция ' . $model->good->title . ' к заказу клиента №' . $model->sale->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $content = '<tr id="' . $model->id . '">
                    <td>' . $model->good->vendor_code . '</td>
                    <td>' . $model->good->title . '</td>
                    <td>' . $model->comment . '</td>
                    <td>' . $model->qty . '</td>
                    <td>' . $model->reserved . '</td>
                    <td>' . $model->unit->title . '</td>
                    <td>' . $model->price . '</td>
                    <td>' . $model->amount . '</td>
                    <td>' . $model->vat . '</td>
                    <td>' . $model->vat_amount . '</td>
                    <td style="width:70px;">    <div class="form-group" role="group">';
                if ($model->good->has_specification) {
                    $content .= '<button class="btn btn-info btn-sm pos_spec"
                                                                        type="button" title="Характеристики"><i
                                                                        class="fa fa-cog fa-lg" aria-hidden="true"></i>
                                                                </button>';
                }
                $content .= '<button class="btn btn-danger btn-sm pos_delete" type="button" title="Удалить позицию">
                            <i class="fa fa-trash fa-lg" aria-hidden="true"></i></button>
                        </div>
                    </td>
                </tr>';
                $amount = $model->sale->amount + $model->sale->vat_amount;
                $num = TblSale::where('sale_id', $model->sale_id)->count('id');
                $result = ['content' => $content, 'num' => $num, 'amount' => $amount];
                return json_encode($result);
            }
        }
        return 'NO';
    }

    public
    function delPosition(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $pos = TblSale::find($input['id']);
            if (!empty($pos)) {
                $sale_id = $pos->sale_id;
                $msg = 'Удалена позиция ' . $pos->good->title . ' из зазаявки клиента №' . $pos->sale->doc_num;
                $pos->delete();

                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $doc = Sale::find($sale_id);
                $amount = $doc->amount + $doc->vat_amount;
                $num = TblSale::where('sale_id', $sale_id)->count('id');
                $result = ['num' => $num, 'amount' => $amount];
                return json_encode($result);
            }
        }
        return 'NO';
    }

    public
    function editPos(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (!empty($input['comment']))
                $input['comment'] = Specification::find($input['comment'])->title;
            $pos_id = $input['pos_id'];
            unset($input['pos_id']);
            $pos = TblSale::find($pos_id);
            if (!empty($pos)) {
                $pos->fill($input);
                if ($pos->update()) {
                    $amount = $pos->sale->amount + $pos->sale->vat_amount;
                    $num = TblSale::where('sale_id', $pos->sale_id)->count('id');
                    $result = ['vat_amount' => $pos->vat_amount, 'num' => $num, 'amount' => $amount];
                    return json_encode($result);
                }
            }
            return 'ERR';
        }
        return 'NO';
    }

    public
    function setReserv(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (empty($input['sale_id']))
                return 'NO';
            $rows = TblSale::where('sale_id', $input['sale_id'])->get();
            if (!empty($rows)) {
                //определяем склад
                $whs_id = Sale::find($input['sale_id'])->warehouse_id;
                foreach ($rows as $row) {
                    $need_qty = $row->qty;
                    if ($row->qty > $row->reserved) { //нужно резервировать товар?
                        //определяем сумму необходимого резерва
                        $need_qty -= $row->reserved_qty;
                        //смотрим остатки на складе
                        $free_qty = $this->getFreeQty($whs_id, $row->good_id);
                        if ($need_qty && $free_qty) { //нужен резерв и есть остатки на складе
                            //резервируем товар
                            $leftovers = Stock::where(['warehouse_id' => $whs_id, 'good_id' => $row->good_id])->get();
                            if (!empty($leftovers)) {
                                foreach ($leftovers as $stock) {
                                    if (!$stock->location->out_lock) { //ячейка не заблокирована на выход
                                        $reserv = new Reservation();
                                        $location = Location::find($stock->location_id);
                                        if ($stock->qty >= $need_qty) {
                                            $reserv->location_id = $location->id;
                                            $reserv->tbl_sale_id = $row->id;
                                            $reserv->qty = $stock->qty - $need_qty;
                                            $reserv->created_at = date('Y-m-d H:i:s');
                                            if ($reserv->save()) {
                                                $stock->qty = $stock->qty - $need_qty; // уменьшаем кол-во на кол-во резерва
                                                $stock->update();
                                                $row->reserved += $reserv->qty;
                                                $row->update();
                                            }
                                            break;
                                        }
                                        if ($stock->qty < $need_qty) {
                                            $reserv->location_id = $location->id;
                                            $reserv->tbl_sale_id = $row->id;
                                            $reserv->qty = $stock->qty;
                                            $reserv->created_at = date('Y-m-d H:i:s');
                                            if ($reserv->save()) {
                                                $stock->qty = 0; // уменьшаем кол-во на кол-во резерва
                                                $stock->update();
                                                $need_qty -= $reserv->qty;
                                                $row->reserved += $reserv->qty;
                                                $row->update();
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
                return 'OK';
            }
        }
        return 'NO';
    }

    public
    function dropReserv(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (empty($input['sale_id']))
                return 'NO';
            $rows = TblSale::where('sale_id', $input['sale_id'])->get();
            if (!empty($rows)) {
                //определяем склад
                $whs_id = Sale::find($input['sale_id'])->warehouse_id;
                foreach ($rows as $row) {
                    //ищем резервирование позиции
                    $reserv = Reservation::where('tbl_sale_id', $row->id)->get();
                    if (!empty($reserv)) {
                        //снимаем резервы
                        foreach ($reserv as $pos) {
                            //товар в ячейке на складе еще есть?
                            $stock = Stock::where(['warehouse_id' => $whs_id, 'location_id' => $pos->location_id, 'good_id' => $row->good_id])->get();
                            if (empty($stock)) {
                                $stock = new Stock();
                                $stock->warehouse_id = $whs_id;
                                $stock->good_id = $row->good_id;
                                $stock->location_id = $pos->location_id;
                                $stock->qty = $pos->qty;
                                $stock->unit_id = $row->unit_id;
                                $stock->cost = $row->price;
                                if ($stock->save()) {
                                    $row->qty -= $pos->qty;
                                    $pos->delete();
                                    $row->update();
                                }
                            } else {
                                foreach ($stock as $st) {
                                    $st->qty = $pos->qty;
                                    if ($st->update()) {
                                        $row->reserved -= $pos->qty;
                                        $pos->delete();
                                        $row->update();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return 'OK';
        }
        return 'NO';
    }

    public
    function newApplication(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (empty($input['sale_id']))
                return 'NO';
            //генерим новый документ
            $priority = Priority::offset(1)->limit(1)->first();
            $statuse = Statuse::offset(0)->limit(1)->first();
            $appl = new Application();
            $appl->doc_num = LibController::GenNumberDoc('applications');
            $appl->priority_id = $priority->id;
            $appl->statuse_id = $statuse->id;
            $appl->sale_id = $input['sale_id'];
            $appl->author_id = Auth::id();
            $appl->user_id = Auth::id();
            $appl->created_at = date('Y-m-d H:i:s');
            if ($appl->save()) {
                //заполняем табличную часть
                $rows = TblSale::where('sale_id', $input['sale_id'])->get();
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        if ($row->need_qty) {
                            $tbl = new TblApplication();
                            $tbl->application_id = $appl->id;
                            $tbl->good_id = $row->good_id;
                            $tbl->qty = $row->need_qty;
                            $tbl->unit_id = $row->unit_id;
                            $tbl->car_id = Car::offset(0)->limit(1)->first()->id;
                            $tbl->save();
                        }
                    }
                }
                return 'OK';
            }
        }
        return 'NO';
    }

    private
    function getFreeQty($wx_id, $good_id)
    {
        //ищем остатки товара на складе
        $qty = 0;
        $leftovers = Stock::where(['warehouse_id' => $wx_id, 'good_id' => $good_id])->get();
        if (!empty($leftovers)) {
            foreach ($leftovers as $row) {
                if (!$row->location->out_lock) { //ячейка не заблокирована на выход
                    $qty += $row->qty;
                }
            }
        }
        return $qty;
    }

    public function delete(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['sale_id'];
            $sale = Sale::find($id);
            if (!empty($sale)) {
                if (!User::hasRole('admin') || !User::isAuthor($sale->user_id)) {//вызываем event
                    $msg = 'Попытка удаления заявки клиента №' . $sale->doc_num . '!';
                    event(new AddEventLogs('access', Auth::id(), $msg));
                    abort(503, 'У Вас нет прав на удаление документа!');
                }
                $msg = "В процессе удаления документа произошла ошибка!";
                if($sale->delete()){
                    $msg = 'Удалена заявка клиента № ' . $sale->doc_num;
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                }
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public function delSale(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $sale = Sale::find($id);
            if (!empty($sale)) {
                if (!User::hasRole('admin') || !User::isAuthor($sale->user_id)) {//вызываем event
                    return 'BAD';
                }
                if($sale->delete()){
                    $msg = 'Удалена заявка клиента № ' . $sale->doc_num;
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                }
                return 'OK';
            }
        }
        return 'NO';
    }

    public function docList(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $sales = Sale::where('state','<',3)->orderBy('created_at','desc')->get();
            $content = '';
            if(!empty($sales)){
                foreach ($sales as $row){
                    $content .= '<tr id="sale'.$row->id.'" class="row_clicable sale-pos" style="cursor: pointer;">
                                    <td>'.$row->doc_num.'</td>
                                    <td>'.$row->created_at.'</td>
                                    <td>'.$row->amount.'</td>
                                    <td>'.$row->firm->title.'</td>
                                    <td>'.$row->status.'</td>
                                    <td>'.$row->date_agreement.'</td>
                                    <td>%</td>
                                    <td>%</td>
                                    <td>'.$row->currency->title.'</td>
                                    <td>'.$row->user->name.'</td>
                                    <td style="width:100px;">
                                        <div class="form-group" role="group">
                                            <a href="/sales/view/'.$row->id.'">
                                                <button class="btn btn-info" type="button" title="Просмотр записи"><i class="fa fa-eye fa-lg>" aria-hidden="true"></i></button>
                                            </a>
                                            <button class="btn btn-danger del_pos" type="button"
                                                    title="Удалить запись"><i class="fa fa-trash fa-lg>"
                                                                               aria-hidden="true"></i></button>
                                        </div>
                                    </td>
                                </tr>';
                }
            }
            return $content;
        }
    }
}
