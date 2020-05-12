<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Category;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Group;
use Modules\Warehouse\Entities\Unit;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class GoodController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('warehouse::goods')) {
            //выбираем первую категорию из списка для отображения
            //$node = Category::first();
            //$rows = Good::where(['category_id'=>$node->id])->orderBy('updated_at', 'desc')->get();
            $rows = [];
            $title = 'Номенклатура';
            $cats = Category::all();
            $catsel = array();
            foreach ($cats as $val) {
                $catsel[$val->id] = $val->category;
            }
            $groups = Group::all();
            $groupsel = array();
            foreach ($groups as $val) {
                $groupsel[$val->id] = $val->title;
            }
            $units = Unit::all();
            $unitsel = array();
            foreach ($units as $val) {
                $unitsel[$val->id] = $val->title;
            }
            $data = [
                'title' => $title,
                'head' => 'Справочник номенклатуры',
                'rows' => $rows,
                'catsel' => $catsel,
                'groupsel' => $groupsel,
                'unitsel' => $unitsel,
                'node' => '1',
                'sub' => '0',
            ];
            return view('warehouse::goods', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('wh_work')) {//вызываем event
            $msg = 'Попытка создания новой номенклатуры!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            //abort(503, 'У Вас нет прав на создание номенклатуры!');
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
                'numeric' => 'Значение поля должно быть целым или дробным числом!',
            ];
            $validator = Validator::make($input, [
                'category_id' => 'required|integer',
                'group_id' => 'required|integer',
                'title' => 'required|string|max:200',
                'descr' => 'nullable|string|max:255',
                'bx_group' => 'nullable|integer',
                'vendor_code' => 'required|string|max:64',
                'analog_code' => 'nullable|string|max:180',
                'brand' => 'nullable|string|max:200',
                'model' => 'nullable|string|max:200',
                'unit_id' => 'required|integer',
                'weight' => 'nullable|numeric',
                'capacity' => 'nullable|numeric',
                'length' => 'nullable|numeric',
                'area' => 'nullable|numeric',
                'vat' => 'nullable|integer',
                'gtd' => 'nullable|integer',
                'barcode' => 'nullable|string|max:100',
            ], $messages);
            if ($validator->fails()) {
                //return redirect()->route('goodAdd')->withErrors($validator)->withInput();
                return 'NO VALIDATE';
            }
            //dd($input);
            $good = new Good();
            $good->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $good в зависимости от того
            // есть такая запись или нет
            Good::updateOrCreate(['vendor_code' => $input['vendor_code']], ['category_id' => $input['category_id'], 'group_id' => $input['group_id'], 'title' => $input['title'],
                'descr' => $input['descr'], 'bx_group' => $input['bx_group'], 'vendor_code' => $input['vendor_code'], 'analog_code' => $input['analog_code'], 'brand' => $input['brand'],
                'model' => $input['model'], 'unit_id' => $input['unit_id'], 'weight' => $input['weight'], 'capacity' => $input['capacity'], 'length' => $input['length'],
                'area' => $input['area'], 'vat' => $input['vat'], 'gtd' => $input['gtd'], 'barcode' => $input['barcode']]);

            $msg = 'Номенклатура ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            //return redirect()->route('goods')->with('status', $msg);
            $id = Good::where(['vendor_code' => $input['vendor_code']])->first()->id;
            return $id;
        }
    }

    public function edit(Request $request)
    {
        if (!Role::granted('wh_work')) {//вызываем event
            $msg = 'Попытка редактирования номенклатуры!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            //abort(503, 'У Вас нет прав на редактирование номенклатуры!');
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $model = Good::find($id);
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
                'numeric' => 'Значение поля должно быть целым или дробным числом!',
            ];
            $validator = Validator::make($input, [
                'category_id' => 'required|integer',
                'group_id' => 'required|integer',
                'title' => 'required|string|max:200',
                'descr' => 'nullable|string|max:255',
                'bx_group' => 'nullable|integer',
                'vendor_code' => 'required|string|max:64',
                'analog_code' => 'nullable|string|max:180',
                'brand' => 'nullable|string|max:200',
                'model' => 'nullable|string|max:200',
                'unit_id' => 'required|integer',
                'weight' => 'nullable|numeric',
                'capacity' => 'nullable|numeric',
                'length' => 'nullable|numeric',
                'area' => 'nullable|numeric',
                'vat' => 'nullable|integer',
                'gtd' => 'nullable|integer',
                'barcode' => 'nullable|string|max:100',
            ], $messages);
            if ($validator->fails()) {
                //return redirect()->route('goodAdd')->withErrors($validator)->withInput();
                return 'NO VALIDATE';
            }
            if($model->vendor_code != $input['vendor_code']){ //если правили артикул
                $model->vendor_code = $input['vendor_code'];
                $model->save();
            }

            // есть такая запись или нет
            Good::updateOrCreate(['vendor_code' => $input['vendor_code']], ['category_id' => $input['category_id'], 'group_id' => $input['group_id'], 'title' => $input['title'],
                'descr' => $input['descr'], 'bx_group' => $input['bx_group'], 'vendor_code' => $input['vendor_code'], 'analog_code' => $input['analog_code'], 'brand' => $input['brand'],
                'model' => $input['model'], 'unit_id' => $input['unit_id'], 'weight' => $input['weight'], 'capacity' => $input['capacity'], 'length' => $input['length'],
                'area' => $input['area'], 'vat' => $input['vat'], 'gtd' => $input['gtd'], 'barcode' => $input['barcode']]);

            $msg = 'Номенклатура ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return $model->category_id;
        }
    }

    public function find(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = Good::find($id)->toArray();
            return json_encode($model);
        }
    }

    public function view(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $category_id = $input['id'];
            //ищем все дочерние категории для данной
            $cats = Category::where(['parent_id'=>$category_id])->get();
            $inval = [$category_id];
            if(!empty($cats)){
                foreach ($cats as $val){
                    array_push($inval,$val->id);
                }
            }
            $goods = Good::whereIn('category_id',$inval)->orderBy('updated_at', 'desc')->get();
            if (!empty($goods)) {
                $content = '';
                foreach ($goods as $good) {
                    $content .= '<tr id="row' . $good->id . '"><td>' . $good->group->title . '</td><td>' . $good->title . '</td><td>' . $good->vendor_code . '</td><td>' . $good->analog_code . '</td>
                                <td>' . $good->brand . '</td><td>' . $good->model . '</td><td>' . $good->barcode . '</td><td>';
                    $content .= $good->updated_at;
                    $content .= '</td><td style="width: 120px">
                                                <div class="form-group" role="group">
                                                    <button class="btn btn-success btn-sm row_edit" type="button"
                                                            data-toggle="modal" data-target="#editGood"
                                                            title="Редактировать запись"><i class="fa fa-edit fa-lg"
                                                                                            aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-info btn-sm row_transfer" type="button"
                                                            title="Передать на сайт"><i class="fa fa-refresh fa-lg"
                                                                                      aria-hidden="true"></i></button>
                                                    <button class="btn btn-danger btn-sm row_delete" type="button"
                                                            title="Удалить запись"><i class="fa fa-trash fa-lg"
                                                                                      aria-hidden="true"></i></button>
                                                </div>
                                            </td>
                                        </tr>';

                }
                return $content;
            }
            return 'NODATA';
        }
    }

    public function download(Request $request)
    {
        if (!Role::granted('import') && !Role::granted('wh_edit')) {
            return 'NO';
        }
        if ($request->hasFile('file')) {
            $cat_id = 2; //$input['category_id'];
            $group_id = 6; //$input['group_id'];
            $unit_id = 1; //$input['unit_id'];
            $vat = 20; //$input['vat'];
            $gtd = 0; //$input['gtd'];
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 0;
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 1; $i < $rows; $i++) {
                    $analog = '';
                    $row = $table[$i];
                    $title = trim($row[2]);
                    //выделяем номера аналогов из строки с именем
                    $tmp = explode(',', $title);
                    $title = $tmp[0]; //наименование слева от первой запятой, аналоги справа
                    if (!empty($tmp[1])) {
                        if (strstr($row[5], "Komatsu") !== FALSE) {
                            //заменяем все внутренние пробелы на тире
                            $tmp[1] = trim(str_replace(' ', '-', $tmp[1]));
                        }
                        //разделяем запятыми строку аналогов
                        $analog = trim(str_replace('/', ' ', $tmp[1]));
                        $analog = str_replace(" ", ", ", $analog);
                        //if ($analog == $row[1])
                        //    $analog = '';
                    }
                    if (strstr($row[5], "Komatsu") !== FALSE) {
                        //заменяем все внутренние пробелы на тире
                        $row[1] = str_replace(' ', '-', trim($row[1]));
                    }
                    if (!empty($row[1])) {
                        $row[1] = trim($row[1]);
                        Good::updateOrCreate(['vendor_code' => $row[1]], ['category_id' => $cat_id, 'group_id' => $group_id,
                            'title' => $title, 'bx_group' => $row[0], 'analog_code' => $analog, 'brand' => $row[4], 'model' => $row[5],
                            'unit_id' => $unit_id, 'vat' => $vat, 'gtd' => $gtd]);
                        $num++;
                    }
                }
            }
            $result = ['rows' => $rows, 'num' => $num];
            return json_encode($result);
        }
        return 'ERR';
    }

    public function delete(Request $request)
    {
        if (!Role::granted('wh_edit')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            $model = Good::find($id);
            if ($model->delete())
                return 'OK';
            else
                return 'ERR';
        }
    }

    public function upload(Request $request)
    {
        if (!Role::granted('export') && !Role::granted('wh_edit')) {
            abort(503, 'У Вас нет прав на экспорт номенклатуры!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $goods = Good::whereIn('category_id', $input['category'])->orderBy('category_id', 'asc')->get();
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
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Номенклатура');
            $k = 1;
            $sheet->getStyle('A' . $k . ':P' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A1', 'Категория');
            $sheet->setCellValue('B1', 'Группа');
            $sheet->setCellValue('C1', 'Наименование');
            $sheet->setCellValue('D1', 'Описание');
            $sheet->setCellValue('E1', 'Код Bitrix');
            $sheet->setCellValue('F1', 'Артикул');
            $sheet->setCellValue('G1', 'Аналоги');
            $sheet->setCellValue('H1', 'Производитель');
            $sheet->setCellValue('I1', 'Модель');
            $sheet->setCellValue('J1', 'Ед. изм.');
            $sheet->setCellValue('K1', 'Вес');
            $sheet->setCellValue('L1', 'Объем');
            $sheet->setCellValue('M1', 'Длина');
            $sheet->setCellValue('N1', 'Площадь');
            $sheet->setCellValue('O1', 'НДС');
            $sheet->setCellValue('P1', 'Учет ГТД');
            $sheet->getStyle('A' . $k . ':P' . $k)->applyFromArray($styleArray);
            $k++;
            foreach ($goods as $row) {
                $sheet->setCellValue('A' . $k, $row->category->category);
                $sheet->setCellValue('B' . $k, $row->group->title);
                $sheet->setCellValue('C' . $k, $row->title);
                $sheet->setCellValue('D' . $k, $row->descr);
                $sheet->setCellValue('E' . $k, $row->bx_group);
                $sheet->setCellValue('F' . $k, $row->vendor_code);
                $sheet->setCellValue('G' . $k, $row->analog_code);
                $sheet->setCellValue('H' . $k, $row->brand);
                $sheet->setCellValue('I' . $k, $row->model);
                $sheet->setCellValue('J' . $k, $row->unit);
                $sheet->setCellValue('K' . $k, $row->weight);
                $sheet->setCellValue('L' . $k, $row->capacity);
                $sheet->setCellValue('M' . $k, $row->length);
                $sheet->setCellValue('N' . $k, $row->area);
                $sheet->setCellValue('O' . $k, $row->vat);
                $sheet->setCellValue('P' . $k, $row->gtd);
                //$sheet->getStyle('A' . $k . ':P' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->getColumnDimension('K')->setAutoSize(true);
            $sheet->getColumnDimension('L')->setAutoSize(true);
            $sheet->getColumnDimension('M')->setAutoSize(true);
            $sheet->getColumnDimension('N')->setAutoSize(true);
            $sheet->getColumnDimension('O')->setAutoSize(true);
            $sheet->getColumnDimension('P')->setAutoSize(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "goods";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

    public function ajaxData(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select id,vendor_code as name from goods where vendor_code like '%$query%'");
        return response()->json($codes);
    }

    public function transfer(Request $request){
        if (!User::hasRole('content_manager')) {
            return 'NOT';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'], 3);
            //находим номенклатуру
            $good = Good::find($id);
            if (!empty($good)) {
                $url = env('EXT_URL');
                $post_data = array(
                    "token" => env('EXT_TOKEN'),
                    "section_id" => $good->bx_group,
                    "artnumber" => $good->vendor_code,
                    "analogs" => $good->analog_code,
                    "name" => $good->title,
                    "brand" => $good->brand,
                    "model_tech" => $good->model,
                    "detail_text" => $good->descr
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // Указываем, что у нас POST запрос
                curl_setopt($ch, CURLOPT_POST, 1);
                // Добавляем переменные
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $output = curl_exec($ch);
                curl_close($ch);
                return $output;
            }
        }
    }

    public function delSpace(Request $request)
    {
        if (!Role::granted('wh_edit')) {
            return 'NO';
        }
        if ($request->isMethod('post')) {
            //$input = $request->except('_token'); //параметр _token нам не нужен
            $cnt = 0;
            $rows = Good::select(['id','vendor_code'])->get();
            foreach ($rows as $row){
                if(strlen($row->vendor_code) != strlen(trim($row->vendor_code))){
                    $row->vendor_code = trim($row->vendor_code);
                    $row->update();
                    $cnt++;
                }
            }
            return 'Обработано записей - '.$cnt;
        }
    }
}
