<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Organisation;
use App\Models\OrgForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class OrganisationController extends Controller
{
    public function index(){
        if (view()->exists('orgs')) {
            $rows = Organisation::paginate(env('PAGINATION_SIZE'));
            $title = 'Организации';
            $data = [
                'title' => $title,
                'head' => 'Наши организации',
                'rows' => $rows,
            ];
            return view('orgs', $data);
        }
        abort(404);
    }

    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания новой организации!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:150',
                'org_form_id' => 'required|integer',
                'print_name' => 'nullable|string|max:150',
                'short_name' => 'nullable|string|max:100',
                'inn' => 'nullable|string|max:12',
                'ogrn' => 'nullable|string|max:15',
                'kpp' => 'nullable|string|max:9',
                'status' => 'required|integer|max:127',
                'prefix' => 'nullable|string|max:10',
                'account' => 'nullable|string|max:25',
                'legal_address' => 'nullable|string|max:255',
                'post_address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'e-mail' => 'nullable|email|max:30',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('orgAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $org = new Organisation();
            $org->fill($input);
            $org->created_at = date('Y-m-d');
            if($org->save()){
                $msg = 'Новая организация '. $input['title'] .' успешно добавлена в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('orgs')->with('status',$msg);
            }
        }
        if(view()->exists('org_add')){
            //выбираем все объекты
            $objects = OrgForm::all();
            $orgsel = array();
            foreach ($objects as $object){
                $orgsel[$object->id] = $object->title;
            }
            $data = [
                'title' => 'Организации',
                'head' => 'Новая запись',
                'orgsel' => $orgsel,
            ];
            return view('org_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Organisation::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника организаций.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Запись '. $model->title .' была удалена из справочника организаций!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect()->route('orgs')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике организаций.';
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
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:150',
                'org_form_id' => 'required|integer',
                'print_name' => 'nullable|string|max:150',
                'short_name' => 'nullable|string|max:100',
                'inn' => 'nullable|string|max:12',
                'ogrn' => 'nullable|string|max:15',
                'kpp' => 'nullable|string|max:9',
                'status' => 'required|integer|max:127',
                'prefix' => 'nullable|string|max:10',
                'account' => 'nullable|string|max:25',
                'legal_address' => 'nullable|string|max:255',
                'post_address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'e-mail' => 'nullable|email|max:30',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('orgEdit')->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные организации '. $model->title .' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('orgs')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('org_edit')){
            //выбираем все объекты
            $objects = OrgForm::all();
            $orgsel = array();
            foreach ($objects as $object){
                $orgsel[$object->id] = $object->title;
            }
            $data = [
                'title' => 'Организационные формы',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
                'orgsel' => $orgsel,
            ];
            return view('org_edit',$data);
        }
        abort(404);
    }

    public function view($id){
        return 'в разработке!';
    }
}
