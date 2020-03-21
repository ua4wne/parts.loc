<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Organisation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Inventory;
use Modules\Warehouse\Entities\InventoryTable;
use Modules\Warehouse\Entities\Stock;
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('warehouse::inventories')) {
            $rows = Inventory::paginate(env('PAGINATION_SIZE'));
            $title = 'Инвентаризации';
            $data = [
                'title' => $title,
                'head' => 'Документы инвентаризаций',
                'rows' => $rows,
            ];
            return view('warehouse::inventories', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('wh_doc')) {//вызываем event
            $msg = 'Попытка создания новой инвентаризации!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание документов инвентаризаций!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'warehouse_id' => 'required|integer',
                'reason' => 'required|string|max:150',
                'user_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('inventoryAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $doc_num = LibController::GenNumberDoc('inventories');
            //определяем организацию, к которой привязан склад
            $org_id = Warehouse::find($input['warehouse_id'])->organisation_id;
            //смотрим указан ли префикс для документов организации
            $prefix = Organisation::find($org_id)->prefix;
            if(!empty($prefix))
                $doc_num = $prefix.'-'.$doc_num;
            $inventory = new Inventory();
            $inventory->fill($input);
            $inventory->doc_num = $doc_num;
            $inventory->status = 1;
            $inventory->created_at = date('Y-m-d H:i:s');
            if ($inventory->save()) {
                $msg = 'Новый документ инвентаризации № ' . $doc_num . ' успешно создан!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('inventories')->with('status', $msg);
            }
        }
        if (view()->exists('warehouse::inventory_add')) {
            $whs = Warehouse::all();
            $whsel = array();
            foreach ($whs as $val) {
                $whsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $data = [
                'title' => 'Инвентаризации',
                'head' => 'Новый документ',
                'whsel' => $whsel,
                'usel' => $usel,
            ];
            return view('warehouse::inventory_add', $data);
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
        if (view()->exists('warehouse::inventory_view')) {
            $doc = Inventory::find($id);
            $rows = InventoryTable::where(['inventory_id' => $id])->get();
            $units = Unit::all();
            $usel = array();
            foreach ($units as $val) {
                $usel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Инвентаризации',
                'head' => 'Инвентаризация № ' . $doc->doc_num,
                'rows' => $rows,
                'id' => $id,
                'usel' => $usel,
                'status' => $doc->status,
            ];
            return view('warehouse::inventory_view', $data);
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
        $model = Inventory::find($id);
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_doc')) {
                $msg = 'Попытка удаления документа инвентаризации № ' . $model->doc_num;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление документов инвентаризаций!');
            }
            InventoryTable::where(['inventory_id' => $model->id])->delete();
            $msg = 'Документ инвентаризации № ' . $model->doc_num . ' был удален со всем своим содержимым!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('inventories')->with('status', $msg);
        }
        if (!Role::granted('wh_doc')) {
            $msg = 'Попытка редактирования документа инвентаризации № ' . $model->doc_num;
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование документов инвентаризаций!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'warehouse_id' => 'required|integer',
                'reason' => 'required|string|max:150',
                'user_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('inventoryEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if ($model->update()) {
                $msg = 'Данные документа инвентаризации № ' . $model->doc_num . ' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('inventories')->with('status', $msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::inventory_edit')) {
            $whs = Warehouse::all();
            $whsel = array();
            foreach ($whs as $val) {
                $whsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $data = [
                'title' => 'Инвентаризации',
                'head' => 'Редактирование документа',
                'whsel' => $whsel,
                'usel' => $usel,
                'data' => $old,
            ];
            return view('warehouse::inventory_edit', $data);
        }
        abort(404);
    }

    public function createPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['doc_id'];
            $doc = Inventory::find($id);
            if (!Role::granted('wh_doc')) {
                $msg = 'Попытка редактирования документа инвентаризации ' . $doc->doc_num;
                //вызываем event
                event(new AddEventLogs('access', Auth::id(), $msg));
                return 'NO';
            }
            if (isset($input['vendor_code']))
                $input['good_id'] = Good::where('vendor_code', $input['vendor_code'])->first()->id;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
                'numeric' => 'Значение поля должно быть целым или дробным числом!',
            ];
            $validator = Validator::make($input, [
                'doc_id' => 'required|integer',
                'good_id' => 'required|integer',
                'cell' => 'nullable|string|max:10',
                'qty' => 'required|integer',
                'price' => 'required|numeric',
                'unit_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return 'NO VALIDATE';
            }
            $amount = round($input['qty'] * $input['price'],2);
            // создаст или обновит запись в модели в зависимости от того
            // есть такая запись или нет
            InventoryTable::updateOrCreate(['inventory_id' => $input['doc_id'], 'good_id' => $input['good_id'],'cell' => $input['cell']],
                ['qty' => $input['qty'], 'price' => $input['price'], 'unit_id' => $input['unit_id'], 'amount' => $amount,'created_at' => date('Y-m-d H:i:s')]);

            $msg = 'Артикул ' . $input['vendor_code'] . ' успешно добавлен\обновлен в инвентаризации ' . $doc->doc_num . '!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            $row = InventoryTable::where(['inventory_id' => $input['doc_id'], 'good_id' => $input['good_id'],'cell' => $input['cell']])->first();
            $result = ['id' => $row->id, 'title' => $row->good->title, 'amount'=>$amount];
            return json_encode($result);
        }
    }

    public function editPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $pos = InventoryTable::find($id);
            $doc = Inventory::find($pos->inventory_id);
            if (!Role::granted('wh_doc')) {
                $msg = 'Попытка редактирования документа инвентаризации № ' . $doc->doc_num;
                //вызываем event
                event(new AddEventLogs('access', Auth::id(), $msg));
                return 'NO';
            }
            if($doc->status==0)
                return 'NO';
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
                'numeric' => 'Значение поля должно быть целым или дробным числом!',
            ];
            $validator = Validator::make($input, [
                'cell' => 'nullable|string|max:10',
                'qty' => 'required|integer',
                'price' => 'required|numeric',
                'unit_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return 'NO VALIDATE';
            }
            $amount = round($input['qty'] * $input['price'],2);
            $pos->cell = $input['cell'];
            $pos->qty = $input['qty'];
            $pos->price = $input['price'];
            $pos->unit_id = $input['unit_id'];
            $pos->amount = $amount;
            $pos->update();
            $msg = 'Позиция с артикулом ' . $pos->good->vendor_code . ' была обновлена в инвентаризации № ' . $doc->doc_num;
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return 'OK';
        }
        return 'ERR';
    }

    public function findPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = InventoryTable::find($id)->toArray();
            return json_encode($model);
        }
    }

    public function delPosition(Request $request)
    {
        if (!Role::granted('wh_doc')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = InventoryTable::find($id);
            $status = $model->inventory->status;
            if($status==0)
                return 'NOT';
            if ($model->delete())
                return 'OK';
            else
                return 'ERR';
        }
    }

    public function download(Request $request)
    {
        if (!Role::granted('import') && !Role::granted('wh_doc')) {
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
            $num = 0;
            $rows = 0;
            $doc_num = $tables[0][0][0]; //A1 - номер документа, брать из БД
            $doc_id = Inventory::where(['doc_num'=>$doc_num])->first()->id;
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 2; $i < $rows; $i++) {
                    $row = $table[$i];
                    if(!empty($row[1])){
                        //$vendor = trim($row[1]);
                        $vendor = $row[1];
                        $price = $row[6];
                        if(!is_numeric($price))
                            $price = 0;
                        $qty = $row[7];
                        if(!is_numeric($qty))
                            $qty = 0;
                        if ($qty > 0) {
                            $good = Good::where(['vendor_code'=>$vendor])->first();
                            if (!empty($good)) {
                                $amount = round($qty * $price,2);
                                // создаст или обновит запись в модели $good в зависимости от того
                                // есть такая запись или нет
                                InventoryTable::updateOrCreate(['inventory_id' => $doc_id, 'good_id' => $good->id, 'cell' => null], ['qty' => $qty,
                                    'price' => $price, 'unit_id' => 1, 'amount' => $amount, 'created_at' => date('Y-m-d H:i:s')]);
                                $num++;
                            }
                        }
                    }
                }
                break;
            }
            $msg = 'Данные инвентаризации №' . $doc_num . ' были загружены из файла '.$path;
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            $result = ['rows' => $rows, 'num' => $num];
            return json_encode($result);
        }
        return 'ERR';
    }

    public function writeToStock(Request $request){
        if (!Role::granted('wh_doc')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $doc = Inventory::find($id);
            $warehouse_id = $doc->warehouse_id;
            $rows = InventoryTable::where(['inventory_id'=>$id])->get();
            if(!empty($rows)){
                foreach ($rows as $row){
                    Stock::updateOrCreate(['warehouse_id' => $warehouse_id, 'good_id' => $row->good_id, 'cell' => $row->cell], ['qty' => $row->qty,
                        'unit_id' => $row->unit_id, 'cost' => $row->amount, 'created_at' => date('Y-m-d H:i:s')]);
                }
                $doc->status = 0;
                $doc->update();
                return 'OK';
            }
        }
        return 'ERR';
    }
}
