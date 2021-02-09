<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Category;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Group;
use Modules\Workflow\Entities\Agreement;
use Modules\Workflow\Entities\PricingRule;
use Validator;

class PricingRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('view_price_rules')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников!');
        }
        if (view()->exists('workflow::pricing_rules')) {
            $rows = PricingRule::paginate(env('PAGINATION_SIZE'));
            $title = 'Правила ценообразования';
            $data = [
                'title' => $title,
                'head' => 'Правила образования цен на товары',
                'rows' => $rows,
            ];
            return view('workflow::pricing_rules', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Role::granted('price_edit')){//вызываем event
            $msg = 'Попытка создания нового правила ценообразования!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание правила!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            //тут приходит артикул, превращаем его в ID товара
            if(!empty($input['good_id'])){
                $input['good_id'] = Good::where('vendor_code',$input['good_id'])->first()->id;
            }
            else{
                $input['good_id'] = null;
            }
            if(empty($input['category_id'])){
                $input['category_id'] = null;
            }
            $input['user_id'] = Auth::id();

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'numeric' => 'Значение поля должно быть числом!',
                'integer' => 'Значение поля должно быть целым числом!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:100',
                'ratio' => 'required|numeric',
                'price_type' => 'required|in:retail,wholesale,small',
                'currency_id' => 'required|integer',
                'user_id' => 'required|integer',
                'category_id' => 'nullable|integer',
                'agreement_id' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if(!empty($input['category_id'])){
                // есть такая запись или нет
                PricingRule::updateOrCreate(['category_id' => $input['category_id'],'price_type'=> $input['price_type'],'currency_id'=>$input['currency_id']],
                    ['title' => $input['title'],'ratio' => $input['ratio'],'user_id' => $input['user_id'],'agreement_id'=>$input['agreement_id']]);
            }
            else{
                //dd($input);
                $rule = new PricingRule();
                $rule->fill($input);
                $rule->created_at = date('Y-m-d H:i:s');
                $rule->save();
            }
            $msg = 'Новое правило ценообразования '. $input['title'] .' успешно добавлено!';
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/pricing_rules')->with('status',$msg);
        }
        if(view()->exists('workflow::price_rule_add')){
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $cats = Category::all();
            $catsel = array();
            foreach ($cats as $val) {
                $catsel[$val->id] = $val->category;
            }
            $agrs = Agreement::whereNull('finish')->get();
            $agrsel = array();
            foreach ($agrs as $val) {
                $agrsel[$val->id] = $val->title.' ('.$val->organisation->title.')';
            }
            $data = [
                'title' => 'Правила ценообразования',
                'head' => 'Новое правило',
                'cursel' => $cursel,
                'catsel' => $catsel,
                'agrsel' => $agrsel,
            ];
            return view('workflow::price_rule_add', $data);
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
        $model = PricingRule::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('price_edit')){
                $msg = 'Попытка удаления правила ценообразования '.$model->title;
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $msg = 'Правило ценообразования '. $model->title .' было удалено!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/pricing_rules')->with('status',$msg);
        }
        if(!Role::granted('price_edit')){
            $msg = 'Попытка редактирования правила ценообразования '.$model->title;
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
                'numeric' => 'Значение поля должно быть числом!',
                'integer' => 'Значение поля должно быть целым числом!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:100',
                'ratio' => 'required|numeric',
                'price_type' => 'required|in:retail,wholesale,small',
                'currency_id' => 'required|integer',
                'agreement_id' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Правило ценообразования '. $model->title .' было обновлено!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/pricing_rules')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('workflow::price_rule_edit')){
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $cats = Category::all();
            $catsel = array();
            foreach ($cats as $val) {
                $catsel[$val->id] = $val->category;
            }
            $agrs = Agreement::whereNull('finish')->get();
            $agrsel = array();
            foreach ($agrs as $val) {
                $agrsel[$val->id] = $val->title.' ('.$val->organisation->title.')';
            }
            $data = [
                'title' => 'Правила ценообразования',
                'head' => 'Редактирование правила '.$old['title'],
                'cursel' => $cursel,
                'catsel' => $catsel,
                'agrsel' => $agrsel,
                'data' => $old,
            ];
            return view('workflow::price_rule_edit',$data);
        }
        abort(404);
    }
}
