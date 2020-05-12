<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class CountryController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('countries')){
            $rows = Country::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Страны',
                'head' => 'Справочник стран мира',
                'rows' => $rows,
            ];

            return view('countries',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике стран!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'unique' => 'Значение поля должно быть уникальным!',
                'numeric' => 'Значение поля должно содержать только цифры!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:50',
                'code1' => 'required|unique:countries|string|max:5',
                'code2' => 'nullable|string|max:5',
                'code3' => 'nullable|string|max:5',
                'eaes' => 'required|numeric',
                'full_name' => 'nullable|string|max:70'
            ],$messages);
            if($validator->fails()){
                return redirect()->route('countryAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $country = new Country();
            $country->fill($input);
            $country->created_at = date('Y-m-d');
            if($country->save()){
                $msg = 'Новая страна '. $input['title'] .' успешно добавлена в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('countries')->with('status',$msg);
            }
        }
        if(view()->exists('country_add')){
            $data = [
                'title' => 'Страны',
                'head' => 'Новая запись',
            ];
            return view('country_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Country::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника стран.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Страна '. $model->title .' была удалена из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/countries')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике стран.';
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на редактирование записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'unique' => 'Значение поля должно быть уникальным!',
                'numeric' => 'Значение поля должно содержать только цифры!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:50',
                'code1' => 'required|string|max:5',
                'code2' => 'nullable|string|max:5',
                'code3' => 'nullable|string|max:5',
                'eaes' => 'required|numeric',
                'full_name' => 'nullable|string|max:70'
            ],$messages);
            if($validator->fails()){
                return redirect()->route('countryEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные страны '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('countries')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('country_edit')){
            $data = [
                'title' => 'Страны',
                'head' => 'Редактирование страны '.$old['title'],
                'data' => $old,
            ];
            return view('country_edit',$data);
        }
        abort(404);
    }

    public function download(Request $request)
    {
        if (!Role::granted('import') && !Role::granted('edit_refs')) {
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
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 1; $i < $rows; $i++) {
                    $row = $table[$i];
                    $title = trim($row[0]);
                    $code1 = $row[1];
                    $code2 = $row[2];
                    $code3 = $row[3];
                    $eaes = 0;
                    if(trim($row[4])=='Да')
                        $eaes = 1;
                    $full = trim($row[5]);
                    if (!empty($title) && !empty($code1)) {
                        Country::updateOrCreate(['title' => $title,'code1'=>$code1], ['code2' => $code2, 'code3' => $code3,
                            'eaes' => $eaes, 'full_name' => $full]);
                        $num++;
                    }
                }
            }
            $result = ['rows' => $rows, 'num' => $num];
            return json_encode($result);
        }
        return 'ERR';
    }

    public function upload()
    {
        if (!Role::granted('export') && !Role::granted('edit_refs')) {
            abort(503,'У Вас нет прав на экспорт справочников!');
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Страны');
        $k = 1;
        $sheet->getStyle('A' . $k . ':F' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A' . $k, 'Наименование');
        $sheet->setCellValue('B' . $k, 'Код');
        $sheet->setCellValue('C' . $k, 'Код альфа-2');
        $sheet->setCellValue('D' . $k, 'Код альфа-3');
        $sheet->setCellValue('E' . $k, 'Участник ЕАЭС');
        $sheet->setCellValue('F' . $k, 'Полное наименование');
        $k++;
        $rows = Country::orderBy('title','asc')->get();
        foreach ($rows as $row){
            $sheet->setCellValue('A' . $k, $row->title);
            $sheet->setCellValue('B' . $k, $row->code1);
            $sheet->setCellValue('C' . $k, $row->code2);
            $sheet->setCellValue('D' . $k, $row->code3);
            $sheet->setCellValue('E' . $k, 'Нет');
            if($row->eaes)
                $sheet->setCellValue('E' . $k, 'Да');
            $sheet->setCellValue('F' . $k, $row->full_name);
            $sheet->getStyle('B' . $k . ':E' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k++;
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "country";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
