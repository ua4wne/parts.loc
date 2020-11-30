<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Currency;
use App\Models\Organisation;
use App\Models\Statuse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Workflow\Entities\Agreement;
use Validator;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('workflow::agreements')){
            $rows = Agreement::paginate(env('PAGINATION_SIZE'));
            $data = [
                'title' => 'Соглашения об условии продаж',
                'head' => 'Соглашения об условиях продаж',
                'rows' => $rows,
            ];

            return view('workflow::agreements',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания нового соглашения об условиях продаж!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи.');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть целым числом!',
            ];
            $validator = Validator::make($input,[
                'doc_num' => 'required|string|max:15',
                'statuse_id' => 'required|integer',
                'title' => 'required|string|max:150',
                'start' => 'nullable|date',
                'finish' => 'nullable|date',
                'organisation_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'comment' => 'nullable|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //dd($input);
            $agreement = new Agreement();
            $agreement->fill($input);
            $agreement->created_at = date('Y-m-d');
            $agreement->user_id = Auth::id();
            if($agreement->save()){
                $msg = 'Новое соглашение об условиях продаж '. $input['title'] .' успешно добавлено!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('agreements')->with('status',$msg);
            }
        }
        if(view()->exists('workflow::agreement_add')){
            $stats = Statuse::all();
            $statsel = array();
            foreach ($stats as $val) {
                $statsel[$val->id] = $val->title;
            }
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::select('id', 'title')->where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Соглашения об условии продаж',
                'head' => 'Новая запись',
                'statsel' => $statsel,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
            ];
            return view('workflow::agreement_add', $data);
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
        $model = Agreement::find($id);
        if($request->isMethod('delete')){
            if(!User::hasRole('admin') || !User::isAuthor($model->user_id)){
                $msg = 'Попытка удаления соглашения об условиях продаж '.$model->title.' из справочника.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Соглашение об условии продаж '. $model->title .' было удалено из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/agreements')->with('status',$msg);
        }
        if(!User::hasRole('admin') || !User::isAuthor($model->user_id)){
            $msg = 'Попытка редактирования соглашения об условии продаж '.$model->title;
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
                'integer' => 'Значение поля должно быть целым числом!',
            ];
            $validator = Validator::make($input,[
                'doc_num' => 'required|string|max:15',
                'statuse_id' => 'required|integer',
                'title' => 'required|string|max:150',
                'start' => 'nullable|date',
                'finish' => 'nullable|date',
                'organisation_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'comment' => 'nullable|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные соглашения об условии продаж '. $model->title .' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('agreements')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('workflow::agreement_edit')){
            $stats = Statuse::all();
            $statsel = array();
            foreach ($stats as $val) {
                $statsel[$val->id] = $val->title;
            }
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::select('id', 'title')->where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Соглашения об условии продаж',
                'head' => 'Новая запись',
                'statsel' => $statsel,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
                'data' => $old,
            ];
            return view('workflow::agreement_edit', $data);
        }
        abort(404);
    }
}
