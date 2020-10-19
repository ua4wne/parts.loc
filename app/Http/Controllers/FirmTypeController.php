<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\FirmType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class FirmTypeController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('typefirms')){
            $rows = FirmType::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Вид контрагента',
                'head' => 'Вид контрагента',
                'rows' => $rows,
            ];

            return view('typefirms',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике Вид контрагента!';
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
                return redirect()->route('firm_typeAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $firm_type = new FirmType();
            $firm_type->fill($input);
            $firm_type->created_at = date('Y-m-d');
            $firm_type->user_id = Auth::id();
            if($firm_type->save()){
                $msg = 'Вид контрагента '. $input['title'] .' успешно добавлен в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('typefirms')->with('status',$msg);
            }
        }
        if(view()->exists('firm_type_add')){
            $data = [
                'title' => 'Вид контрагента',
                'head' => 'Новая запись',
            ];
            return view('firm_type_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = FirmType::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника Вид контрагента.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Вид контрагента '. $model->title .' был удален из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/firm-types')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике Вид контрагента.';
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
                return redirect()->route('firm_typeEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные '. $model->title .' обновлены в справочнике Вид контрагента!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/firm-types')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('firm_type_edit')){
            $data = [
                'title' => 'Вид контрагента',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('firm_type_edit',$data);
        }
        abort(404);
    }
}
