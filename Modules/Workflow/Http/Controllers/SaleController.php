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
use Dompdf\Dompdf;
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
use Modules\Workflow\Entities\Contact;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\PricingRule;
use Modules\Workflow\Entities\Sale;
use Modules\Workflow\Entities\SetOffer;
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
            $sales = Sale::where('state', '<', 3)->orderBy('created_at', 'desc')->get();
            $firms = Firm::all();
            $firmsel = array();
            foreach ($firms as $val) {
                $firmsel[$val->id] = $val->title;
            }
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $data = [
                'title' => $title,
                'head' => 'Помощник продаж',
                'firmsel' => $firmsel,
                'rows' => $rows,
                'sales' => $sales,
                'unsel' => $unsel,
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
                'state' => 'required|integer',
                'destination' => 'required|string|max:150',
                'contact' => 'nullable|string|max:100',
                'to_door' => 'nullable|integer',
                'delivery_in_price' => 'nullable|integer',
                'user_id' => 'required|integer',
                'date_agreement' => 'nullable|date',
                'has_vat' => 'nullable|integer',
                'price_type' => 'required|in:retail,wholesale,small',
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
                                <td>' . $good->title . '</td><td data-toggle="modal" data-target="#editDoc">
                                            <i class="fa fa-cart-plus" aria-hidden="true"></i></td></tr>';
                    if (!empty($good->catalog_num)) {
                        $analogs = Good::where('catalog_num', $good->catalog_num)->where('id', '!=', $good->id)->get();
                        if (!empty($analogs)) {
                            foreach ($analogs as $row) {
                                $content .= '<tr id="' . $row->id . '" class="clicable"><td>' . $row->code . '</td><td>' . $row->vendor_code . '</td>
                            <td>' . $row->title . '</td><td data-toggle="modal" data-target="#editDoc">
                                            <i class="fa fa-cart-plus" aria-hidden="true"></i></td></tr>';
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
                        $whs = Warehouse::all();
                        if(!empty($whs)){
                            foreach ($whs as $wh){
                                $rows = DB::select("SELECT warehouse_id, good_id, unit_id, SUM(qty) AS sum_qty,
                                                    SUM(cost) AS sum_cost FROM stocks WHERE good_id = $id AND warehouse_id = $wh->id
                                                    GROUP BY warehouse_id,unit_id");
                                if (!empty($rows)) {
                                    foreach ($rows as $row) {
                                        $content .= '<tr><td>' . Warehouse::find($wh->id)->title . '</td>';
                                        $content .= '<td>' . Good::find($row->good_id)->title . '</td>';
                                        $content .= '<td>' . $row->sum_qty . '</td>';
                                        $content .= '<td>free</td>';
                                        $content .= '<td>' . Unit::find($row->unit_id)->title . '</td>';
                                        $content .= '<td>' . $row->sum_cost . '</td>';
                                    }
                                }
                            }
                        }
                        break;
                    case 'offers':
                        $rows = TblApplication::where('good_id', $id)->orderBy('created_at', 'desc')->limit(5)->get();
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                $offer = SetOffer::where('tbl_application_id', $row->id)->first();
                                if (!empty($offer)) {
                                    $sale = Sale::find($row->application->sale_id);
                                    $content .= '<tr><td><a href="/applications/view/' . $row->application_id . '" target="_blank">'
                                        . $row->application->doc_num . '</a></td>';
                                    $content .= '<td>' . $offer->firm->name . '</td>';
                                    $content .= '<td>' . $offer->delivery_time . '</td>';
                                    $content .= '<td>' . $offer->amount . '</td>';
                                    $content .= '<td>' . $sale->currency->title . '</td>';
                                    $content .= '<td>' . $offer->comment . '</td></tr>';
                                }
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
            $firms = Firm::all();
            $firmsel = array();
            foreach ($firms as $val) {
                $firmsel[$val->id] = $val->name;
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
                'firmsel' => $firmsel,
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
                'state' => 'required|integer',
                'destination' => 'required|string|max:150',
                'contact' => 'nullable|string|max:100',
                'to_door' => 'nullable|integer',
                'delivery_in_price' => 'nullable|integer',
                'user_id' => 'required|integer',
                'date_agreement' => 'nullable|date',
                'has_vat' => 'nullable|integer',
                'price_type' => 'required|in:retail,wholesale,small',
                'doc_num_firm' => 'nullable|string|max:15',
                'date_firm' => 'nullable|date',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $sale->fill($input);
            if ($sale->update()) {
                //если есть связанный наряд на сборку - обновляем его статус
                $shipment = Shipment::where('sale_id', $sale->id)->first();
                if (!empty($shipment) && ($sale->state > 1)) {
                    $shipment->stage = $sale->state - 1;
                    $shipment->update();
                }
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
            $model->sub_good_id = $model->good_id;
            $model->created_at = date('Y-m-d H:i:s');
            $model->reserved = 0;
            if ($model->save()) {
                $msg = 'Добавлена новая позиция ' . $model->good->title . ' к заказу клиента №' . $model->sale->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $content = '<tr id="' . $model->id . '">
                    <td>' . $model->good->vendor_code . '</td>
                    <td>' . $model->sub_good->vendor_code . '</td>
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
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (empty($input['sale_id']))
                return 'NO';
            $sale = Sale::find($input['sale_id']);
            if (!User::hasRole('admin') || !User::isAuthor($sale->user_id)) {//вызываем event
                return 'BAD';
            }
            $rows = TblSale::where('sale_id', $sale->id)->get();
            if (!empty($rows)) {
                //определяем склад
                $whs_id = $sale->warehouse_id;
                foreach ($rows as $row) {
                    $need_qty = $row->qty;
                    if ($row->qty > $row->reserved) { //нужно резервировать товар?
                        //определяем сумму необходимого резерва
                        $need_qty -= $row->reserved_qty;
                        //смотрим остатки на складе
                        if($row->good_id == $row->sub_good_id)
                            $free_qty = $this->getFreeQty($whs_id, $row->good_id);
                        else
                            $free_qty = $this->getFreeQty($whs_id, $row->sub_good_id);
                        if ($need_qty && $free_qty) { //нужен резерв и есть остатки на складе
                            //резервируем товар
                            if($row->good_id == $row->sub_good_id)
                                $leftovers = Stock::where(['warehouse_id' => $whs_id, 'good_id' => $row->good_id])->get();
                            else
                                $leftovers = Stock::where(['warehouse_id' => $whs_id, 'good_id' => $row->sub_good_id])->get();
                            if (!empty($leftovers)) {
                                foreach ($leftovers as $stock) {
                                    if (!$stock->location->out_lock) { //ячейка не заблокирована на выход
                                        $reserv = new Reservation();
                                        $location = Location::find($stock->location_id);
                                        if ($stock->qty >= $need_qty) {
                                            $reserv->warehouse_id = $whs_id;
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
                                            $reserv->warehouse_id = $whs_id;
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
                return $this->getTable($sale->id);
            }
        }
        return 'NO';
    }

    public
    function dropReserv(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (empty($input['sale_id']))
                return 'NO';
            $sale = Sale::find($input['sale_id']);
            if (!User::hasRole('admin') || !User::isAuthor($sale->user_id)) {//вызываем event
                return 'BAD';
            }
            $rows = TblSale::where('sale_id', $sale->id)->get();
            if (!empty($rows)) {
                //определяем склад
                $whs_id = $sale->warehouse_id;
                foreach ($rows as $row) {
                    //ищем резервирование позиции
                    $reserv = Reservation::where('tbl_sale_id', $row->id)->get();
                    if (!empty($reserv)) {
                        //снимаем резервы
                        foreach ($reserv as $pos) {
                            //товар в ячейке на складе еще есть?
                            if($row->good_id == $row->sub_good_id)
                                $stock = Stock::where(['warehouse_id' => $whs_id, 'location_id' => $pos->location_id, 'good_id' => $row->good_id])->get();
                            else
                                $stock = Stock::where(['warehouse_id' => $whs_id, 'location_id' => $pos->location_id, 'good_id' => $row->sub_good_id])->get();
                            if (empty($stock)) {
                                $stock = new Stock();
                                $stock->warehouse_id = $pos->warehouse_id;
                                $stock->good_id = $row->sub_good_id;
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
            return $this->getTable($sale->id);
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
                            $tbl->tbl_sale_id = $row->id;
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

    public function delete(Request $request)
    {
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
                if ($sale->delete()) {
                    $msg = 'Удалена заявка клиента № ' . $sale->doc_num;
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                }
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public function delSale(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $sale = Sale::find($id);
            if (!empty($sale)) {
                if (!User::hasRole('admin') || !User::isAuthor($sale->user_id)) {//вызываем event
                    return 'BAD';
                }
                if ($sale->delete()) {
                    $msg = 'Удалена заявка клиента № ' . $sale->doc_num;
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                }
                return 'OK';
            }
        }
        return 'NO';
    }

    public function docList(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $sales = Sale::where('state', '<', 3)->orderBy('created_at', 'desc')->get();
            $content = '';
            if (!empty($sales)) {
                foreach ($sales as $row) {
                    $content .= '<tr id="sale' . $row->id . '" class="row_clicable sale-pos" style="cursor: pointer;">
                                    <td>' . $row->doc_num . '</td>
                                    <td>' . $row->created_at . '</td>
                                    <td>' . $row->amount . '</td>
                                    <td>' . $row->firm->title . '</td>
                                    <td>' . $row->status . '</td>
                                    <td>' . $row->date_agreement . '</td>
                                    <td>%</td>
                                    <td>%</td>
                                    <td>' . $row->currency->title . '</td>
                                    <td>' . $row->user->name . '</td>
                                    <td style="width:100px;">
                                        <div class="form-group" role="group">
                                            <a href="/sales/view/' . $row->id . '" target="_blank">
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

    public function docTable(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            return $this->getTable($input['sale_id']);
        }
        return 'NO';
    }

    public function priceUpdate(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $sale = Sale::find($input['sale_id']);
            if (!User::hasRole('admin') || !User::isAuthor($sale->user_id)) {//вызываем event
                return 'BAD';
            }
            //ищем ценовое правило
            $rule = PricingRule::where(['agreement_id' => $sale->agreement_id, 'price_type' => $sale->price_type])->first();
            $rows = TblSale::where('sale_id', $sale->id)->get();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    if (!empty($rule)) {
                        $row->ratio = $rule->ratio;
                        $row->update();
                    }
                }
                return $this->getTable($sale->id);
            }
        }
        return 'NO';
    }

    private function getTable($id)
    {
        $rows = TblSale::where('sale_id', $id)->get();
        if (!empty($rows)) {
            $content = '';
            foreach ($rows as $row) {
                $content .= '<tr id="' . $row->id . '">
                    <td>' . $row->good->vendor_code . '</td>';
                if ($row->good->vendor_code == $row->sub_good->vendor_code)
                    $content .= '<td>Оригинал</td>';
                else
                    $content .= '<td>' . $row->sub_good->vendor_code . '</td>';
                $content .= '<td>' . $row->good->title . '</td>
                    <td>' . $row->comment . '</td>
                    <td>' . $row->qty . '</td>
                    <td>' . $row->reserved . '</td>
                    <td>' . $row->unit->title . '</td>
                    <td>' . $row->price * $row->ratio . '</td>
                    <td>' . $row->amount . '</td>
                    <td>' . $row->vat . '</td>
                    <td>' . $row->vat_amount . '</td>
                    <td style="width:70px;">    <div class="form-group" role="group">';
                $content .= '<button class="btn btn-danger btn-sm pos_delete" type="button" title="Удалить позицию">
                            <i class="fa fa-trash fa-lg" aria-hidden="true"></i></button>
                        </div>
                    </td>
                </tr>';
            }
            $model = TblSale::where('sale_id', $id)->first();
            $amount = $model->sale->amount + $model->sale->vat_amount;
            $num = TblSale::where('sale_id', $model->sale_id)->count('id');
            $result = ['content' => $content, 'num' => $num, 'amount' => $amount];
            return json_encode($result);
        }
    }

    public function getInvoice($id)
    {
        $html = '<html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

            <style type="text/css">
                * {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 14px;
                    line-height: 14px;
                }
                table {
                    margin: 0 0 15px 0;
                    width: 100%;
                    border-collapse: collapse;
                    border-spacing: 0;
                }
                table td {
                    padding: 5px;
                }
                table th {
                    padding: 5px;
                    font-weight: bold;
                }

                .header {
                    margin: 0 0 0 0;
                    padding: 0 0 15px 0;
                    font-size: 12px;
                    line-height: 12px;
                    text-align: center;
                }

                /* Реквизиты банка */
                .details td {
                    padding: 3px 2px;
                    border: 1px solid #000000;
                    font-size: 12px;
                    line-height: 12px;
                    vertical-align: top;
                }

                h1 {
                    margin: 0 0 10px 0;
                    padding: 10px 0 10px 0;
                    border-bottom: 2px solid #000;
                    font-weight: bold;
                    font-size: 20px;
                }

                /* Поставщик/Покупатель */
                .contract th {
                    padding: 3px 0;
                    vertical-align: top;
                    text-align: left;
                    font-size: 13px;
                    line-height: 15px;
                }
                .contract td {
                    padding: 3px 0;
                }

                /* Наименование товара, работ, услуг */
                .list thead, .list tbody  {
                    border: 2px solid #000;
                }
                .list thead th {
                    padding: 4px 0;
                    border: 1px solid #000;
                    vertical-align: middle;
                    text-align: center;
                }
                .list tbody td {
                    padding: 0 2px;
                    border: 1px solid #000;
                    vertical-align: middle;
                    font-size: 11px;
                    line-height: 13px;
                }
                .list tfoot th {
                    padding: 3px 2px;
                    border: none;
                    text-align: right;
                }

                /* Сумма */
                .total {
                    margin: 0 0 20px 0;
                    padding: 0 0 10px 0;
                    border-bottom: 2px solid #000;
                }
                .total p {
                    margin: 0;
                    padding: 0;
                }

                /* Руководитель, бухгалтер */
                .sign {
                    position: relative;
                }
                .sign table {
                    width: 60%;
                }
                .sign th {
                    padding: 40px 0 0 0;
                    text-align: left;
                }
                .sign td {
                    padding: 40px 0 0 0;
                    border-bottom: 1px solid #000;
                    text-align: right;
                    font-size: 12px;
                }

                .sign-1 {
                    position: absolute;
                    left: 149px;
                    top: -44px;
                }
                .sign-2 {
                    position: absolute;
                    left: 149px;
                    top: 0;
                }
                .printing {
                    position: absolute;
                    left: 271px;
                    top: -15px;
                }
            </style>
        </head>
        <body>
            <p class="header">
                Внимание! Оплата данного счета означает согласие с условиями поставки товара.
                Уведомление об оплате обязательно, в противном случае не гарантируется наличие
                товара на складе. Товар отпускается по факту прихода денег на р/с Поставщика.
            </p>';
        $sale = Sale::find($id);
        $org = Organisation::find($sale->organisation_id);
        $firm = Firm::find($sale->firm_id);
        $contact = Contact::where('firm_id', $firm->id)->first();
        $html .= '<table class="details">
                    <tbody>
                        <tr>
                            <td colspan="2" style="border-bottom: none;">Банк организации</td>
                            <td>БИК</td>
                            <td style="border-bottom: none;">000000000</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-top: none; font-size: 10px;">Банк получателя</td>
                            <td>Р/счет №</td>
                            <td style="border-top: none;">00000000000000000000</td>
                        </tr>
                        <tr>
                            <td width="25%">ИНН ' . $org->inn . '</td>
                            <td width="30%">КПП ' . $org->kpp . '</td>
                            <td width="10%" rowspan="3">К/счет №</td>
                            <td width="35%" rowspan="3">00000000000000000000</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-bottom: none;">' . $org->print_name . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-top: none; font-size: 10px;">Получатель</td>
                        </tr>
                    </tbody>
                </table>
                <h1>Счет на оплату ' . $sale->doc_num . ' от ' . date('d-m-Y') . '</h1>
	            <table class="contract">
		<tbody>
			<tr>
				<td width="15%">Поставщик:</td>
				<th width="85%">
					' . $org->legal_address . '
				</th>
			</tr>
			<tr>
				<td>Покупатель:</td>
				<th>
					' . $contact->legal_address . '
				</th>
			</tr>
		</tbody>
	</table>
	            <table class="list">
		<thead>
			<tr>
				<th width="5%">№</th>
				<th width="54%">Наименование товара, работ, услуг</th>
				<th width="8%">Коли-<br>чество</th>
				<th width="5%">Ед.<br>изм.</th>
				<th width="14%">Цена</th>
				<th width="14%">Сумма</th>
			</tr>
		</thead>
		<tbody>';

        $total = $nds = 0;
        $prods = TblSale::where('sale_id', $sale->id)->get();
        if (!empty($prods)) {
            foreach ($prods as $i => $row) {
                $total += $row->amount;
                $nds += $row->vat_amount;

                $html .= '
			<tr>
				<td align="center">' . (++$i) . '</td>
				<td align="left">' . $row->good->title . '</td>
				<td align="right">' . $row->qty . '</td>
				<td align="left">' . $row->unit->title . '</td>
				<td align="right">' . $this->format_price($row->price * $row->ratio) . '</td>
				<td align="right">' . $this->format_price($row->amount) . '</td>
			</tr>';
            }
        }
        $html .= '</tbody>
            <tfoot>
                <tr>
                    <th colspan="5">Итого:</th>
                    <th>' . $this->format_price($total) . '</th>
                </tr>
                <tr>
                    <th colspan="5">В том числе НДС:</th>
                    <th>' . ((empty($nds)) ? '-' : $this->format_price($nds)) . '</th>
                </tr>
                <tr>
                    <th colspan="5">Всего к оплате:</th>
                    <th>' . $this->format_price($total) . '</th>
                </tr>

            </tfoot>
        </table>
        <div class="total">
            <p>Всего наименований ' . count($prods) . ', на сумму ' . $this->format_price($total) . ' руб.</p>
            <p><strong>' . $this->str_price($total) . '</strong></p>
        </div>
	    <div class="sign">
		<img class="sign-1" src="sign-1.png">
		<img class="sign-2" src="sign-2.png">
		<img class="printing" src="printing.png">

		<table>
			<tbody>
				<tr>
					<th width="30%">Руководитель</th>
					<td width="70%">Иванов А.А.</td>
				</tr>
				<tr>
					<th>Бухгалтер</th>
					<td>Сидоров Б.Б.</td>
				</tr>
			</tbody>
		</table>
	</div>
        </body>
        </html>';
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('invoice'); //todo сделать сохранение\обновление файла на диск
    }

    // Форматирование цен.
    private function format_price($value)
    {
        return number_format($value, 2, ',', ' ');
    }

    // Сумма прописью.
    private function str_price($value)
    {
        $value = explode('.', number_format($value, 2, '.', ''));

        //$f = new \NumberFormatter('ru', \NumberFormatter::SPELLOUT);
        $str = number_format($value[0]);//$f->format($value[0]);

        // Первую букву в верхний регистр.
        $str = mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));

        // Склонение слова "рубль".
        $num = $value[0] % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1:
                $rub = 'рубль';
                break;
            case 2:
            case 3:
            case 4:
                $rub = 'рубля';
                break;
            default:
                $rub = 'рублей';
        }

        return $str . ' ' . $rub . ' ' . $value[1] . ' копеек.';
    }
}
