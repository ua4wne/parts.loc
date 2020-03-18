<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Currency;
use App\Models\Organisation;
use App\Models\Price;
use App\Models\PriceTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use Validator;

class PriceController extends Controller
{
    public function index(){
        if (view()->exists('prices')) {
            $rows = Price::paginate(env('PAGINATION_SIZE'));
            $title = 'Прайсы';
            $data = [
                'title' => $title,
                'head' => 'Прайсы',
                'rows' => $rows,
            ];
            return view('prices', $data);
        }
        abort(404);
    }

    public function create(Request $request)
    {
        if (!Role::granted('price_edit')) {//вызываем event
            $msg = 'Попытка создания нового прайса!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание прайсов!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
                'currency_id' => 'required|integer',
                'organisation_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('priceAdd')->withErrors($validator)->withInput();
            }
            //dd($input);
            $price = new Price();
            $price->fill($input);
            $price->user_id = Auth::id();
            $price->created_at = date('Y-m-d');
            if($price->save()){
                $msg = 'Новый прайс ' . $input['title'] . ' успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('prices')->with('status', $msg);
            }
        }
        if (view()->exists('price_add')) {
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::where(['status'=>1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Номенклатура',
                'head' => 'Новая запись',
                'cursel' => $cursel,
                'orgsel' => $orgsel,
            ];
            return view('price_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request){
        $model = Price::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('price_edit')){
                $msg = 'Попытка удаления прайса '.$model->title;
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление прайсов!');
            }
            PriceTable::where(['price_id'=>$model->id])->delete();
            $msg = 'Прайс '. $model->title .' был удален со всем своим содержимым!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/prices')->with('status',$msg);
        }
        if(!Role::granted('price_edit')){
            $msg = 'Попытка редактирования прайса '.$model->title;
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на редактирование прайсов!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|string|max:100',
                'descr' => 'nullable|string|max:255',
                'currency_id' => 'required|integer',
                'organisation_id' => 'required|integer',
            ], $messages);
            if($validator->fails()){
                return redirect()->route('priceEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные прайса '. $model->title .' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('prices')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('price_edit')){
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::where(['status'=>1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Прайсы',
                'head' => 'Редактирование прайса '.$old['title'],
                'data' => $old,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
            ];
            return view('price_edit',$data);
        }
        abort(404);
    }
}
