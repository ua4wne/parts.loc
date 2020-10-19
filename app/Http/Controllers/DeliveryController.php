<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class DeliveryController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('deliveries')){
            $rows = Delivery::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Транспортные компании',
                'head' => 'Транспортные компании',
                'rows' => $rows,
            ];

            return view('deliveries',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике транспортных компаний!';
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
                return redirect()->route('deliveryAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $delivery = new Delivery();
            $delivery->fill($input);
            $delivery->created_at = date('Y-m-d');
            $delivery->user_id = Auth::id();
            if($delivery->save()){
                $msg = 'Новая транспортная компания '. $input['title'] .' успешно добавлена в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('deliveries')->with('status',$msg);
            }
        }
        if(view()->exists('delivery_add')){
            $data = [
                'title' => 'Транспортные компании',
                'head' => 'Новая запись',
            ];
            return view('delivery_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Delivery::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления транспортной компании '.$model->title.' из справочника.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Транспортная компания '. $model->title .' была удалена из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect()->route('deliveries')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике транспортных компаний.';
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
                return redirect()->route('deliveryEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные транспортной компании '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('deliveries')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('delivery_edit')){
            $data = [
                'title' => 'Транспортные компании',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('delivery_edit',$data);
        }
        abort(404);
    }
}
