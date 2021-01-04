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
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Order;
use Modules\Workflow\Entities\Purchase;
use Modules\Workflow\Entities\TblOrder;
use Modules\Workflow\Entities\TblPurchase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('workflow::purchases')) {
            $rows = Purchase::all();
            $data = [
                'title' => 'Приобретение товаров и услуг',
                'head' => 'Приобретение товаров и услуг',
                'rows' => $rows,
            ];

            return view('workflow::purchases', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            $msg = 'Попытка создания нового документа "Приобретение товаров и услуг"!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|unique:purchases|string|max:15',
                'firm_id' => 'required|integer',
                'statuse_id' => 'required|integer',
                'finish' => 'nullable|date',
                'currency_id' => 'required|integer',
                'hoperation_id' => 'required|integer',
                'organisation_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'warehouse_id' => 'required|integer',
                'user_id' => 'required|integer',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $purchase = new Purchase();
            $purchase->fill($input);
            $purchase->user_id = $input['user_id'];
            $purchase->created_at = date('Y-m-d H:i:s');
            if ($purchase->save()) {
                $msg = 'Документ "Приобретение товаров и услуг" № ' . $input['doc_num'] . ' был успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/purchases')->with('status', $msg);
            }
        }
        if (view()->exists('workflow::purchase_add')) {
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
            $doc_num = LibController::GenNumberDoc('purchases');
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Приобретение товаров и услуг',
                'head' => 'Новый документ',
                'statsel' => $statsel,
                'usel' => $usel,
                'cursel' => $cursel,
                'hopsel' => $hopsel,
                'orgsel' => $orgsel,
                'doc_num' => $doc_num,
                'wxsel' => $wxsel,
                'unsel' => $unsel,
            ];
            return view('workflow::purchase_add', $data);
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
        if (view()->exists('workflow::purchase_view')) {
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
            $purchase = Purchase::find($id);
            $contracts = Contract::select('id', 'title')->where(['firm_id' => $purchase->firm_id, 'organisation_id' => $purchase->organisation_id])->get();
            $contsel = array();
            foreach ($contracts as $val) {
                $contsel[$val->id] = $val->title;
            }
            $rows = TblPurchase::where('purchase_id', $id)->get();

            $data = [
                'title' => 'Приобретение товаров и услуг',
                'head' => 'Приобретение товаров и услуг № ' . $purchase->doc_num,
                'statsel' => $statsel,
                'usel' => $usel,
                'cursel' => $cursel,
                'hopsel' => $hopsel,
                'orgsel' => $orgsel,
                'wxsel' => $wxsel,
                'unsel' => $unsel,
                'purchase' => $purchase,
                'contsel' => $contsel,
                'rows' => $rows,
            ];
            return view('workflow::purchase_view', $data);
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
        $purchase = Purchase::find($id);
        if (!Role::granted('purchases')) {//вызываем event
            $msg = 'Попытка редактирования приобретения №' . $purchase->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование документов приобретений товаров!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                //'doc_num' => 'required|string|max:15',
                'firm_id' => 'required|integer',
                'statuse_id' => 'required|integer',
                'finish' => 'nullable|date',
                'currency_id' => 'required|integer',
                'hoperation_id' => 'required|integer',
                'organisation_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'warehouse_id' => 'required|integer',
                'user_id' => 'required|integer',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $purchase->fill($input);
            if ($purchase->update()) {
                $msg = 'Данные приобретения товаров и услуг № ' . $purchase->doc_num . ' были успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public function findByOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            $firm = Firm::where('name', $input['firm'])->first();
            if (!empty($firm->id)) {
                $orders = Order::where('firm_id', $firm->id)->get();
                if (!empty($orders)) {
                    foreach ($orders as $order) {
                        if ($order->statuse->title == 'Закрыт') continue;
                        if ($order->free_pos) {
                            $content .= '<option value="' . $order->id . '">' . $order->doc_num . '</option>' . PHP_EOL;
                        }
                    }
                }
            }
            return $content;
        }
    }

    public function findByAnalog(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            $goods = Good::where('catalog_num', $input['analog_code'])->get();
            if (!empty($goods)) {
                foreach ($goods as $good) {
                    $content .= '<option value="' . $good->id . '">' . $good->title . ' (' . $good->vendor_code . ')</option>' . PHP_EOL;
                }
            }
            return $content;
        }
    }

    public function getOrderPos(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $order = Order::where(['doc_num' => $input['by_order']])->first();
            //$purchase_id = $input['id_doc'];
            if ($order->statuse->title != 'Закрыт' && $order->free_pos) {
                $content = '';
                //$pos = DB::select("SELECT * FROM tbl_orders WHERE order_id=$order->id AND good_id NOT IN (SELECT good_id FROM tbl_purchases WHERE order_id = $order->id)");
                $pos = TblOrder::where('order_id', $order->id)->get();
                if (!empty($pos)) {
                    foreach ($pos as $row) {
                        $free_qty = $this->getFreeQty($order->id, $row->good_id);
                        if ($free_qty > 0) {
                            $good = Good::find($row->good_id);
                            $title = $good->title . ' (' . $good->vendor_code . ') - ' . $free_qty;
                            $content .= '<option value="' . $row->id . '">' . $title . '</option>' . PHP_EOL;
                        }
                    }
                    return $content;
                }
                return 'NO';
            }
            return 'LOCK';
        }
    }

    public function addPosition(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            $pos_id = $input['order_pos'];
            $doc_id = $input['purchase_id'];
            $content = '';
            $amount = 0;
            if (!empty($pos_id) && !empty($doc_id)) {
                foreach ($pos_id as $id) {
                    $pos = TblOrder::find($id);
                    if (!empty($pos)) {
                        $new = new TblPurchase();
                        $new->purchase_id = $doc_id;
                        $new->good_id = $pos->good_id;
                        $new->qty = $this->getFreeQty($pos->order_id, $pos->good_id);
                        $new->unit_id = $pos->unit_id;
                        $new->price1 = $pos->price;
                        $new->price2 = $pos->price;
                        $new->vat = $pos->vat;
                        $new->order_id = $pos->order_id;
                        if ($new->save()) {
                            $msg = 'Добавлена новая позиция ' . $new->good->title . ' к приобретению товаров и услуг №' . $new->purchase->doc_num;
                            //вызываем event
                            event(new AddEventLogs('info', Auth::id(), $msg));
                            $content .= '<tr id="' . $new->id . '">
                                        <td>' . $new->good->vendor_code . '</td>
                                        <td>' . $new->good->title . '</td>
                                        <td>' . $pos->comment . '</td>
                                        <td>' . $new->qty . '</td>
                                        <td>' . $new->unit->title . '</td>
                                        <td>' . $new->price1 . '</td>
                                        <td>' . $new->price2 . '</td>
                                        <td>' . $new->amount . '</td>
                                        <td><a href="/orders/view/' . $new->order->id . '" target="_blank">' . $new->order->doc_num . ' от ' . $new->order->created_at . '</a></td>
                                        <td style="width:100px;">
                                                        <div class="form-group" role="group">
                                                            <button class="btn btn-info btn-sm pos_edit"
                                                                    type="button" title="Редактировать позицию"><i
                                                                    class="fa fa-edit fa-lg" aria-hidden="true"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm pos_delete"
                                                                    type="button" title="Удалить позицию"><i
                                                                    class="fa fa-trash fa-lg" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                    </tr>';
                            $amount = $new->purchase->amount + $new->purchase->vat_amount;
                        }
                    }
                }
            }
            $num = TblPurchase::where('purchase_id', $doc_id)->count('id');
            $result = ['content' => $content, 'num' => $num, 'amount' => $amount];
            return json_encode($result);
        }
        return 'NO';
    }

    public function delPosition(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $pos = TblPurchase::find($input['id']);
            $purchase_id = $pos->purchase_id;
            if (!empty($pos)) {
                $pos->delete();
                $msg = 'Удалена позиция ' . $pos->good->title . ' из приобретения товаров и услуг №' . $pos->purchase->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $doc = Purchase::find($purchase_id);
                $amount = $doc->amount + $doc->vat_amount;
                $num = TblPurchase::where('purchase_id', $purchase_id)->count('id');
                $result = ['num' => $num, 'amount' => $amount];
                return json_encode($result);
            }
        }
        return 'NO';
    }

    public function PosEdit(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token','analog_code'); //параметр _token нам не нужен
            $pos = TblPurchase::find($input['pos_id']);
            if (!empty($pos)) {
                unset($input['pos_id']);
                $pos->fill($input);
                if ($pos->update()) {
                    $doc = Purchase::find($pos->purchase_id);
                    //обновляем связанную запись
                    if(!empty($input['sub_good_id'])){
                        $tbl = TblOrder::where(['order_id'=>$pos->order_id,'good_id'=>$pos->good_id])->first();
                        $tbl->sub_good_id = $pos->sub_good_id;
                        $tbl->update();
                        $amount = $doc->amount + $doc->vat_amount;
                        $num = TblPurchase::where('purchase_id', $pos->purchase_id)->count('id');
                        $result = ['vat_amount' => $pos->vat_amount, 'sum' => $pos->amount, 'num' => $num, 'amount' => $amount,
                            'unit' => $pos->unit->title,'code'=>$pos->sub_good->vendor_code,'title'=>$pos->sub_good->title];
                    }
                    else{
                        $amount = $doc->amount + $doc->vat_amount;
                        $num = TblPurchase::where('purchase_id', $pos->purchase_id)->count('id');
                        $result = ['vat_amount' => $pos->vat_amount, 'sum' => $pos->amount, 'num' => $num, 'amount' => $amount,
                            'unit' => $pos->unit->title,'code'=>'Оригинал','title'=>$pos->good->title];
                    }
                    return json_encode($result);
                }
                return 'ERR';
            }
        }
        return 'NO';
    }

    public function delete(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            $msg = 'Попытка удаления документа Приобретение товаров и услуг №' . $request['purchase_id']->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на удаление таких докуметов!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['purchase_id'];
            $purchase = Purchase::find($id);
            if (!empty($purchase)) {
                $msg = 'Документ "Приобретение товаров и услуг" № ' . $purchase->doc_num . ' был удален.';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $purchase->delete();
                return redirect()->back()->with('status', $msg);
            }
        }
        return 'NO';
    }

    public function download(Request $request)
    {
        if (!Role::granted('import') && !Role::granted('purchases')) {
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
            $num = 1;
            $rows = 0;
            $err = '';
            $multi = 0;
            $lock = Statuse::where('title', 'Закрыт')->first()->id;
            $purchase_id = $request['doc_id'];
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 1; $i < $rows; $i++) {
                    $row = $table[$i];
                    $good_id = null;
                    //смотрим заполнение номера документа в текущей строке
                    if (!empty($row[4])) {
                        //смотрим есть ли такой документ и не закрыт ли он
                        $order = Order::where('doc_num', trim($row[4]))->where('statuse_id', '!=', $lock)->first();
                        if (empty($order)) {
                            $err .= 'стр. №' . $num . ' - Заказ поставщику №' . $row[4] . ' отсутствует в базе или имеет статус "Закрыт"!' . PHP_EOL;
                        } else {
                            //смотрим заполнено ли поле артикула, аналога или каталожный номер
                            $vendor_code = trim($row[0]);
                            $analog_code = trim($row[1]);
                            if (!empty($vendor_code)) {
                                $good = Good::where('vendor_code', $vendor_code)->first();
                            } elseif (!empty($analog_code)) {
                                $good = Good::where('analog_code', $analog_code)->first();
                            }
                            if (!empty($good)) {
                                $good_id = $good->id;
                                //смотрим, присутствует ли эта номенклатура в заказе
                                $pos = TblOrder::where(['order_id' => $order->id, 'good_id' => $good_id])->first();
                                if (empty($pos)) {
                                    $err .= 'стр. №' . $num . ' - Номенклатура ' . $pos->good->title . ' отсутствует в заказе поставщику №' . $row[4] . '!' . PHP_EOL;
                                    $num++;
                                    continue;
                                }
                                //смотрим не забрали ли уже эту номенклатуру в другое приобретение из этого заказа
                                $free = TblPurchase::where(['good_id' => $good_id, 'order_id' => $order->id])->first();
                                if (empty($free)) {
                                    $price = str_replace(',', '.', trim($row[3]));
                                    $qty = trim($row[2]);
                                    $vat = 0;
                                    TblPurchase::updateOrCreate(['purchase_id' => $purchase_id, 'order_id' => $order->id, 'good_id' => $good_id],
                                        ['qty' => $qty, 'unit_id' => 1, 'price1' => $price, 'price2' => $price, 'vat' => $vat]);
                                } else {
                                    $err .= 'стр. №' . $num . ' - Номенклатура "' . $pos->good->title . '" из заказа поставщику №' . $row[4] .
                                        ' уже загружена в приобретение №' . Purchase::find($free->purchase_id)->doc_num . PHP_EOL;
                                }
                            } else {
                                $err .= 'стр. №' . $num . ' - Не определена номенклатура! Не указаны или не верно указаны: артикул, аналог или каталожный номер.' . PHP_EOL;
                            }
                        }
                    } else {
                        $err .= 'стр. №' . $num . ' - Не указан номер заказа поставщику!' . PHP_EOL;
                    }
                    $num++;
                }
                break;
            }
            $num -= 1;
            $rows -= 1;
            $result = ['rows' => $rows, 'num' => $num, 'err' => $err, 'multi' => $multi];
            return json_encode($result);
        }
        return 'ERR';
    }

    private function getFreeQty($order_id, $good_id)
    {
        //смотрим общее количество в заказе
        $order_qty = TblOrder::where(['order_id' => $order_id, 'good_id' => $good_id])->sum('qty');
        //смотрим общее количество в поступлениях
        $purchase_qty = TblPurchase::where(['order_id' => $order_id, 'good_id' => $good_id])->sum('qty');
        if ($order_qty > $purchase_qty)
            return $order_qty - $purchase_qty;
        else
            return 0;
    }
}
