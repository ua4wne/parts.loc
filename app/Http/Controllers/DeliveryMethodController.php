<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\DeliveryMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class DeliveryMethodController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('methods')){
            $rows = DeliveryMethod::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Способы доставки',
                'head' => 'Способы доставки',
                'rows' => $rows,
            ];

            return view('methods',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике способов доставки!';
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
                return redirect()->route('methodAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $delivery = new DeliveryMethod();
            $delivery->fill($input);
            $delivery->created_at = date('Y-m-d');
            $delivery->user_id = Auth::id();
            if($delivery->save()){
                $msg = 'Новый способ доставки '. $input['title'] .' успешно добавлен в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('methods')->with('status',$msg);
            }
        }
        if(view()->exists('method_add')){
            $data = [
                'title' => 'Способы доставки',
                'head' => 'Новая запись',
            ];
            return view('method_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = DeliveryMethod::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника способов доставки.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Способ доставки '. $model->title .' был удален из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect()->route('methods')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике способов доставки.';
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
                return redirect()->route('methodEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные способа доставки '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('methods')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('method_edit')){
            $data = [
                'title' => 'Способы доставки',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('method_edit',$data);
        }
        abort(404);
    }
}
