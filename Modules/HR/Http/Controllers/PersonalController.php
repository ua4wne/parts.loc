<?php

namespace Modules\HR\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Organisation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\HR\Entities\Personal;
use Modules\HR\Entities\Position;
use Validator;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('hr_work')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочника персонала!');
        }
        if (view()->exists('hr::personals')) {
            $rows = Personal::paginate(env('PAGINATION_SIZE'));
            $title = 'Справочник персонала';
            $data = [
                'title' => $title,
                'head' => 'Справочник персонала',
                'rows' => $rows,
            ];
            return view('hr::personals', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('hr_work')) {//вызываем event
            $msg = 'Попытка создания новой записи в справочнике персонала!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи в справочнике персонала!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'user_id' => 'required|integer',
                'position_id' => 'required|integer',
                'organisation_id' => 'required|integer',
                'signing' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('personalAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $personal = new Personal();
            $personal->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $personal в зависимости от того
            // есть такая запись или нет
            Personal::updateOrCreate(['user_id' => $input['user_id'], 'organisation_id' => $input['organisation_id']], ['position_id' => $input['position_id'], 'signing' => $input['signing']]);

            $msg = 'Новая запись ' . User::find($input['user_id'])->name . ' успешно добавлена в справочник персонала!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('personals')->with('status', $msg);
        }
        if (view()->exists('hr::personal_add')) {
            $users = User::where(['active' => 1])->get();
            $usersel = array();
            foreach ($users as $val) {
                $usersel[$val->id] = $val->name;
            }
            $positions = Position::all();
            $possel = array();
            foreach ($positions as $val) {
                $possel[$val->id] = $val->title;
            }
            $organisations = Organisation::all();
            $orgsel = array();
            foreach ($organisations as $val) {
                $orgsel[$val->id] = $val->short_name;
            }
            $data = [
                'title' => 'Справочник персонала',
                'head' => 'Новая запись',
                'usersel' => $usersel,
                'possel' => $possel,
                'orgsel' => $orgsel,
            ];
            return view('hr::personal_add', $data);
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
        $model = Personal::find($id);
        $name = User::find($model->user_id)->name;
        if ($request->isMethod('delete')) {
            if (!Role::granted('hr_work')) {
                $msg = 'Попытка удаления записи ' . $name . ' из справочника сотрудников.';
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Запись ' . $name . ' была удалена из справочника сотрудников!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/personals')->with('status', $msg);
        }
        if (!Role::granted('hr_work')) {
            $msg = 'Попытка редактирования записи ' . $name . ' в справочнике сотрудников.';
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'user_id' => 'required|integer',
                'position_id' => 'required|integer',
                'organisation_id' => 'required|integer',
                'signing' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('personalAdd')->withErrors($validator)->withInput();
            }
            Personal::updateOrCreate(['user_id' => $input['user_id'], 'organisation_id' => $input['organisation_id']], ['position_id' => $input['position_id'], 'signing' => $input['signing']]);
            $msg = 'Данные сотрудника ' . User::find($input['user_id'])->name . ' были обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->route('personals')->with('status', $msg);

        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('hr::personal_edit')) {
            $users = User::where(['active' => 1])->get();
            $usersel = array();
            foreach ($users as $val) {
                $usersel[$val->id] = $val->name;
            }
            $positions = Position::all();
            $possel = array();
            foreach ($positions as $val) {
                $possel[$val->id] = $val->title;
            }
            $organisations = Organisation::all();
            $orgsel = array();
            foreach ($organisations as $val) {
                $orgsel[$val->id] = $val->short_name;
            }
            $data = [
                'title' => 'Справочник персонала',
                'head' => 'Редактирование записи '.User::find($old['user_id'])->name,
                'usersel' => $usersel,
                'possel' => $possel,
                'orgsel' => $orgsel,
                'data' => $old,
            ];
            return view('hr::personal_edit', $data);
        }
        abort(404);
    }
}
