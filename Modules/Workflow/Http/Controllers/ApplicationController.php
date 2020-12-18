<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Car;
use App\Models\Currency;
use App\Models\Priority;
use App\Models\Statuse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Unit;
use Modules\Workflow\Entities\Application;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Sale;
use Modules\Workflow\Entities\TblApplication;
use Validator;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('workflow::applications')) {
            $title = 'Заявки менеджеру';
            $rows = Application::orderBy('rank','desc')->get();
            $data = [
                'title' => $title,
                'head' => 'Заявки менеджеру',
                'rows' => $rows,
            ];
            return view('workflow::applications', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            $msg = 'Попытка создания новой заявки менеджеру!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $sale_id = Sale::where('doc_num', $input['sale_id'])->first()->id;
            $input['sale_id'] = $sale_id;

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|string|max:15',
                'priority_id' => 'required|integer',
                'statuse_id' => 'required|integer',
                'sale_id' => 'required|integer',
                'user_id' => 'required|integer',
                'rank' => 'nullable|integer',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $app = new Application();
            $app->fill($input);
            $app->author_id = Auth::id();
            $app->created_at = date('Y-m-d H:i:s');
            if ($app->save()) {
                $msg = 'Заявка менеджеру № ' . $input['doc_num'] . ' была успешно добавлена!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/applications')->with('status', $msg);
            }
        }
        if (view()->exists('workflow::application_add')) {
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $pris = Priority::all();
            $psel = array();
            foreach ($pris as $val) {
                $psel[$val->id] = $val->title;
            }
            $stats = Statuse::all();
            $statsel = array();
            foreach ($stats as $val) {
                $statsel[$val->id] = $val->title;
            }
            $doc_num = LibController::GenNumberDoc('applications');

            $data = [
                'title' => 'Заявки менеджеру',
                'head' => 'Новая заявка менеджеру',
                'usel' => $usel,
                'psel' => $psel,
                'statsel' => $statsel,
                'doc_num' => $doc_num,
            ];
            return view('workflow::application_add', $data);
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
        if (view()->exists('workflow::application_view')) {
            $stats = Statuse::all();
            $statsel = array();
            foreach ($stats as $val) {
                $statsel[$val->id] = $val->title;
            }
            $currs = Currency::all();
            $cursel = array();
            foreach ($currs as $val) {
                $cursel[$val->id] = $val->title;
            }
            $carrs = Car::all();
            $carsel = array();
            foreach ($carrs as $val) {
                $carsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $pris = Priority::all();
            $psel = array();
            foreach ($pris as $val) {
                $psel[$val->id] = $val->title;
            }
            $firms = Firm::select('id','title')->whereNotNUll('vcode')->get();
            $firmsel = array();
            foreach ($firms as $val) {
                $firmsel[$val->id] = $val->title;
            }
            $app = Application::find($id);
            $rows = TblApplication::where('application_id', $id)->get();
            $vat = env('VAT');

            //цепочка связанных документов
            $link = Sale::find($app->sale_id);
            $tbody = '';
            if(!empty($link)){
                $tbody .= '<tr><td class="text-bold"><a href="/sales/view/'.$link->id.'" target="_blank">
                    Заказ клиента №' . $link->doc_num . '</a></td>';
                if(isset($link->statuse_id)){
                    $tbody .= '<td>' . $link->statuse->title . '</td>';
                }
                else{
                    $tbody .= '<td></td>';
                }
                $tbody .= '<td>'
                    . $link->created_at . '</td><td>' . $link->user->name . '</td></tr>';
            }

            $data = [
                'title' => 'Заявки менеджеру',
                'head' => 'Заявка менеджеру № ' . $app->doc_num,
                'statsel' => $statsel,
                'usel' => $usel,
                'unsel' => $unsel,
                'psel' => $psel,
                'firmsel' => $firmsel,
                'carsel' => $carsel,
                'cursel' => $cursel,
                'vat' => $vat,
                'application' => $app,
                'rows' => $rows,
                'tbody' => $tbody,
            ];
            return view('workflow::application_view', $data);
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
        $app = Application::find($id);
        if (!Role::granted('sales')) {//вызываем event
            $msg = 'Попытка редактирования заявки менеджеру №' . $app->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование заявок менеджеру!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $sale_id = Sale::where('doc_num', $input['sale_id'])->first()->id;
            $input['sale_id'] = $sale_id;

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'doc_num' => 'required|string|max:15',
                'priority_id' => 'required|integer',
                'statuse_id' => 'required|integer',
                'sale_id' => 'required|integer',
                'user_id' => 'required|integer',
                'rank' => 'nullable|integer',
                'comment' => 'nullable|string|max:254',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $app->fill($input);
            if ($app->update()) {
                $msg = 'Данные заявки менеджеру № ' . $app->doc_num . ' были успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect()->back()->with('status', $msg);
            }
        }
    }

    public function addPosition(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code','by_catalog'); //параметр _token нам не нужен

            $model = new TblApplication();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                $msg = 'Добавлена новая позиция ' . $model->good->title . ' к заявке менеджеру №' . $model->application->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $content = '<tr id="' . $model->id . '">
                    <td>' . $model->good->catalog_num . '</td>
                    <td>' . $model->good->analog_code . '</td>
                    <td>' . $model->good->title . '</td>
                    <td>' . $model->qty . '</td>
                    <td>' . $model->car->title . '</td>
                    <td>' . $model->offers . '</td>
                    <td style="width:70px;">    <div class="form-group" role="group">';
                $content .= '<button class="btn btn-danger btn-sm pos_delete" type="button" title="Удалить позицию">
                            <i class="fa fa-trash fa-lg" aria-hidden="true"></i></button>
                        </div>
                    </td>
                </tr>';

                $result = ['content' => $content];
                return json_encode($result);
            }
        }
        return 'NO';
    }
}
