<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class ExpenseController extends Controller
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
        if(view()->exists('expenses')){
            $rows = Expense::orderBy('title','asc')->paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Статьи затрат',
                'head' => 'Справочник статей затрат',
                'rows' => $rows,
            ];

            return view('expenses',$data);
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
            $msg = 'Попытка создания новой статьи затрат!';
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
            ];
            $validator = Validator::make($input,[
                'code' => 'required|unique:expenses|string|max:10',
                'title' => 'required|unique:currency|max:50',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //dd($input);
            $expense = new Expense();
            $expense->fill($input);
            $expense->created_at = date('Y-m-d');
            if($expense->save()){
                $msg = 'Новая статья затрат '. $input['title'] .' успешно добавлена в справочник!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('expenses')->with('status',$msg);
            }
        }
        if(view()->exists('expense_add')){
            $data = [
                'title' => 'Статьи затрат',
                'head' => 'Новая запись',
            ];
            return view('expense_add', $data);
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
        $model = Expense::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('delete_refs')){
                $msg = 'Попытка удаления записи '.$model->title.' из справочника статей затрат.';
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Статья затрат '. $model->title .' была удалена из справочника!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/expenses')->with('status',$msg);
        }
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования статьи затрат '.$model->title.' в справочнике.';
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
                'unique' => 'Значение поля должно быть уникальным!',
            ];
            $validator = Validator::make($input,[
                'code' => 'required|unique:expenses|string|max:10',
                'title' => 'required|unique:currency|max:50',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $model->fill($input);
            if($model->update()){
                $msg = 'Данные статьи затрат '. $model->title .' обновлены в справочнике!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('expenses')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('expense_edit')){
            $data = [
                'title' => 'Статьи затрат',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old,
            ];
            return view('expense_edit',$data);
        }
        abort(404);
    }
}
