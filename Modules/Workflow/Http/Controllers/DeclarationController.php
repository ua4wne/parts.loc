<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\Organisation;
use App\Models\Statuse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Unit;
use Modules\Workflow\Entities\Contract;
use Modules\Workflow\Entities\Declaration;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\TblDeclaration;
use Modules\Workflow\Entities\TblPurchase;
use Validator;

class DeclarationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('workflow::declarations')) {
            $rows = Declaration::all();
            $data = [
                'title' => 'Таможенные декларации',
                'head' => 'Таможенные декларации на импорт',
                'rows' => $rows,
            ];

            return view('workflow::declarations', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('declarations')) {//вызываем event
            $msg = 'Попытка создания новой декларации на импорт!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;
            $broker_id = Firm::where('name', $input['broker_id'])->first()->id;
            $input['broker_id'] = $broker_id;

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|unique:declarations|string|max:15',
                'declaration_num' => 'required|string|max:30',
                'organisation_id' => 'required|integer',
                'firm_id' => 'required|integer',
                'who_register' => 'required|in:broker,yourself',
                'broker_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'contract_id' => 'nullable|integer',
                'tax' => 'required|numeric',
                'fine' => 'nullable|numeric',
                'expense_id' => 'required|integer',
                'country_id' => 'required|integer',
                'cost' => 'required|numeric',
                'user_id' => 'required|integer',
                'rate' => 'required|numeric',
                'amount' => 'required|numeric',
                'vat' => 'required|numeric',
                'vat_amount' => 'required|numeric',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $declaration = new Declaration();
            $declaration->fill($input);
            $declaration->user_id = $input['user_id'];
            $declaration->created_at = date('Y-m-d H:i:s');
            if ($declaration->save()) {
                $msg = 'Декларация на импорт" № ' . $input['doc_num'] . ' была успешно добавлена!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/declarations')->with('status', $msg);
            }
        }
        if (view()->exists('workflow::declaration_add')) {
            $countries = Country::all();
            $contsel = array();
            foreach ($countries as $val) {
                $contsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::select('id', 'title')->where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $doc_num = LibController::GenNumberDoc('declarations');
            $exps = Expense::all();
            $expsel = array();
            foreach ($exps as $val) {
                $expsel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Таможенная декларация на импорт',
                'head' => 'Новый документ',
                'contsel' => $contsel,
                'usel' => $usel,
                'cursel' => $cursel,
                'expsel' => $expsel,
                'orgsel' => $orgsel,
                'doc_num' => $doc_num,
            ];
            return view('workflow::declaration_add', $data);
        }
        abort(404);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        if (view()->exists('workflow::declaration_view')) {
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $countries = Country::all();
            $contrsel = array();
            foreach ($countries as $val) {
                $contrsel[$val->id] = $val->title;
            }
            $curs = Currency::all();
            $cursel = array();
            foreach ($curs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $orgs = Organisation::select('id', 'title')->where(['status' => 1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val->id] = $val->title;
            }
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $exps = Expense::all();
            $expsel = array();
            foreach ($exps as $val) {
                $expsel[$val->id] = $val->title;
            }
            $declaration = Declaration::find($id);
            $contracts = Contract::select('id', 'title')->where(['firm_id' => $declaration->broker_id, 'organisation_id' => $declaration->organisation_id])->get();
            $contsel = array();
            foreach ($contracts as $val) {
                $contsel[$val->id] = $val->title;
            }
            $rows = TblDeclaration::where('declaration_id', $id)->get();

            $data = [
                'title' => 'Таможенная декларация на импорт',
                'head' => 'Таможенная декларация на импорт № ' . $declaration->doc_num,
                'usel' => $usel,
                'unsel' => $unsel,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
                'contsel' => $contsel,
                'contrsel' => $contrsel,
                'expsel' => $expsel,
                'declaration' => $declaration,
                'rows' => $rows,
            ];
            return view('workflow::declaration_view', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        $declaration = Declaration::find($id);
        if (!Role::granted('declarations')) {//вызываем event
            $msg = 'Попытка редактирования таможенной декларации №' . $declaration->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование документов таможенных деклараций!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm_id = Firm::where('name', $input['firm_id'])->first()->id;
            $input['firm_id'] = $firm_id;
            $broker_id = Firm::where('name', $input['broker_id'])->first()->id;
            $input['broker_id'] = $broker_id;

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|string|max:15',
                'declaration_num' => 'required|string|max:30',
                'organisation_id' => 'required|integer',
                'firm_id' => 'required|integer',
                'who_register' => 'required|in:broker,yourself',
                'broker_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'contract_id' => 'nullable|integer',
                'tax' => 'required|numeric',
                'fine' => 'nullable|numeric',
                'expense_id' => 'required|integer',
                'country_id' => 'required|integer',
                'cost' => 'required|numeric',
                'user_id' => 'required|integer',
                'rate' => 'required|numeric',
                'amount' => 'required|numeric',
                'vat' => 'required|numeric',
                'vat_amount' => 'required|numeric',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $declaration->fill($input);
            if ($declaration->update()) {
                $msg = 'Данные таможенной декларации № ' . $declaration->doc_num . ' были успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public function findPurchases()
    {
        $content = '';
        $closed = Statuse::where('title', 'Закрыт')->first()->id;
        $docs = DB::select("SELECT id, doc_num, created_at from purchases WHERE statuse_id != $closed
                                    AND id NOT IN (SELECT purchase_id FROM tbl_declarations)");
        foreach ($docs as $val) {
            $content .= '<option value="' . $val->id . '">' . $val->doc_num . ' ' . $val->created_at . '</option>' . PHP_EOL;
        }
        return $content;
    }

    public function getPurchases(Request $request)
    {
        $content = '';
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $docs = TblDeclaration::select('purchase_id')->where('declaration_id', $input['id'])->distinct()->get();
            foreach ($docs as $val) {
                $content .= '<option value="' . $val->purchase->id . '">' . $val->purchase->doc_num . ' ' . $val->purchase->created_at . '</option>' . PHP_EOL;
            }
        }
        return $content;
    }

    public function addPosition(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            //dd($input);
            $purchase_id = $input['from_docs'];
            $declaration_id = $input['declaration_id'];

            $content = '<thead>
                                        <tr>
                                            <th>Номенклатура</th>
                                            <th>Характеристика</th>
                                            <th>Кол-во</th>
                                            <th>Ед.изм</th>
                                            <th>Таможенная стоимость</th>
                                            <th>Сумма пошлины</th>
                                            <th>Сумма НДС</th>
                                            <th>Документ-основание</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>';
            //$amount = 0;
            if (!empty($purchase_id) && !empty($declaration_id)) {
                foreach ($purchase_id as $id) {
                    $pos = TblPurchase::where('purchase_id', $id)->get();
                    if (!empty($pos)) {
                        foreach ($pos as $val) {
                            $new = new TblDeclaration();
                            $new->declaration_id = $declaration_id;
                            $new->purchase_id = $val->purchase_id;
                            $new->good_id = $val->sub_good_id;
                            $new->qty = $val->qty;
                            $new->unit_id = $val->unit_id;
                            $new->comment = $val->comment;
                            $new->amount = 0;
                            $new->vat = 0;
                            $new->duty = 0;
                            $new->save();
                        }
                    }
                }
            }
            //делаем пересчет сумм
            $declaration = Declaration::find($declaration_id);
            $amount = $declaration->cost;
            $duty = $declaration->amount;
            $vat = $declaration->vat_amount;
            $this->DistributeCost($declaration_id,$duty,$amount,$vat);
            //выбираем табличную часть декларации
            $rows = TblDeclaration::where('declaration_id', $declaration_id)->get();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $content .= '<tr id="' . $row->id . '">
                                        <td>' . $row->good->title . '</td>
                                        <td>' . $row->comment . '</td>
                                        <td>' . $row->qty . '</td>
                                        <td>' . $row->unit->title . '</td>
                                        <td>' . $row->amount . '</td>
                                        <td>' . $row->duty . '</td>
                                        <td>' . $row->vat . '</td>
                                        <td><a href="/purchases/view/' . $row->purchase_id . '" target="_blank">' . $row->purchase->doc_num . ' от ' . $row->purchase->created_at . '</a></td>
                                        <td style="width:70px;">
                                                        <div class="form-group" role="group">
                                                            <button class="btn btn-info btn-sm pos_edit"
                                                                    type="button" title="Редактировать позицию"><i
                                                                    class="fa fa-edit fa-lg" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                    </tr>';
                }
            }
            return $content;
        }
        return 'NO';
    }

    public function PosEdit(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $pos = TblDeclaration::find($input['pos_id']);
            if (!empty($pos)) {
                unset($input['pos_id']);
                $pos->fill($input);
                if ($pos->update()) {
                    return 'OK';
                }
                return 'ERR';
            }
        }
        return 'NO';
    }

    public function delPurchasePos(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['declaration_id'];
            $docs = $input['id_docs'];
            if (!empty($id) && !empty($docs)) {
                foreach ($docs as $doc) {
                    $tbl = TblDeclaration::where(['declaration_id' => $id, 'purchase_id' => $doc])->get();
                    if (!empty($tbl)) {
                        foreach ($tbl as $row) {
                            $row->delete();
                        }
                    }
                }
                //делаем пересчет сумм
                $declaration = Declaration::find($id);
                $amount = $declaration->cost;
                $duty = $declaration->amount;
                $vat = $declaration->vat_amount;
                if (!empty($id) && !empty($duty) && !empty($amount) && !empty($vat)) {
                    $this->DistributeCost($id,$duty,$amount,$vat);
                }
            }
            return 'OK';
        }
        return 'NO';
    }

    public function CostAllocation(Request $request)
    {
        if (!Role::granted('purchases')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $amount = $input['cost'];
            $duty = $input['amount'];
            $vat = $input['vat_amount'];
            if (!empty($id) && !empty($duty) && !empty($amount) && !empty($vat)) {
                if($this->DistributeCost($id,$duty,$amount,$vat))
                    return 'OK';
            }
        }
        return 'NO';
    }

    private function DistributeCost($id,$duty,$amount,$vat){
        $tbl = TblDeclaration::where('declaration_id', $id)->orderBy('qty', 'desc')->get();
        $sum = TblDeclaration::where('declaration_id', $id)->sum('qty');
        if (!empty($tbl) && !empty($sum)) {
            $comm_amount = 0;
            $comm_duty = 0;
            $comm_vat = 0;
            foreach ($tbl as $row) {
                $row->amount = round(($row->qty / $sum) * $amount, 2);
                $row->duty = round(($row->qty / $sum) * $duty, 2);
                $row->vat = round(($row->qty / $sum) * $vat, 2);
                $row->update();
                $comm_amount += $row->amount;
                $comm_duty += $row->duty;
                $comm_vat += $row->vat;
            }
            //делаем корректировку, устраняя ошибки округления сумм
            $row = TblDeclaration::where('purchase_id', $id)->orderBy('qty', 'desc')->first();
            if ($comm_amount > $amount) {
                $row->amount += $comm_amount - $amount;
            }
            if ($comm_amount < $amount) {
                $row->amount += $amount - $comm_amount;
            }
            if ($comm_duty > $duty) {
                $row->duty += $comm_duty - $duty;
            }
            if ($comm_duty < $duty) {
                $row->duty += $duty - $comm_duty;
            }
            if ($comm_vat > $vat) {
                $row->vat += $comm_vat - $vat;
            }
            if ($comm_vat < $vat) {
                $row->vat += $vat - $comm_vat;
            }
            $row->update();
            return true;
        }
        return false;
    }
}
