<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Hoperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class HoperationController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('hopers')){
            $rows = Hoperation::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Хозяйственные операции',
                'head' => 'Хозяйственные операции',
                'rows' => $rows,
            ];

            return view('hopers',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике хозопераций!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:100',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('hoperAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $hoper = new Hoperation();
            $hoper->fill($input);
            $hoper->created_at = date('Y-m-d');
            $hoper->user_id = Auth::id();
            if($hoper->save()){
                $msg = 'Новая хозоперация '. $input['title'] .' успешно добавлена в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('hopers')->with('status',$msg);
            }
        }
        if(view()->exists('hoper_add')){
            $data = [
                'title' => 'Хозяйственные операции',
                'head' => 'Новая запись',
            ];
            return view('hoper_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Hoperation::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника хозопераций.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Хозоперация '. $model->title .' была удалена из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/hopers')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике хозопераций.';
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
                'title' => 'required|string|max:50',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('hoperEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные хозоперации '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('hopers')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('hoper_edit')){
            $data = [
                'title' => 'Хозоперации',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('hoper_edit',$data);
        }
        abort(404);
    }
}
