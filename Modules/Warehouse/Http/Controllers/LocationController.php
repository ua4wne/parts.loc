<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Location;
use Modules\Warehouse\Entities\Warehouse;
use Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('wh_work')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр мест хранений!');
        }
        if (view()->exists('warehouse::locations')) {
            $rows = Location::all(); //paginate(env('PAGINATION_SIZE'));
            $title = 'Места хранения';
            $data = [
                'title' => $title,
                'head' => 'Места хранения',
                'rows' => $rows,
            ];
            return view('warehouse::locations', $data);
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
            $msg = 'Попытка создания новой ячейки!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание мест хранений!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|unique:locations|string|max:15',
                'barcode' => 'nullable|string|max:32',
                'warehouse_id' => 'required|integer',
                'length' => 'nullable|numeric',
                'widht' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'capacity' => 'required|numeric',
                'priority' => 'required|integer',
                'in_lock' => 'required|integer',
                'out_lock' => 'required|integer',

            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //dd($input);
            $location = new Location();
            $location->fill($input);
            $location->created_at = date('Y-m-d');
            if($location->save()){
                $msg = 'Новое место хранения ' . $location->title . ' успешно добавлено!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('locations')->with('status', $msg);
            }
        }
        if (view()->exists('warehouse::location_add')) {
            $whs = Warehouse::all();
            $whsel = array();
            foreach ($whs as $val) {
                $whsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Места хранения',
                'head' => 'Новое место хранения',
                'whsel' => $whsel,
            ];
            return view('warehouse::location_add', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id,Request $request)
    {
        $model = Location::find($id);
        $name = $model->title;
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_edit')) {
                $msg = 'Попытка удаления места хранения ' . $name;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Место хранения ' . $name . ' было удалено!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/locations')->with('status', $msg);
        }
        if (!Role::granted('wh_edit')) {
            $msg = 'Попытка редактирования места хранения ' . $name;
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                //'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                //'title' => 'required|string|max:15',
                'barcode' => 'nullable|string|max:32',
                'warehouse_id' => 'required|integer',
                'length' => 'nullable|numeric',
                'widht' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'capacity' => 'required|numeric',
                'priority' => 'required|integer',
                'in_lock' => 'required|integer',
                'out_lock' => 'required|integer',

            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные по месту хранения ' . $model->title . ' успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
            }
            return redirect()->route('locations')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::location_edit')) {
            $whs = Warehouse::all();
            $whsel = array();
            foreach ($whs as $val) {
                $whsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Места хранения',
                'head' => 'Ячейка ' . $model->title,
                'data' => $old,
                'whsel' => $whsel,
            ];
            return view('warehouse::location_edit', $data);
        }
        abort(404);
    }
}
