<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class CarController extends Controller
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
        if(view()->exists('cars')){
            $rows = Car::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Автотранспорт',
                'head' => 'Транспортные средства',
                'rows' => $rows,
            ];

            return view('cars',$data);
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
            $msg = 'Попытка создания нового ТС!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'unique' => 'Значение поля должно быть уникальным!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|unique:cars|max:150',
                'descr' => 'nullable|string|max:254',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //dd($input);
            $car = new Car();
            $car->fill($input);
            $car->created_at = date('Y-m-d');
            if($car->save()){
                $msg = 'Новое ТС '. $input['title'] .' успешно добавлено в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('cars')->with('status',$msg);
            }
        }
        if(view()->exists('car_add')){
            $data = [
                'title' => 'Автотранспорт',
                'head' => 'Новая запись',
            ];
            return view('car_add', $data);
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
        $model = Car::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника ТС.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'ТС '. $model->title .' было удалено из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/cars')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике ТС.';
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
                'title' => 'required|string|max:150',
                'descr' => 'nullable|string|max:254',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $model->fill($input);
            if($model->update()){
                $msg = 'Данные ТС '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('cars')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('car_edit')){
            $data = [
                'title' => 'Автотранспорт',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('car_edit',$data);
        }
        abort(404);
    }
}
