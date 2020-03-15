<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Unit;
use Validator;

class UnitController extends Controller
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
        if (view()->exists('warehouse::units')) {
            $rows = Unit::paginate(env('PAGINATION_SIZE'));
            $title = 'Единицы измерений';
            $data = [
                'title' => $title,
                'head' => 'Единицы измерений',
                'rows' => $rows,
            ];
            return view('warehouse::units', $data);
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
            $msg = 'Попытка создания новой единицы измерения!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание единиц измерений!');
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
                'short_name' => 'required|string|max:7',
                'code' => 'required|string|max:7',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('unitAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $unit = new Unit();
            $unit->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $unit в зависимости от того
            // есть такая запись или нет
            Unit::updateOrCreate(['title' => $input['title']], ['short_name' => $input['short_name'],'code' => $input['code']]);

            $msg = 'Единица измерения ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('units')->with('status', $msg);
        }
        if (view()->exists('warehouse::unit_add')) {
            $data = [
                'title' => 'Единицы измерений',
                'head' => 'Новая запись',
            ];
            return view('warehouse::unit_add', $data);
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
        $model = Unit::find($id);
        $name = $model->title;
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_edit')) {
                $msg = 'Попытка удаления единицы измерения ' . $name;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Единица измерения ' . $name . ' была удалена!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/units')->with('status', $msg);
        }
        if (!Role::granted('wh_edit')) {
            $msg = 'Попытка редактирования единицы измерения ' . $name;
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
                'short_name' => 'required|string|max:7',
                'code' => 'required|string|max:7',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('unitEdit')->withErrors($validator)->withInput();
            }
            Unit::updateOrCreate(['title' => $input['title']], ['short_name' => $input['short_name'],'code' => $input['code']]);

            $msg = 'Единица измерения ' . $input['title'] . ' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('units')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::unit_edit')) {
            $data = [
                'title' => 'Единицы измерений',
                'head' => 'Новая запись',
                'data' => $old,
            ];
            return view('warehouse::unit_edit', $data);
        }
        abort(404);
    }
}
