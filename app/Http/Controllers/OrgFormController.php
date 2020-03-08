<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\OrgForm;
use Modules\Admin\Entities\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class OrgFormController extends Controller
{
    public function index(){
        if(!Role::granted('view_refs')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if (view()->exists('orgforms')) {
            $rows = OrgForm::paginate(env('PAGINATION_SIZE'));
            $title = 'Организационные формы';
            $data = [
                'title' => $title,
                'head' => 'Организационные формы для юрлиц',
                'rows' => $rows,
            ];
            return view('orgforms', $data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике организационных форм!';
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
                'nameRU' => 'required|string|max:5',
                'nameEN' => 'nullable|string|max:5',
                'title' => 'required|string|max:100',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('orgformAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $orgform = new OrgForm();
            $orgform->fill($input);
            $orgform->created_at = date('Y-m-d');
            if($orgform->save()){
                $msg = 'Новая форма '. $input['nameRU'] .' успешно добавлена в справочник организационных форм!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/orgforms')->with('status',$msg);
            }
        }
        if(view()->exists('orgform_add')){
            $data = [
                'title' => 'Организационные формы',
                'head' => 'Новая запись',
            ];
            return view('orgform_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = OrgForm::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника организационных форм.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Запись '. $model->title .' была удалена из справочника организационных форм!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/orgforms')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике организационных форм.';
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
                'nameRU' => 'required|string|max:5',
                'nameEN' => 'nullable|string|max:5',
                'title' => 'required|string|max:100',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('orgformEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные записи '. $model->title .' из справочника организационных форм обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/orgforms')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('orgform_edit')){
            $data = [
                'title' => 'Организационные формы',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('orgform_edit',$data);
        }
        abort(404);
    }
}
