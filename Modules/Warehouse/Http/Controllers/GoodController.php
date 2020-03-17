<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Category;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Group;
use Modules\Warehouse\Entities\Unit;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
            Good::updateOrCreate(['vendor_code' => $input['vendor_code']], ['category_id' => $input['category_id'],'group_id' => $input['group_id'],'title' => $input['title'],
                'descr' => $input['descr'],'bx_group' => $input['bx_group'],'vendor_code' => $input['vendor_code'],'analog_code' => $input['analog_code'],'brand' => $input['brand'],
                'model' => $input['model'],'unit_id' => $input['unit_id'],'weight' => $input['weight'],'capacity' => $input['capacity'],'length' => $input['length'],
                'area' => $input['area'],'vat' => $input['vat'],'gtd' => $input['gtd'],'barcode' => $input['barcode']]);

            $msg = 'Номенклатура ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            //return redirect()->route('goods')->with('status', $msg);
            $id = Good::where(['vendor_code' => $input['vendor_code']])->first()->id;
            return $id;
        }
        /*if (view()->exists('warehouse::good_add')) {
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
                'title' => 'Номенклатура',
                'head' => 'Новая запись',
                'catsel' => $catsel,
                'groupsel' => $groupsel,
                'unitsel' => $unitsel,
            ];
            return view('warehouse::good_add', $data);
        }
        abort(404);*/
    }

    public function edit(Request $request){
        if (!Role::granted('wh_work')) {//вызываем event
            $msg = 'Попытка редактирования номенклатуры!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            //abort(503, 'У Вас нет прав на редактирование номенклатуры!');
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $model = Good::find($input['id']);
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
            // есть такая запись или нет
            Good::updateOrCreate(['vendor_code' => $input['vendor_code']], ['category_id' => $input['category_id'],'group_id' => $input['group_id'],'title' => $input['title'],
                'descr' => $input['descr'],'bx_group' => $input['bx_group'],'vendor_code' => $input['vendor_code'],'analog_code' => $input['analog_code'],'brand' => $input['brand'],
                'model' => $input['model'],'unit_id' => $input['unit_id'],'weight' => $input['weight'],'capacity' => $input['capacity'],'length' => $input['length'],
                'area' => $input['area'],'vat' => $input['vat'],'gtd' => $input['gtd'],'barcode' => $input['barcode']]);

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
            $model = Good::find($input['id'])->toArray();
            return json_encode($model);
        }
    }

    public function view(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $category_id = $input['id'];
            $goods = Good::where(['category_id'=>$category_id])->get();
            if(!empty($goods)){
                $content='';
                foreach ($goods as $good){
                    $content.= '<tr id="row'.$good->id.'"><td>'.$good->group->title.'</td><td>'.$good->title.'</td><td>'.$good->vendor_code.'</td><td>'.$good->analog_code.'</td>
                                <td>'.$good->brand.'</td><td>'.$good->model.'</td><td>';
                    if($good->gtd)
                        $content.= '<span role="button" class="label label-success">Есть</span>';
                    else
                        $content.= '<span role="button" class="label label-danger">Нет</span>';
                    $content.= '</td><td>'.$good->barcode.'</td>
                                            <td>
                                                <div class="form-group" role="group">
                                                    <button class="btn btn-success btn-sm row_edit" type="button"
                                                            data-toggle="modal" data-target="#editGood"
                                                            title="Редактировать запись"><i class="fa fa-edit fa-lg"
                                                                                            aria-hidden="true"></i>
                                                    </button>
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

    public function download(Request $request){
        if(!Role::granted('import') && !Role::granted('wh_edit')){
            return 'NO';
        }
        if($request->hasFile('file')) {
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
            foreach( $tables as $table ) {
                $rows = count($table);
                $analog = '';
                for($i=1;$i<$rows;$i++){
                    $row = $table[$i];
                    $title = trim($row[2]);
                    //выделяем номера аналогов из строки с именем
                    $tmp = explode(',',$title);
                    $title = $tmp[0]; //наименование слева от первой запятой, аналоги справа
                    if(!empty($tmp[1])){
                        if(strstr($row[5],"Komatsu") !== FALSE){
                            //заменяем все внутренние пробелы на тире
                            $tmp[1] = trim(str_replace(' ','-',$tmp[1]));
                            $row[1] = trim(str_replace(' ','-',$row[1]));
                        }
                        //разделяем запятыми строку аналогов
                        $analog = trim(str_replace('/',' ',$tmp[1]));
                        $analog = str_replace(" ",", ",$analog);
                        if($analog == $row[1])
                            $analog = '';
                    }
                    if(!empty($row[1])){
                        Good::updateOrCreate(['vendor_code' => $row[1]], ['category_id' => $cat_id,'group_id' => $group_id,
                            'title' => $title,'bx_group' => $row[0],'analog_code' => $analog,'brand' => $row[4], 'model' => $row[5],
                            'unit_id' => $unit_id,'vat' => $vat,'gtd' => $gtd]);
                        $num++;
                    }
                }
            }
            $result = ['rows'=>$rows,'num'=>$num];
            return json_encode($result);
        }
        return 'ERR';
    }

    public function delete(Request $request){
        if(!Role::granted('wh_edit')){
            return 'NO';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //$model = Good::find($input['id']);
            return 'OK';
        }
    }
}