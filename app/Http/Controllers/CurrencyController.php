<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class CurrencyController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('currency')){
            $rows = Currency::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Валюты',
                'head' => 'Справочник валют',
                'rows' => $rows,
            ];

            return view('currency',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике валют!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'unique' => 'Значение поля должно быть уникальным!',
                'numeric' => 'Значение поля должно содержать только цифры!',
            ];
            $validator = Validator::make($input,[
                'dcode' => 'required|unique:currency|string|max:5',
                'scode' => 'required|unique:currency|string|max:5',
                'unit' => 'required|numeric',
                'title' => 'required|unique:currency|max:100',
                'cource' => 'required|numeric'
            ],$messages);
            if($validator->fails()){
                return redirect()->route('currencyAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $currency = new Currency();
            $currency->fill($input);
            $currency->created_at = date('Y-m-d');
            if($currency->save()){
                $msg = 'Новая валюта '. $input['title'] .' успешно добавлена в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('currency')->with('status',$msg);
            }
        }
        if(view()->exists('currency_add')){
            $data = [
                'title' => 'Валюты',
                'head' => 'Новая запись',
            ];
            return view('currency_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Currency::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника валют.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Валюта '. $model->title .' была удалена из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/currency')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике валют.';
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на редактирование записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'numeric' => 'Значение поля должно содержать только цифры!',
            ];
            $validator = Validator::make($input,[
                'unit' => 'required|numeric',
                'cource' => 'required|numeric'
            ],$messages);
            if($validator->fails()){
                return redirect()->route('currencyEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные по валюте '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('currency')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('currency_edit')){
            $data = [
                'title' => 'Валюты',
                'head' => 'Редактирование валюты '.$old['title'],
                'data' => $old,
            ];
            return view('currency_edit',$data);
        }
        abort(404);
    }
}
