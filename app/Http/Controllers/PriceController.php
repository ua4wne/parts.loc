<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Currency;
use App\Models\Organisation;
use App\Models\Price;
use App\Models\PriceTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class PriceController extends Controller
{
    public function index()
    {
        if (view()->exists('prices')) {
            $rows = Price::paginate(env('PAGINATION_SIZE'));
            $title = 'Прайсы';
            $data = [
                'title' => $title,
                'head' => 'Прайсы',
                'rows' => $rows,
            ];
            return view('prices', $data);
        }
        abort(404);
    }

    public function create(Request $request)
    {
        if (!Role::granted('price_edit')) {//вызываем event
            $msg = 'Попытка создания нового прайса!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание прайсов!');
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
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
                'currency_id' => 'required|integer',
                'organisation_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('priceAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $doc_num = LibController::GenNumberDoc('prices');
            //смотрим указан ли префикс для документов организации
            $prefix = Organisation::find($input['organisation_id'])->prefix;
            if(!empty($prefix))
                $doc_num = $prefix.'-'.$doc_num;
            $price = new Price();
            $price->fill($input);
            $price->doc_num = $doc_num;
            $price->user_id = Auth::id();
            $price->created_at = date('Y-m-d H:i:s');
            if ($price->save()) {
                $msg = 'Новый прайс ' . $input['title'] . ' успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('prices')->with('status', $msg);
            }
        }
        if (view()->exists('price_add')) {
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Номенклатура',
                'head' => 'Новая запись',
                'cursel' => $cursel,
                'orgsel' => $orgsel,
            ];
            return view('price_add', $data);
        }
        abort(404);
    }

    public function edit($id, Request $request)
    {
        $model = Price::find($id);
        if ($request->isMethod('delete')) {
            if (!Role::granted('price_edit')) {
                $msg = 'Попытка удаления прайса ' . $model->title;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление прайсов!');
            }
            PriceTable::where(['price_id' => $model->id])->delete();
            $msg = 'Прайс ' . $model->title . ' был удален со всем своим содержимым!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/prices')->with('status', $msg);
        }
        if (!Role::granted('price_edit')) {
            $msg = 'Попытка редактирования прайса ' . $model->title;
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование прайсов!');
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
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
                'currency_id' => 'required|integer',
                'organisation_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('priceEdit', ['id' => $id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if ($model->update()) {
                $msg = 'Данные прайса ' . $model->title . ' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('prices')->with('status', $msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('price_edit')) {
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Прайсы',
                'head' => 'Редактирование прайса ' . $old['title'],
                'data' => $old,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
            ];
            return view('price_edit', $data);
        }
        abort(404);
    }

    public function show($id)
    {
        if (view()->exists('price_view')) {
            $name = Price::find($id)->title;
            $rows = PriceTable::where(['price_id' => $id])->get();
            $data = [
                'title' => 'Прайсы',
                'head' => 'Просмотр прайса ' . $name,
                'rows' => $rows,
                'id' => $id,
            ];
            return view('price_view', $data);
        }
        abort(404);
    }

    public function createPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['price_id'];
            $price = Price::find($id);
            if (!Role::granted('price_edit')) {
                $msg = 'Попытка редактирования прайса ' . $price->title;
                //вызываем event
                event(new AddEventLogs('access', Auth::id(), $msg));
                //abort(503, 'У Вас нет прав на редактирование прайсов!');
                return 'NO';
            }
            if (isset($input['vendor_code']))
                $input['good_id'] = Good::where('vendor_code', $input['vendor_code'])->first()->id;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'numeric' => 'Значение поля должно быть целым или дробным числом!',
            ];
            $validator = Validator::make($input, [
                'price_id' => 'required|integer',
                'good_id' => 'required|integer',
                'cost_1' => 'required|numeric',
                'cost_2' => 'nullable|numeric',
                'cost_3' => 'nullable|numeric',
            ], $messages);
            if ($validator->fails()) {
                //return redirect()->route('posPriceAdd',['price_id'=>$price_id])->withErrors($validator)->withInput();
                return 'NO VALIDATE';
            }
            // создаст или обновит запись в модели $good в зависимости от того
            // есть такая запись или нет
            PriceTable::updateOrCreate(['price_id' => $input['price_id'], 'good_id' => $input['good_id']], ['cost_1' => $input['cost_1'],
                'cost_2' => $input['cost_2'], 'cost_3' => $input['cost_3'], 'created_at' => date('Y-m-d H:i:s')]);

            $msg = 'Артикул ' . $input['vendor_code'] . ' успешно добавлен\обновлен в прайсе ' . $price->title . '!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            //return redirect()->route('priceView',['id'=>$price_id])->with('status', $msg);
            $row = PriceTable::where(['price_id' => $input['price_id'], 'good_id' => $input['good_id']])->first();
            $result = ['id' => $row->id, 'title' => $row->good->title];
            return json_encode($result);
        }
    }

    public function editPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $pos = PriceTable::find($id);
            $price = Price::find($pos->price_id);
            if (!Role::granted('price_edit')) {
                $msg = 'Попытка редактирования прайса ' . $price->title;
                //вызываем event
                event(new AddEventLogs('access', Auth::id(), $msg));
                //abort(503, 'У Вас нет прав на редактирование прайсов!');
                return 'NO';
            }
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'numeric' => 'Значение поля должно быть целым или дробным числом!',
            ];
            $validator = Validator::make($input, [
                'cost_1' => 'required|numeric',
                'cost_2' => 'nullable|numeric',
                'cost_3' => 'nullable|numeric',
            ], $messages);
            if ($validator->fails()) {
                return 'NO VALIDATE';
            }
            $pos->cost_1 = $input['cost_1'];
            $pos->cost_2 = $input['cost_2'];
            $pos->cost_3 = $input['cost_3'];
            $pos->update();
            $msg = 'Позиция с артикулом ' . $pos->vendor_code . ' была обновлена в прайсе ' . $price->title . '!';
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
            $model = PriceTable::find($id)->toArray();
            return json_encode($model);
        }
    }

    public function delPosition(Request $request)
    {
        if (!Role::granted('price_edit')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = PriceTable::find($id);
            if ($model->delete())
                return 'OK';
            else
                return 'ERR';
        }
    }

    public function download(Request $request)
    {
        if (!Role::granted('import')) {
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
            $price_id = Price::where(['doc_num'=>$doc_num])->first()->id;
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 2; $i < $rows; $i++) {
                    $row = $table[$i];
                    if(!empty($row[1])){
                        //$vendor = trim($row[1]);
                        $vendor = $row[1];
                        if(!is_numeric($row[3]))
                            $row[3] = 0;
                        if(!is_numeric($row[6]))
                            $row[6] = null;
                        if(!is_numeric($row[8]))
                            $row[8] = null;
                        $good = Good::where(['vendor_code'=>$vendor])->first();
                        if (!empty($good)) {
                            // создаст или обновит запись в модели $good в зависимости от того
                            // есть такая запись или нет
                            PriceTable::updateOrCreate(['price_id' => $price_id, 'good_id' => $good->id], ['cost_1' => $row[3],
                                'cost_2' => $row[6], 'cost_3' => $row[8], 'created_at' => date('Y-m-d H:i:s')]);
                            $num++;
                        }
                    }
                }
                break;
            }
            $msg = 'Прайс №' . $doc_num . ' был загружен из файла '.$path;
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            $result = ['rows' => $rows, 'num' => $num];
            return json_encode($result);
        }
        return 'ERR';
    }

    public function upload($id)
    {
        if (!Role::granted('export')) {
            abort(503, 'У Вас нет прав на экспорт прайсов!');
        }
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
        $price = Price::find($id);
        if(!empty($price)){
            $title = $price->title;
            $doc_num = $price->doc_num;
            $date = $price->updated_at;
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($title);
            $sheet->setCellValue('A1',$title);
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1:I1')->getFont()->setBold(true);
            $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A2', $doc_num);
            $sheet->setCellValue('B2', 'Дата обновления: '.$date);
            $k = 3;
            $sheet->getStyle('A' . $k . ':I' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $k, 'Код Bitrix');
            $sheet->setCellValue('B' . $k, 'Артикул');
            $sheet->setCellValue('C' . $k, 'Аналоги');
            $sheet->setCellValue('D' . $k, 'Наименование');
            $sheet->setCellValue('E' . $k, 'Цена, руб (Сайт)');
            $sheet->setCellValue('F' . $k, 'Бренд');
            $sheet->setCellValue('G' . $k, 'Модель');
            $sheet->setCellValue('H' . $k, 'Цена (1С)');
            $sheet->setCellValue('I' . $k, 'Цена (Рынок)');
            $sheet->getStyle('A' . $k . ':I' . $k)->applyFromArray($styleArray);
            $k++;
            //выбираем позиции прайса
            $positions = PriceTable::where(['price_id'=>$id])->get();
            foreach ($positions as $row){
                $sheet->setCellValue('A' . $k, $row->good->bx_group);
                $sheet->setCellValue('B' . $k, $row->good->vendor_code);
                $sheet->setCellValue('C' . $k, $row->good->analog_code);
                $sheet->setCellValue('D' . $k, $row->good->title);
                $sheet->setCellValue('E' . $k, $row->cost_1);
                $sheet->setCellValue('F' . $k, $row->good->brand);
                $sheet->setCellValue('G' . $k, $row->good->model);
                $sheet->setCellValue('H' . $k, $row->cost_2);
                $sheet->setCellValue('I' . $k, $row->cost_3);
                $k++;
            }
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "price";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }

    }
}
