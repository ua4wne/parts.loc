<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Statuse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class StatusController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('stats')){
            $rows = Statuse::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Статусы',
                'head' => 'Статусы документов',
                'rows' => $rows,
            ];

            return view('stats',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!User::hasRole('admin')){//вызываем event
            $msg = 'Попытка создания нового статуса документа!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи. Создавать и изменять статусы может только администратор!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:30',
                'style' => 'required|string|max:150',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('statusAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $status = new Statuse();
            $status->fill($input);
            $status->created_at = date('Y-m-d');
            $status->user_id = Auth::id();
            if($status->save()){
                $msg = 'Новый статус '. $input['title'] .' успешно добавлен в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('stats')->with('status',$msg);
            }
        }
        if(view()->exists('status_add')){
            $data = [
                'title' => 'Статусы документов',
                'head' => 'Новая запись',
            ];
            return view('status_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Statuse::find($id);
        if($request->isMethod('delete')){
            if(!User::hasRole('admin')){
                $msg = 'Попытка удаления статуса '.$model->title.' из справочника.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Статус '. $model->title .' был удален из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/stats')->with('status',$msg);
        }
        if(!User::hasRole('admin')){
            $msg = 'Попытка редактирования статуса '.$model->title.' в справочнике.';
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
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:30',
                'style' => 'required|string|max:150',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('statusEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные статуса '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('stats')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('status_edit')){
            $data = [
                'title' => 'Статусы документов',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('status_edit',$data);
        }
        abort(404);
    }
}
