<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Organisation;
use App\Models\Price;
use App\Models\PriceTable;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Stock;
use Modules\Warehouse\Entities\TblWhCorrect;
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Warehouse\Entities\WhCorrect;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class WhCorrectController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('warehouse::wh_corrects')) {
            $rows = WhCorrect::paginate(env('PAGINATION_SIZE'));
            $title = 'Корректировки остатков';
            $data = [
                'title' => $title,
                'head' => 'Документы корректировок остатков',
                'rows' => $rows,
            ];
            return view('warehouse::wh_corrects', $data);
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
            $msg = 'Попытка создания новой корректировки остатков!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание документов корректировок остатков!');
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
                'price_id' => 'required|integer',
                'reason' => 'required|string|max:150',
                'user_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('wh_correctsAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $doc_num = LibController::GenNumberDoc('wh_corrects');
            //определяем организацию, к которой привязан склад
            $org_id = Warehouse::find($input['warehouse_id'])->organisation_id;
            //смотрим указан ли префикс для документов организации
            $prefix = Organisation::find($org_id)->prefix;
            if(!empty($prefix))
                $doc_num = $prefix.'-'.$doc_num;
            $wh_correct = new WhCorrect();
            $wh_correct->fill($input);
            $wh_correct->doc_num = $doc_num;
            $wh_correct->status = 1;
            $wh_correct->created_at = date('Y-m-d H:i:s');
            if ($wh_correct->save()) {
                $msg = 'Новый документ корректировки остатков № ' . $doc_num . ' успешно создан!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('wh_corrects')->with('status', $msg);
            }
        }
        if (view()->exists('warehouse::whcorrects_add')) {
            $whs = Warehouse::all();
            $whsel = array();
            foreach ($whs as $val) {
                $whsel[$val->id] = $val->title;
            }
            $prices = Price::all();
            $psel = array();
            foreach ($prices as $val) {
                $psel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $data = [
                'title' => 'Корректировка остатков',
                'head' => 'Новый документ',
                'whsel' => $whsel,
                'psel' => $psel,
                'usel' => $usel,
            ];
            return view('warehouse::whcorrects_add', $data);
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
        if (view()->exists('warehouse::whcorrect_view')) {
            $doc = WhCorrect::find($id);
            $rows = TblWhCorrect::where(['wh_correct_id' => $id])->get();
            $units = Unit::all();
            $usel = array();
            foreach ($units as $val) {
                $usel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Корректировка остатков',
                'head' => 'Корректировка остатков № ' . $doc->doc_num,
                'rows' => $rows,
                'id' => $id,
                'usel' => $usel,
                'status' => $doc->status,
            ];
            return view('warehouse::whcorrect_view', $data);
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
        $model = WhCorrect::find($id);
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_doc')) {
                $msg = 'Попытка удаления документа корректировки остатков № ' . $model->doc_num;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление документов корректировки остатков!');
            }
            TblWhCorrect::where(['wh_correct_id' => $model->id])->delete();
            $msg = 'Документ корректировки остатков № ' . $model->doc_num . ' был удален со всем своим содержимым!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('wh_corrects')->with('status', $msg);
        }
        if (!Role::granted('wh_doc')) {
            $msg = 'Попытка редактирования документа корректировки остатков № ' . $model->doc_num;
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование документов корректировки остатков!');
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
                'price_id' => 'required|integer',
                'reason' => 'required|string|max:150',
                'user_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('wh_correctEdit')->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if ($model->update()) {
                $msg = 'Данные документа корректировки остатков № ' . $model->doc_num . ' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('wh_corrects')->with('status', $msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::whcorrect_edit')) {
            $whs = Warehouse::all();
            $whsel = array();
            foreach ($whs as $val) {
                $whsel[$val->id] = $val->title;
            }
            $prices = Price::all();
            $psel = array();
            foreach ($prices as $val) {
                $psel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $data = [
                'title' => 'Корректировка остатков',
                'head' => 'Новый документ',
                'whsel' => $whsel,
                'psel' => $psel,
                'usel' => $usel,
                'data' => $old
            ];
            return view('warehouse::whcorrect_edit', $data);
        }
        abort(404);
    }

    public function createPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['doc_id'];
            $doc = WhCorrect::find($id);
            if (!Role::granted('wh_doc')) {
                $msg = 'Попытка редактирования документа корректировки остатков ' . $doc->doc_num;
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
                'unit_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return 'NO VALIDATE';
            }
            //определяем стоимость по выбранному прайсу
            $price = null;
            $amount = null;
            $price = PriceTable::where(['price_id'=>$doc->price_id,'good_id'=>$input['good_id']])->first()->cost_1;
            if(!empty($price))
                $amount = round($input['qty'] * $price,2);
            // создаст или обновит запись в модели в зависимости от того
            // есть такая запись или нет
            TblWhCorrect::updateOrCreate(['wh_correct_id' => $input['doc_id'], 'good_id' => $input['good_id'],'cell' => $input['cell']],
                ['qty' => $input['qty'], 'price' => $price, 'unit_id' => $input['unit_id'], 'amount' => $amount,'created_at' => date('Y-m-d H:i:s')]);

            $msg = 'Артикул ' . $input['vendor_code'] . ' успешно добавлен\обновлен в документе-корректировке ' . $doc->doc_num . '!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            $row = TblWhCorrect::where(['wh_correct_id' => $input['doc_id'], 'good_id' => $input['good_id'],'cell' => $input['cell']])->first();
            $result = ['id' => $row->id, 'title' => $row->good->title, 'amount'=>$amount, 'analog_code'=>$row->good->analog_code,'price'=>$price];
            return json_encode($result);
        }
    }

    public function findPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = TblWhCorrect::find($id)->toArray();
            return json_encode($model);
        }
    }

    public function editPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $pos = TblWhCorrect::find($id);
            $doc = WhCorrect::find($pos->wh_correct_id);
            if (!Role::granted('wh_doc')) {
                $msg = 'Попытка редактирования документа корректировки остатков № ' . $doc->doc_num;
                //вызываем event
                event(new AddEventLogs('access', Auth::id(), $msg));
                return 'NO';
            }
            if($doc->status==0)
                return 'NO';
            if (isset($pos))
                $input['good_id'] = $pos->good_id;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input, [
                'cell' => 'nullable|string|max:10',
                'qty' => 'required|integer',
                'unit_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return 'NO VALIDATE';
            }
            //определяем стоимость по выбранному прайсу
            $price = null;
            $amount = null;
            $price = PriceTable::where(['price_id'=>$doc->price_id,'good_id'=>$input['good_id']])->first()->cost_1;
            if(!empty($price))
                $amount = round($input['qty'] * $price,2);
            $pos->cell = $input['cell'];
            $pos->qty = $input['qty'];
            $pos->price = $price;
            $pos->unit_id = $input['unit_id'];
            $pos->amount = $amount;
            $pos->update();
            $msg = 'Позиция с артикулом ' . $pos->good->vendor_code . ' была обновлена в документе корректировки остатков № ' . $doc->doc_num;
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            $row = TblWhCorrect::where(['wh_correct_id' => $pos->wh_correct_id, 'good_id' => $input['good_id'],'cell' => $input['cell']])->first();
            $result = ['id' => $row->id, 'amount'=>$amount, 'price'=>$price];
            return json_encode($result);
            //return 'OK';
        }
        return 'ERR';
    }

    public function delPosition(Request $request)
    {
        if (!Role::granted('wh_doc')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = TblWhCorrect::find($id);
            $status = $model->whcorrect->status;
            if($status==0)
                return 'NOT';
            if ($model->delete())
                return 'OK';
            else
                return 'ERR';
        }
    }

    public function upload(Request $request)
    {
        if (!Role::granted('export') && !Role::granted('wh_edit')) {
            abort(503, 'У Вас нет прав на выгрузку шаблона!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $doc_id = $input['doc_id'];
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                )
            );
            $doc_name = 'Корректировка остатков №'.WhCorrect::find($doc_id)->doc_num;

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(14);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Шаблон');
            $sheet->setCellValue('A1', $doc_id);
            $sheet->setCellValue('B1', $doc_name);
            $k = 2;
            $sheet->getStyle('A' . $k . ':M' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $k, 'Артикул');
            $sheet->setCellValue('B' . $k, 'Ячейка склада');
            $sheet->setCellValue('C' . $k, 'Кол-во');
            $sheet->getStyle('A' . $k . ':C' . $k)->applyFromArray($styleArray);

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "correct";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

    public function download(Request $request){
        if (!Role::granted('import') && !Role::granted('wh_edit')) {
            abort(503, 'У Вас нет прав на загрузку шаблона!');
        }
        $id ='';
        if($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 0;
            // Цикл по листам Excel-файла
            foreach( $tables as $table ) {
                $rows = count($table);
                $id = $table[0][0];
                $doc = WhCorrect::find($id);
                if(empty($doc)){
                    $msg = 'Файл шаблона поврежден или имеет неверный формат! Не обнаружен ID документа.';
                    return redirect('wh_corrects')->with('error', $msg);
                }
                $unit_id = 1;
                for($i=2;$i<$rows;$i++){
                    $row = $table[$i];
                    //определяем номенклатуру по артикулу
                    $good_id = Good::where(['vendor_code'=>$row[0]])->first()->id;
                    if(!empty($good_id)){
                        //определяем стоимость по выбранному прайсу
                        $price = null;
                        $amount = null;
                        $price = PriceTable::where(['price_id'=>$doc->price_id,'good_id'=>$good_id])->first()->cost_1;
                        if(!empty($price))
                            $amount = round($row[2] * $price,2);
                    }
                    if (!empty($id)) {
                        TblWhCorrect::updateOrCreate(['wh_correct_id' => $id,'good_id'=>$good_id,'cell'=>$row[1]], ['qty' => $row[2], 'price' => $price,
                            'unit_id' => $unit_id, 'amount' => $amount]);
                        $num++;
                    }
                }
            }
            $msg = 'Выполнен импорт данных из файла Excel!';
            $rows = $rows - 2; //не считаем заголовки
            //вызываем event
             event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('wh_corrects/view/'.$id)->with('status',$msg.' Обработано записей: '.$num.' из '.$rows);
        }
        $msg = 'Ну и где? Где я спрашиваю тот файл, который я должен загрузить из шаблона?';
        return redirect('wh_corrects/view/'.$id)->with('error', $msg);
    }

    public function writeToStock(Request $request){
        if (!Role::granted('wh_work')) {
            return 'NOT';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $doc = WhCorrect::find($id);
            $warehouse_id = $doc->warehouse_id;
            $rows = TblWhCorrect::where(['wh_correct_id'=>$id])->get();
            if(!empty($rows)){
                foreach ($rows as $row){
                    Stock::updateOrCreate(['warehouse_id' => $warehouse_id, 'good_id' => $row->good_id, 'cell' => $row->cell], ['qty' => $row->qty,
                        'unit_id' => $row->unit_id, 'cost' => $row->amount, 'created_at' => date('Y-m-d H:i:s')]);
                    //ищем пустую ячейку с таким товаром и удаляем ее из базы
                    $tmp = Stock::where(['warehouse_id' => $warehouse_id, 'good_id' => $row->good_id, 'cell' => null])->first();
                    if(!empty($tmp)) $tmp->delete();
                }
                $doc->status = 0;
                $doc->update();
                return 'OK';
            }
        }
        return 'ERR';
    }
}
