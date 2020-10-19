<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Brand;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Specification;
use Validator;

class SpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        if (!Role::granted('wh_work')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочника характеристик номенклатуры!');
        }
        if (view()->exists('warehouse::specifications')) {
            $rows = Specification::where('good_id',$id)->paginate(env('PAGINATION_SIZE'));
            $title = Good::find($id)->title;
            $data = [
                'title' => 'Характеристики номенклатуры',
                'head' => $title,
                'rows' => $rows,
                'id' => $id,
            ];
            return view('warehouse::specifications', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id,Request $request)
    {
        if (!Role::granted('wh_edit')) {//вызываем event
            $good = Good::find($id);
            $msg = 'Попытка создания новой характеристики для '.$good->title.'!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание характеристик номенклатуры!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //dd($input);
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
                'integer' => 'Значение поля должно целым числом!',
            ];
            $validator = Validator::make($input, [
                //'good_id' => 'required|integer',
                'brand_id' => 'required|integer',
                'title' => 'required|string|max:100',
            ], $messages);
            if ($validator->fails()) {
                //$messages = $validator->errors();
                //dump($messages);
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $spec = new Specification();
            $spec->created_at = date('Y-m-d');
            // создаст или обновит запись в модели $spec в зависимости от того
            // есть такая запись или нет
            Specification::updateOrCreate(['good_id' => $id, 'title' => $input['title']],
                ['brand_id' => $input['brand_id']]);

            $msg = 'Характеристика ' . $input['title'] . ' для номенклатуры '.Good::find($id)->title.' успешно добавлена\обновлена!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/specifications/view/'.$id)->with('status', $msg);
        }
        if (view()->exists('warehouse::spfc_add')) {
            $brands = Brand::all();
            $bsel = array();
            if(!empty($brands)){
                foreach ($brands as $row){
                    $bsel[$row->id] = $row->title;
                }
            }
            $data = [
                'title' => 'Характеристики номенклатуры',
                'head' => 'Новая запись',
                'good_id' => $id,
                'bsel' => $bsel,
            ];
            return view('warehouse::spfc_add', $data);
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
        $model = Specification::find($id);
        $name = $model->title;
        if ($request->isMethod('delete')) {
            if (!Role::granted('wh_edit')) {
                $msg = 'Попытка удаления характеристики ' . $name;
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $msg = 'Характеристика ' . $name . ' была удалена!';
            $model->delete();
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect('/specifications/view/'.$model->good_id)->with('status', $msg);
        }
        if (!Role::granted('wh_edit')) {
            $msg = 'Попытка редактирования характеристики ' . $name;
            //вызываем event
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'title' => 'required|string|max:100',
                'brand_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные характеристики ' . $input['title'] . ' успешно обновлены!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return redirect('/specifications/view/'.$model->good_id)->with('status', $msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('warehouse::spfc_edit')) {
            $brands = Brand::all();
            $bsel = array();
            if(!empty($brands)){
                foreach ($brands as $row){
                    $bsel[$row->id] = $row->title;
                }
            }
            $data = [
                'title' => 'Характеристика',
                'head' => $name,
                'data' => $old,
                'bsel' => $bsel,
            ];
            return view('warehouse::spfc_edit', $data);
        }
        abort(404);
    }
}
