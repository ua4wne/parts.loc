<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Organisation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Warehouse;
use Validator;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('wh_work')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочника складов!');
        }
        if (view()->exists('warehouse::warehouses')) {
            $rows = Warehouse::paginate(env('PAGINATION_SIZE'));
            $title = 'Склады';
            $data = [
                'title' => $title,
                'head' => 'Список складов',
                'rows' => $rows,
            ];
            return view('warehouse::warehouses', $data);
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
            $msg = 'Попытка создания новой записи в справочнике складов!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи в справочнике складов!');
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
                'organisation_id' => 'required|integer',
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
                'user_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('warehouseAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $warehouse = new Warehouse();
            $warehouse->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $personal в зависимости от того
            // есть такая запись или нет
            Warehouse::updateOrCreate(['organisation_id' => $input['organisation_id'], 'title' => $input['title']], ['descr' => $input['descr'], 'user_id' => $input['user_id']]);

            $msg = 'Данные по складу ' . $warehouse->title . ' успешно добавлены\обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('warehouses')->with('status', $msg);
        }
        if (view()->exists('warehouse::warehouse_add')) {
            $users = User::where(['active' => 1])->get();
            $usersel = array();
            foreach ($users as $val) {
                $usersel[$val->id] = $val->name;
            }
            $organisations = Organisation::all();
            $orgsel = array();
            foreach ($organisations as $val) {
                $orgsel[$val->id] = $val->short_name;
            }
            $data = [
                'title' => 'Склады',
                'head' => 'Новая запись',
                'usersel' => $usersel,
                'orgsel' => $orgsel,
            ];
            return view('warehouse::warehouse_add', $data);
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
        $model = Warehouse::find($id);
        $name = $model->title;
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_edit')) {
                $msg = 'Попытка удаления записи ' . $name . ' из справочника складов.';
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Запись ' . $name . ' была удалена из справочника складов!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/warehouses')->with('status', $msg);
        }
        if (!Role::granted('wh_edit')) {
            $msg = 'Попытка редактирования записи ' . $name . ' в справочнике складов.';
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
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'organisation_id' => 'required|integer',
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
                'user_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('warehouseEdit')->withErrors($validator)->withInput();
            }
            Warehouse::updateOrCreate(['organisation_id' => $input['organisation_id'], 'title' => $input['title']], ['descr' => $input['descr'], 'user_id' => $input['user_id']]);

            $msg = 'Данные по складу ' . $name . ' успешно добавлены\обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('warehouses')->with('status', $msg);

        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::warehouse_edit')) {
            $users = User::where(['active' => 1])->get();
            $usersel = array();
            foreach ($users as $val) {
                $usersel[$val->id] = $val->name;
            }
            $organisations = Organisation::all();
            $orgsel = array();
            foreach ($organisations as $val) {
                $orgsel[$val->id] = $val->short_name;
            }
            $data = [
                'title' => 'Склады',
                'head' => 'Новая запись',
                'usersel' => $usersel,
                'orgsel' => $orgsel,
                'data' => $old,
            ];
            return view('warehouse::warehouse_edit', $data);
        }
        abort(404);
    }
}
