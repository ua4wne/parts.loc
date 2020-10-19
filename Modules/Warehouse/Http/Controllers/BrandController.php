<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Brand;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('wh_work')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочника единиц измерений!');
        }
        if (view()->exists('warehouse::brands')) {
            $rows = Brand::paginate(env('PAGINATION_SIZE'));
            $title = 'Бренды';
            $data = [
                'title' => $title,
                'head' => 'Справочник брендов',
                'rows' => $rows,
            ];
            return view('warehouse::brands', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('wh_edit')) {//вызываем event
            $msg = 'Попытка создания нового бренда!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание брендов!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|string|max:70',
                'full_name' => 'nullable|string|max:100',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //dd($input);
            $brand = new Brand();
            $brand->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $brand в зависимости от того
            // есть такая запись или нет
            Brand::updateOrCreate(['title' => $input['title']], ['full_name' => $input['full_name']]);

            $msg = 'Запись бренда ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('brands')->with('status', $msg);
        }
        if (view()->exists('warehouse::brand_add')) {
            $data = [
                'title' => 'Справочник брендов',
                'head' => 'Новая запись',
            ];
            return view('warehouse::brand_add', $data);
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
        $model = Brand::find($id);
        $name = $model->title;
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_edit')) {
                $msg = 'Попытка удаления бренда ' . $name;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Бренд ' . $name . ' был удален!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/brands')->with('status', $msg);
        }
        if (!Role::granted('wh_edit')) {
            $msg = 'Попытка редактирования бренда ' . $name;
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|string|max:70',
                'full_name' => 'required|string|max:100',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            Brand::updateOrCreate(['title' => $input['title']], ['full_name' => $input['full_name']]);

            $msg = 'Запись бренда ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('brands')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::brand_edit')) {
            $data = [
                'title' => 'Справочник брендов',
                'head' => 'Новая запись',
                'data' => $old,
            ];
            return view('warehouse::brand_edit', $data);
        }
        abort(404);
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
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 0; $i < $rows; $i++) {
                    $row = $table[$i];
                    if(!empty($row[0])){
                        $title = $row[0];
                        $full_name = $row[0];
                        Brand::updateOrCreate(['title' => $title], ['full_name' => $full_name]);
                        $num++;
                    }
                }
                break;
            }
            $msg = 'Данные по брендам были загружены из файла '.$path;
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            $result = ['rows' => $rows, 'num' => $num];
            return json_encode($result);
        }
        return 'ERR';
    }
}
