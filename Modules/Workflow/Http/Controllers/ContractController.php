<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Currency;
use App\Models\DeliveryMethod;
use App\Models\Organisation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Workflow\Entities\BankAccount;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Firm;
use Validator;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        if(view()->exists('workflow::contracts')){
            $rows = Contract::where(['firm_id'=>$id])->get();
            $data = [
                'title' => 'Договоры',
                'id' => $id,
                'firm' => Firm::find($id)->title,
                'head' => 'Договоры с контрагентом',
                'rows' => $rows,
            ];

            return view('workflow::contracts',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id,Request $request)
    {
        if(!Role::granted('fin_edit')){//вызываем event
            $msg = 'Попытка создания нового договора с контрагентом '.Firm::find($id)->title.'!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //dd($input);
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input,[
                'status' => 'required|integer',
                'doc_num' => 'required|string|max:15',
                'title' => 'required|string|max:150',
                'print_title' => 'nullable|string|max:150',
                'start' => 'nullable|date',
                'finish' => 'nullable|date',
                'type' => 'required|string|max:100',
                'organisation_id' => 'required|integer',
                'org_acc' => 'nullable|string|max:25',
                'firm_id' => 'required|integer',
                'firm_acc' => 'nullable|string|max:25',
                'user_id' => 'required|integer',
                'uip' => 'nullable|string|max:100',
                'gosid' => 'nullable|string|max:100',
                'delivery_method' => 'nullable|string|max:150',
                'currency_id' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('contractAdd',['id'=>$id])->withErrors($validator)->withInput();
            }
            $contract = new Contract();
            $contract->fill($input);
            $contract->created_at = date('Y-m-d H:i:s');
            if ($contract->save()) {
                $msg = 'Новый договор № ' . $input['doc_num'] . ' с контрагентом ' . Firm::find($id)->title . ' успешно создан!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('contracts',['id'=>$id])->with('status', $msg);
            }
        }
        if(view()->exists('workflow::contract_add')){
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
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $baccs = BankAccount::where(['firm_id' => $id,'status'=>1])->orderBy('is_main','asc')->get();
            $basel = array();
            foreach ($baccs as $val) {
                $basel[$val->id] = $val->title;
            }

            $data = [
                'head' => 'Новая запись',
                'title' => 'Договоры',
                'firm' => Firm::find($id)->title,
                'basel' => $basel,
                'id' => $id,
                'orgsel' => $orgsel,
                'cursel' => $cursel,
                'usel' => $usel,
            ];
            return view('workflow::contract_add', $data);
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
        $model = Contract::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('fin_edit')){
                $msg = 'Попытка удаления договора '.$model->title.' с контрагентом '.$model->firm->title;
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Договор '. $model->title.' с контрагентом '. $model->firm->title .' был удален!';
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect()->route('contracts',['id'=>$model->firm->id])->with('status',$msg);
        }
        if(!Role::granted('fin_edit')){
            $msg = 'Попытка редактирования договора '.$model->title .' с контрагентом '.$model->firm->title;
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на редактирование записи!');
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //dd($input);
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input,[
                'status' => 'required|integer',
                'doc_num' => 'required|string|max:15',
                'title' => 'required|string|max:150',
                'print_title' => 'nullable|string|max:150',
                'start' => 'nullable|date',
                'finish' => 'nullable|date',
                'type' => 'required|string|max:100',
                'organisation_id' => 'required|integer',
                'org_acc' => 'nullable|string|max:25',
                'firm_id' => 'required|integer',
                'firm_acc' => 'nullable|string|max:25',
                'user_id' => 'required|integer',
                'uip' => 'nullable|string|max:100',
                'gosid' => 'nullable|string|max:100',
                'delivery_method' => 'nullable|string|max:150',
                'currency_id' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('contractAdd',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if ($model->update()) {
                $msg = 'Данные договора № ' . $input['doc_num'] . ' с контрагентом ' . Firm::find($model->firm_id)->title . ' были обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->route('contracts',['id'=>$model->firm_id])->with('status', $msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('workflow::contract_edit')){
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $baccs = BankAccount::where(['firm_id' => $model->firm_id,'status'=>1])->orderBy('is_main','asc')->get();
            $basel = array();
            foreach ($baccs as $val) {
                $basel[$val->id] = $val->title;
            }

            $data = [
                'head' => 'Редактирование договора '.$old['title'],
                'title' => 'Договоры с контрагентом '.$model->firm->title,
                'firm' => $model->firm->title,
                'org' => $model->organisation->title,
                'basel' => $basel,
                'id' => $id,
                'cursel' => $cursel,
                'usel' => $usel,
                'data' => $old
            ];
            return view('workflow::contract_edit', $data);
        }
        abort(404);
    }
}
