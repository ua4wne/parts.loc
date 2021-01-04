<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Group;
use Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('wh_work')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочника групп товаров!');
        }
        if (view()->exists('warehouse::groups')) {
            $rows = Group::paginate(env('PAGINATION_SIZE'));
            $title = 'Группы товаров';
            $data = [
                'title' => $title,
                'head' => 'Товарные группы',
                'rows' => $rows,
            ];
            return view('warehouse::groups', $data);
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
            $msg = 'Попытка создания новой товарной группы!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание товарных групп!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('groupAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $group = new Group();
            $group->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $group в зависимости от того
            // есть такая запись или нет
            Group::updateOrCreate(['title' => $input['title']], ['descr' => $input['descr']]);

            $msg = 'Данные по товарной группе ' . $group->title . ' успешно добавлены\обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('groups')->with('status', $msg);
        }
        if (view()->exists('warehouse::group_add')) {
            $data = [
                'title' => 'Группы товаров',
                'head' => 'Новая запись',
            ];
            return view('warehouse::group_add', $data);
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
        $model = Group::find($id);
        $name = $model->title;
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_edit')) {
                $msg = 'Попытка удаления товарной группы ' . $name;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Товарная группа ' . $name . ' была удалена!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/groups')->with('status', $msg);
        }
        if (!Role::granted('wh_edit')) {
            $msg = 'Попытка редактирования товарной группы ' . $name;
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
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('groupEdit')->withErrors($validator)->withInput();
            }
            Group::updateOrCreate(['title' => $input['title']], ['descr' => $input['descr']]);

            $msg = 'Данные по товарной группе ' . $model->title . ' успешно добавлены\обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('groups')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::group_edit')) {
            $data = [
                'title' => 'Группы товаров',
                'head' => $model->title,
                'data' => $old,
            ];
            return view('warehouse::group_edit', $data);
        }
        abort(404);
    }
}
