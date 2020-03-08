<?php

namespace Modules\HR\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\HR\Entities\Position;
use Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('hr_work')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочника должностей!');
        }
        if (view()->exists('hr::positions')) {
            $rows = Position::paginate(env('PAGINATION_SIZE'));
            $title = 'Справочник должностей';
            $data = [
                'title' => $title,
                'head' => 'Справочник должностей',
                'rows' => $rows,
            ];
            return view('hr::positions', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Role::granted('hr_work')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике должностей!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи в справочнике должностей!');
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
                return redirect()->route('positionAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $position = new Position();
            $position->fill($input);
            $position->created_at = date('Y-m-d');
            if($position->save()){
                $msg = 'Новая запись '. $input['title'] .' успешно добавлена в справочник должностей!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/positions')->with('status',$msg);
            }
        }
        if(view()->exists('hr::position_add')){
            $data = [
                'title' => 'Справочник должностей',
                'head' => 'Новая запись',
            ];
            return view('hr::position_add', $data);
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
        $model = Position::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('hr_work')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника должностей.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Запись '. $model->title .' была удалена из справочника должностей!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/positions')->with('status',$msg);
        }
        if(!Role::granted('hr_work')){
            $msg = 'Попытка редактирования записи '.$model->title.' в справочнике должностей.';
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
                'title' => 'required|string|max:100',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('positionEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные записи '. $model->title .' из справочника должностей обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/positions')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('hr::position_edit')){
            $data = [
                'title' => 'Справочник должностей',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('hr::position_edit',$data);
        }
        abort(404);
    }
}
