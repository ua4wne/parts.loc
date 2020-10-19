<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Currency;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Workflow\Entities\Bank;
use Modules\Workflow\Entities\BankAccount;
use Modules\Workflow\Entities\Firm;
use Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        if(view()->exists('workflow::bank_accounts')){
            $rows = BankAccount::where(['firm_id'=>$id])->get();
            $data = [
                'title' => 'Банковские счета',
                'id' => $id,
                'firm' => Firm::find($id)->title,
                'head' => 'Банковские счета контрагента',
                'rows' => $rows,
            ];

            return view('workflow::bank_accounts',$data);
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
            $msg = 'Попытка создания нового банковского счета для контрагента!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //определяем банк по бику или swift
            if(!empty($input['bik']))
                $input['bank_id'] = Bank::where(['bik'=>$input['bik']])->first()->id;
            if(!empty($input['swift']))
                $input['bank_id'] = Bank::where(['swift'=>$input['swift']])->first()->id;
            if(empty($input['status']))
                $input['status'] = 0;
            if(empty($input['is_main']))
                $input['is_main'] = 0;
            if(empty($input['for_pay']))
                $input['for_pay'] = 0;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input,[
                'bik' => 'nullable|string|max:10',
                'swift' => 'nullable|string|max:15',
                'title' => 'required|string|max:100',
                'account' => 'required|string|max:25',
                'currency_id' => 'required|integer',
                'status' => 'nullable|integer',
                'is_main' => 'nullable|integer',
                'for_pay' => 'nullable|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('bank_accountAdd',['id'=>$id])->withErrors($validator)->withInput();
            }

            $acc = new BankAccount();
            $acc->title = $input['title'];
            $acc->firm_id = $id;
            $acc->bank_id = $input['bank_id'];
            $acc->account = $input['account'];
            $acc->currency_id = $input['currency_id'];
            $acc->status = $input['status'];
            $acc->is_main = $input['is_main'];
            $acc->for_pay = $input['for_pay'];
            $acc->created_at = date('Y-m-d');

            if($acc->save()){
                $msg = 'Банковский счет '. $input['title'] .' для контрагента '.Firm::find($id)->title.' был успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('bank_accounts',['id'=>$id])->with('status',$msg);
            }
        }
        if(view()->exists('workflow::bank_account_add')){
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $foreinger = Firm::find($id)->foreinger;
            $data = [
                'head' => 'Новая запись',
                'title' => 'Банковские счета',
                'firm' => Firm::find($id)->title,
                'id' => $id,
                'cursel' => $cursel,
                'foreinger' => $foreinger,
            ];
            return view('workflow::bank_account_add', $data);
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
        $model = BankAccount::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('fin_edit')){
                $msg = 'Попытка удаления банковского счета '.$model->title.' '.$model->account;
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Банковский счет '. $model->title.' '.$model->account .' был удален!';
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect()->route('bank_accounts',['id'=>$model->firm->id])->with('status',$msg);
        }
        if(!Role::granted('fin_edit')){
            $msg = 'Попытка редактирования банковского счета '.$model->title.' '.$model->account;
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на редактирование записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            if(empty($input['status']))
                $input['status'] = 0;
            if(empty($input['is_main']))
                $input['is_main'] = 0;
            if(empty($input['for_pay']))
                $input['for_pay'] = 0;
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно быть числовым!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:100',
                'account' => 'required|string|max:25',
                'currency_id' => 'required|integer',
                'status' => 'nullable|integer',
                'is_main' => 'nullable|integer',
                'for_pay' => 'nullable|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('bank_accountEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные банковского счета '.$model->title.' '.$model->account .' обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('bank_accounts',['id'=>$model->firm->id])->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if(view()->exists('workflow::bank_account_edit')){
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $data = [
                'head' => 'Редактирование счета '.$old['title'],
                'title' => 'Банковские счета',
                'firm' => Firm::find($model->firm_id)->title,
                'bank' => Bank::find($model->bank_id)->title,
                'cursel' => $cursel,
                'data' => $old
            ];
            return view('workflow::bank_account_edit',$data);
        }
        abort(404);
    }

    public function findOrgAcc(Request $request) {
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $content = '';
            if(isset($input['org_id'])){
                $bacc = Organisation::find($input['org_id']);
                //foreach ($baccs as $val) {
                    $content .= '<option value="' . $bacc->account . '">' . $bacc->account . '</option>' . PHP_EOL;
                //}
            }
            return $content;
        }
    }
}
