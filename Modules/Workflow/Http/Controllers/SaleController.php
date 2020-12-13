<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Currency;
use App\Models\Delivery;
use App\Models\DeliveryMethod;
use App\Models\Organisation;
use App\Models\Statuse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Workflow\Entities\Agreement;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Sale;
use Modules\Workflow\Entities\TblSale;
use Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!User::hasRole('admin') && !User::hasRole('manager') && !User::hasRole('director')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр данных!');
        }
        if (view()->exists('workflow::sales_area')) {
            $title = 'Рабочее место менеджера';
            $rows = Good::offset(0)->limit(10)->get();
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
            ];
            return view('workflow::sales_area', $data);
        }
        abort(404);
    }

    public function orders(){
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
                'contact' => 'required|string|max:100',
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
                    if(!empty($good->catalog_num)){
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
            //$err_rows = OrderError::where('sale_id', $id)->get();
            //$err = OrderError::where('sale_id', $id)->count('id');
            $vat = 0;
            if($sale->has_vat) $vat = env('VAT');
            $tbody = '';
            //цепочка связанных документов
            /*$links = TblPurchase::select('purchase_id')->where('order_id',$id)->distinct()->get();
            if(!empty($links)){
                foreach ($links as $row){
                    $tbody .= '<tr><td class="text-bold"><a href="/purchases/view/'.$row->purchase->id.'" target="_blank">
                    Приобретение товаров и услуг №' . $row->purchase->doc_num . '</a></td>';
                    if(isset($row->purchase->statuse_id)){
                        $tbody .= '<td>' . $row->purchase->statuse->title . '</td>';
                    }
                    else{
                        $tbody .= '<td></td>';
                    }
                    $tbody .= '<td>'
                        . $row->purchase->created_at . '</td><td>' . $row->purchase->user->name . '</td></tr>';
                }
                foreach ($links as $row){
                    $decl = TblDeclaration::select('declaration_id')->where('purchase_id',$row->purchase->id)->first();
                    if(!empty($decl)){
                        $tbody .= '<tr><td class="text-bold"><a href="/declarations/view/'.$decl->declaration->id.'" target="_blank">
                    Таможенная декларация на импорт №' . $decl->declaration->doc_num . '</a></td>';
                        if(isset($decl->declaration->statuse_id)){
                            $tbody .= '<td>' . $decl->declaration->statuse->title . '</td>';
                        }
                        else{
                            $tbody .= '<td></td>';
                        }
                        $tbody .= '<td>'
                            . $decl->declaration->created_at . '</td><td>' . $decl->declaration->user->name . '</td></tr>';
                    }
                }*/
            //}
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
    public function edit($id)
    {
        return view('workflow::edit');
    }

    public function getSale(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select id, `doc_num` as `name` from sales where `doc_num` like '%$query%'");
        return response()->json($codes);
    }
}
