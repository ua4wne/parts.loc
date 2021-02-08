<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Currency;
use App\Models\Hoperation;
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
use Modules\Warehouse\Entities\Specification;
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Order;
use Modules\Workflow\Entities\OrderError;
use Modules\Workflow\Entities\Purchase;
use Modules\Workflow\Entities\TblApplication;
use Modules\Workflow\Entities\TblDeclaration;
use Modules\Workflow\Entities\TblOrder;
use Modules\Workflow\Entities\TblPurchase;
use Modules\Workflow\Entities\TblSale;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class OrderController extends Controller
{
    public function index()
    {
        if (view()->exists('workflow::orders')) {
            $rows = Order::all();
            $data = [
                'title' => 'Заказы поставщикам',
                'head' => 'Заказы поставщикам',
                'rows' => $rows,
            ];

            return view('workflow::orders', $data);
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
            $msg = 'Попытка создания нового заказа поставщику!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $input['amount'] = 0;
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;
            if (empty($input['has_vat'])) $input['has_vat'] = 0;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|unique:orders|string|max:15',
                'amount' => 'required|numeric',
                'firm_id' => 'required|integer',
                'statuse_id' => 'required|integer',
                'finish' => 'nullable|date',
                'currency_id' => 'required|integer',
                'hoperation_id' => 'required|integer',
                'has_money' => 'nullable|integer',
                'organisation_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'warehouse_id' => 'required|integer',
                'user_id' => 'required|integer',
                'has_vat' => 'nullable|integer',
                'doc_num_firm' => 'nullable|string|max:15',
                'date_firm' => 'nullable|date',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $order = new Order();
            $order->fill($input);
            $order->user_id = $input['user_id'];
            $order->created_at = date('Y-m-d H:i:s');
            if ($order->save()) {
                $msg = 'Заказ поставщику № ' . $input['doc_num'] . ' был успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/orders')->with('status', $msg);
            }
        }
        if (view()->exists('workflow::order_add')) {
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
            $hops = Hoperation::all();
            $hopsel = array();
            foreach ($hops as $val) {
                $hopsel[$val->id] = $val->title;
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
            $doc_num = LibController::GenNumberDoc('orders');
            $vat = env('VAT');
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Заказы поставщику',
                'head' => 'Новый заказ поставщику',
                'statsel' => $statsel,
                'usel' => $usel,
                'cursel' => $cursel,
                'hopsel' => $hopsel,
                'orgsel' => $orgsel,
                'doc_num' => $doc_num,
                'wxsel' => $wxsel,
                'vat' => $vat,
                'unsel' => $unsel,
            ];
            return view('workflow::order_add', $data);
        }
        abort(404);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        if (view()->exists('workflow::order_view')) {
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
            $hops = Hoperation::all();
            $hopsel = array();
            foreach ($hops as $val) {
                $hopsel[$val->id] = $val->title;
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
            $order = Order::find($id);
            $contracts = Contract::select('id', 'title')->where(['firm_id' => $order->firm_id, 'organisation_id' => $order->organisation_id])->get();
            $contsel = array();
            foreach ($contracts as $val) {
                $contsel[$val->id] = $val->title;
            }
            $rows = TblOrder::where('order_id', $id)->get();
            $err_rows = OrderError::where('order_id', $id)->get();
            $err = OrderError::where('order_id', $id)->count('id');
            $vat = 0;
            if($order->has_vat) $vat = env('VAT');
            //цепочка связанных документов
            $links = TblPurchase::select('purchase_id')->where('order_id',$id)->distinct()->get();
            $tbody = '';
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
                }
            }
            $data = [
                'title' => 'Заказы поставщику',
                'head' => 'Заказ поставщику № ' . $order->doc_num,
                'statsel' => $statsel,
                'usel' => $usel,
                'cursel' => $cursel,
                'hopsel' => $hopsel,
                'orgsel' => $orgsel,
                'wxsel' => $wxsel,
                'unsel' => $unsel,
                'order' => $order,
                'contsel' => $contsel,
                'vat' => $vat,
                'rows' => $rows,
                'err_rows' => $err_rows,
                'err' => $err,
                'tbody' => $tbody,
            ];
            return view('workflow::order_view', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        $order = Order::find($id);
        if (!Role::granted('orders')) {//вызываем event
            $msg = 'Попытка редактирования заказа поставщику №' . $order->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование заказов поставщику!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;
            if (empty($input['has_vat'])) $input['has_vat'] = 0;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                //'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'firm_id' => 'required|integer',
                'statuse_id' => 'required|integer',
                'finish' => 'nullable|date',
                'currency_id' => 'required|integer',
                'hoperation_id' => 'required|integer',
                'has_money' => 'nullable|integer',
                'organisation_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'warehouse_id' => 'required|integer',
                'user_id' => 'required|integer',
                'has_vat' => 'nullable|integer',
                'doc_num_firm' => 'nullable|string|max:15',
                'date_firm' => 'nullable|date',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $order->fill($input);
            if ($order->update()) {
                $msg = 'Данные заказа поставщику № ' . $order->doc_num . ' были успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public function findGood(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select id, concat(title,' (',vendor_code,')') as `name` from goods where title like '%$query%'");
        return response()->json($codes);
    }

    public function ajaxData(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select id, `name` from firms where `name` like '%$query%'");
        return response()->json($codes);
    }

    public function findContract(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            if (isset($input['firm']) && isset($input['org_id'])) {
                $firm_id = Firm::where('name', $input['firm'])->first()->id;
                $contracts = Contract::where(['organisation_id' => $input['org_id'], 'status' => 1])->where('firm_id', $firm_id)->get();
                foreach ($contracts as $contract) {
                    $content .= '<option value="' . $contract->id . '">' . $contract->title . '</option>' . PHP_EOL;
                }
            }
            return $content;
        }
    }

    public function findByVendor(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            if (isset($input['vendor_code'])) {
                $good = Good::where('vendor_code', $input['vendor_code'])->first();
                $content .= '<option value="' . $good->id . '">' . $good->title . '</option>' . PHP_EOL;
                if(!empty($good->catalog_num)){
                    $analogs = Good::where('catalog_num',$good->catalog_num)->where('id','!=',$good->id)->get();
                    if(!empty($analogs)){
                        foreach ($analogs as $row){
                            $content .= '<option value="' . $row->id . '">' . $row->title . ' (' . $row->vendor_code . ')</option>' . PHP_EOL;
                        }
                    }
                }

            }
            return $content;
        }
    }

    public function findByName(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            if (isset($input['by_name'])) {
                $tmp = explode('(',$input['by_name']);
                $title = trim($tmp[0]);
                $code = str_replace(')','',$tmp[1]);
                $good = Good::where(['title'=>$title,'vendor_code'=>$code])->first();
                $content .= '<option value="' . $good->id . '">' . $good->title . '</option>' . PHP_EOL;
                if(!empty($good->catalog_num)){
                    $analogs = Good::where('catalog_num',$good->catalog_num)->where('id','!=',$good->id)->get();
                    if(!empty($analogs)){
                        foreach ($analogs as $row){
                            $content .= '<option value="' . $row->id . '">' . $row->title . ' (аналог)</option>' . PHP_EOL;
                        }
                    }
                }
            }
            return $content;
        }
    }

    public function specByVendor(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            if (isset($input['id'])) {
                $specs = Specification::where('good_id', $input['id'])->get();
                $content = '';
                foreach ($specs as $row) {
                    $content .= '<option value="' . $row->id . '">' . $row->title . '</option>' . PHP_EOL;
                }
                return $content;
            }
            return $content;
        }
    }

    public function getSpecifications(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if($input['tbl_id'] == 'order')
                $good_id = TblOrder::find($input['id'])->good_id;
            if($input['tbl_id'] == 'sale')
                $good_id = TblSale::find($input['id'])->good_id;
            $specs = Specification::where('good_id', $good_id)->get();
            $content = '';
            foreach ($specs as $row) {
                $content .= '<option value="' . $row->id . '">' . $row->title . '</option>' . PHP_EOL;
            }
            return $content;
        }
    }

    public function setSpecifications(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if($input['tbl_id'] == 'order')
                $pos = TblOrder::find($input['id']);
            if($input['tbl_id'] == 'sale')
                $pos = TblSale::find($input['id']);
            if (!empty($pos)) {
                $pos->comment = $input['title'];
                if ($pos->update())
                    return 'OK';
            }
            return 'ERR';
        }
    }

    public function addPosition(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (!empty($input['comment']))
                $input['comment'] = Specification::find($input['comment'])->title;
            $model = new TblOrder();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            $model->sub_good_id = $model->good_id;
            if ($model->save()) {
                $msg = 'Добавлена новая позиция ' . $model->good->title . ' к заказу поставщику №' . $model->order->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $content = '<tr id="' . $model->id . '">
                    <td>' . $model->good->vendor_code . '</td>
                    <td>Оригинал</td>
                    <td>' . $model->good->title . '</td>
                    <td>' . $model->comment . '</td>
                    <td>' . $model->qty . '</td>
                    <td>' . $model->unit->title . '</td>
                    <td>' . $model->price . '</td>
                    <td>' . $model->amount . '</td>
                    <td>' . $model->vat . '</td>
                    <td>' . $model->vat_amount . '</td>
                    <td></td>
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
                $amount = $model->order->amount + $model->order->vat_amount;
                $num = TblOrder::where('order_id', $model->order_id)->count('id');
                $result = ['content' => $content, 'num' => $num, 'amount' => $amount];
                return json_encode($result);
            }
        }
        return 'NO';
    }

    public function editErrPos(Request $request){
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (!empty($input['ecomment']))
                $input['comment'] = Specification::find($input['ecomment'])->title;
            unset($input['ecomment']);
            $err_id = $input['err_id'];
            unset($input['err_id']);
            $dbl = TblOrder::where(['order_id'=>$input['order_id'],'good_id'=>$input['good_id']])->first();
            if(empty($dbl)){
                $model = new TblOrder();
                $model->fill($input);
                $model->sub_good_id = $model->good_id;
                if ($model->save()) {
                    $msg = 'Добавлена новая позиция ' . $model->good->title . ' к заказу поставщику №' . $model->order->doc_num;
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                    //удаляем позицию из таблицы ошибок
                    OrderError::find($err_id)->delete();
                    $content = '<tr id="' . $model->id . '">
                    <td>' . $model->good->vendor_code . '</td>
                    <td>Оригинал</td>
                    <td>' . $model->good->title . '</td>
                    <td>' . $model->comment . '</td>
                    <td>' . $model->qty . '</td>
                    <td>' . $model->unit->title . '</td>
                    <td>' . $model->price . '</td>
                    <td>' . $model->amount . '</td>
                    <td>' . $model->vat . '</td>
                    <td>' . $model->vat_amount . '</td>
                    <td>' . $model->purchase . '</td>
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
                    $amount = $model->order->amount + $model->order->vat_amount;
                    $num = TblOrder::where('order_id', $model->order_id)->count('id');
                    $result = ['content' => $content, 'num' => $num, 'amount' => $amount];
                    return json_encode($result);
                }
            }
            else{
                //удаляем позицию из таблицы ошибок
                OrderError::find($err_id)->delete();
                return 'DBL';
            }
        }
        return 'NO';
    }

    public function delPosition(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $pos = TblOrder::find($input['id']);
            $order_id = $pos->order_id;
            //проверяем нет ли такой позиции в приобретениях товаров
            $linkpos = TblPurchase::where(['good_id'=>$pos->good_id, 'order_id'=>$order_id])->first();
            if (!empty($pos) && empty($linkpos)) {
                $pos->delete();
                $msg = 'Удалена позиция ' . $pos->good->title . ' из заказа поставщику №' . $pos->order->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $doc = Order::find($order_id);
                $amount = $doc->amount + $doc->vat_amount;
                $num = TblOrder::where('order_id', $order_id)->count('id');
                $result = ['num' => $num, 'amount' => $amount];
                return json_encode($result);
            }
            return 'LINK';
        }
        return 'NO';
    }

    public function delErrPosition(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $pos = OrderError::find($input['id']);
            if (!empty($pos)) {
                $pos->delete();
                return 'OK';
            }
        }
        return 'NO';
    }

    public function delete(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $order_id = $input['order_id'];
            $order = Order::find($order_id);
            if (!empty($order)) {
                if (!User::hasRole('admin') || !User::isAuthor($order->user_id)) {//вызываем event
                    return 'BAD';
                }
                $msg = "В процессе удаления документа произошла ошибка!";
                if($order->delete()){
                    $msg = 'Удалена заявка поставщику № ' . $order->doc_num;
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                }
                return redirect()->back()->with('status', $msg);
            }
        }
        return 'NO';
    }

    public function download(Request $request)
    {
        if (!Role::granted('import') && !Role::granted('orders')) {
            return 'NO';
        }
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 3;
            $rows = 0;
            $err = 0;
            $multi = 0;
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                $doc_num = $table[0][2];
                if (empty($doc_num)) return 'ERR'; //определяем заполнение номера документа в определенном поле в файле
                $order = Order::where('doc_num', $doc_num)->first(); //определяем существование документа
                if (empty($order)) return 'NO';
                if ($order->id) {
                    for ($i = 3; $i < $rows; $i++) {
                        $row = $table[$i];
                        if (!empty($row[0])) {
                            $good = Good::where('vendor_code', $row[0])->get();
                            $qty = $row[1];
                            $price = str_replace(',', '.', $row[2]);
                            $vat = 0;
                            if ($order->has_vat) $vat = env('VAT');
                            if(count($good)==1){
                                $pos = new TblOrder();
                                $pos->order_id = $order->id;
                                $pos->good_id = $good->id;
                                $pos->sub_good_id = $good->id;
                                $pos->qty = $qty;
                                $pos->unit_id = $good->unit_id;
                                $pos->price = $price;
                                $pos->vat = $vat;
                                if ($pos->save())
                                    $num++;
                            }
                            elseif(count($good)>1){
                                // есть такая запись или нет
                                OrderError::updateOrCreate(['order_id' => $order->id,'vendor_code'=>$row[0]],
                                    ['qty' => $qty,'unit' => 'Штука','price' => $price,'vat'=>$vat,'multi'=>1]);
                                $multi++;
                            }
                            else {
                                // есть такая запись или нет
                                OrderError::updateOrCreate(['order_id' => $order->id,'vendor_code'=>$row[0]],
                                    ['qty' => $qty,'unit' => 'Штука','price' => $price,'vat'=>$vat]);
                                $err++;
                            }
                        }
                    }
                }
                break;
            }
            $num-=3;
            $rows-=3;
            $result = ['rows' => $rows, 'num' => $num, 'err' => $err,'multi' => $multi];
            return json_encode($result);
        }
        return 'ERR';
    }

    public function newPurchase($id){
        $order = Order::find($id);
        //создаем новый документ
        if(!empty($order)){
            $doc = new Purchase();
            $doc->doc_num = LibController::GenNumberDoc('purchases');
            $doc->firm_id = $order->firm_id;
            $doc->statuse_id = Statuse::first()->id;
            $doc->currency_id = $order->currency_id;
            $doc->hoperation_id = $order->hoperation_id;
            $doc->organisation_id = $order->organisation_id;
            $doc->contract_id = $order->contract_id;
            $doc->warehouse_id = $order->warehouse_id;
            $doc->user_id = Auth::id();
            $doc->created_at = date('Y-m-d H:i:s');
            if($doc->save()){
                $tbl = new TblPurchase();
                //выбираем позиции из документа-основания, которые не привязаны ни к каким приобретениям
                $pos = DB::select("SELECT * FROM tbl_orders WHERE order_id=$id AND good_id NOT IN (SELECT good_id FROM tbl_purchases WHERE order_id = $id)");
                if (!empty($pos)) {
                    foreach ($pos as $row) {
                        $tbl->purchase_id = $doc->id;
                        $tbl->good_id = $row->good_id;
                        $tbl->qty = $row->qty;
                        $tbl->unit_id = $row->unit_id;
                        $tbl->price1 = $row->price;
                        $tbl->price2 = $row->price;
                        $tbl->vat = $row->vat;
                        $tbl->order_id = $row->order_id;
                        $tbl->save();
                    }
                }
                $msg = 'Создан новый документ приобретения товаров и услуг №' . $doc->doc_num . ' на основании заказа поставщику №' . $order->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/purchases/view/'.$doc->id)->with('status', $msg);
            }
        }
        return redirect()->back()->with('error', 'В процессе создания документа приобретения товаров и услуг произошла ошибка. Документ не был создан!');
    }

    public function newOrder(Request $request){
        if (!Role::granted('orders')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $app_id = $input['id'];
            $rows = TblApplication::where('application_id',$app_id)->distinct()->get(['firm_id']);
            if(!empty($rows)){
                foreach ($rows as $row){
                    dd($row->firm_id);
                }
            }
        }
    }
}
