<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Location;
use Modules\Warehouse\Entities\Reservation;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Workflow\Entities\Relocation;
use Modules\Workflow\Entities\Sale;
use Modules\Workflow\Entities\Shipment;
use Modules\Workflow\Entities\ShipmentUpload;
use Modules\Workflow\Entities\TblSale;
use Validator;
use PDF;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('workflow::shipments')) {
            $rows = Shipment::all();
            $data = [
                'title' => 'Отгрузки',
                'head' => 'Наряды на сборку',
                'rows' => $rows,
            ];

            return view('workflow::shipments', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if (!Role::granted('sales')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            if (empty($input['sale_id']))
                return 'NO';
            //генерим новый документ
            $doc = new Shipment();
            $doc->doc_num = LibController::GenNumberDoc('shipments');
            $doc->sale_id = $input['sale_id'];
            $sale = Sale::find($input['sale_id']);
            $doc->warehouse_id = $sale->warehouse_id;
            $doc->author_id = Auth::id();
            $doc->user_id = Warehouse::find($sale->warehouse_id)->user_id;
            $doc->rank = 1;
            $doc->stage = 0;
            $doc->created_at = date('Y-m-d H:i:s');
            $doc->dst_id = Location::where(['warehouse_id' => $sale->warehouse_id, 'is_assembly' => 1])->first()->id;
            if ($doc->save()) {
                //заполняем табличную часть
                $rows = TblSale::where('sale_id', $input['sale_id'])->get();
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        //смотрим резерирование
                        $reservs = Reservation::where('tbl_sale_id', $row->id)->get();
                        if (!empty($reservs)) {
                            foreach ($reservs as $pos) {
                                $tbl = new Relocation();
                                $tbl->sale_id = $input['sale_id'];
                                $tbl->src_id = $pos->location_id;
                                $tbl->dst_id = $doc->dst_id;
                                $tbl->good_id = $row->good_id;
                                $tbl->qty = $pos->qty;
                                $tbl->unit_id = $row->unit_id;
                                $tbl->stage = 0;
                                $tbl->created_at = date('Y-m-d H:i:s');
                                $tbl->save();
                            }
                        }
                    }
                }
                $sale->state = 1;
                $sale->update();
                return 'OK';
            }
        }
        return 'NO';
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        if (view()->exists('workflow::shipment_view')) {
            $doc = Shipment::find($id);
            $rows = Relocation::where('sale_id', $doc->sale_id)->get();
            $dst = Location::where(['warehouse_id' => $doc->warehouse_id, 'is_assembly' => 1])->get();
            $dstsel = array();
            if (!empty($dst)) {
                foreach ($dst as $val) {
                    $dstsel[$val->id] = $val->title;
                }
            }
            $work_task = Relocation::where(['sale_id' => $doc->sale_id, 'stage' => 0])->count('id');
            $done_task = Relocation::where(['sale_id' => $doc->sale_id, 'stage' => 1])->count('id');
            $tbody = ''; //связанные документы
            //цепочка связанных документов
            $link = Sale::find($doc->sale_id);
            if (!empty($link)) {
                $tbody .= '<tr><td class="text-bold"><a href="/sales/view/' . $link->id . '" target="_blank">
                    Заказ клиента №' . $link->doc_num . '</a></td>';
                $tbody .= '<td>' . $link->status . '</td>';
                $tbody .= '<td>'
                    . $link->created_at . '</td><td>' . $link->user->name . '</td></tr>';

            }
            $content = '';
            $files = ShipmentUpload::where('shipment_id', $id)->get();
            if (!empty($files)) {
                foreach ($files as $file) {
                    $content .= '<div class="col-sm-3" id="'.$file->id.'">
                                    <div class="panel panel-white no-radius text-center">
                                        <div class="panel-body">
                                             <div class="img-thumbnail">
                                                <a href="'.Storage::url($file->path).'" data-fancybox="group">
                                                    <img class="img-responsive" src="'.Storage::url($file->path).'" alt="">
                                                </a>
                                             </div>
                                             <p>
                                                <a class="btn btn-wide btn-red del_pos" href="#"><i class="fa fa-trash-o"></i> Удалить</a>
                                             </p>
                                        </div>
                                    </div>
                                </div>';
                }
            }
            $data = [
                'title' => 'Наряд на сборку',
                'head' => 'Наряд на сборку №' . $doc->doc_num,
                'doc' => $doc,
                'rows' => $rows,
                'tbody' => $tbody,
                'dstsel' => $dstsel,
                'work_task' => $work_task,
                'done_task' => $done_task,
                'content' => $content,
            ];

            return view('workflow::shipment_view', $data);
        }
        abort(404);
    }

    public function edit(Request $request)
    {
        if (!Role::granted('sales') && !Role::granted('wh_work')) {//вызываем event
            $msg = 'Попытка правки наряда на сборку!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на эту операцию!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token', 'vendor_code'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
            ];
            $validator = Validator::make($input, [
                'id' => 'required|integer',
                'stage' => 'required|integer',
                'rank' => 'required|integer',
                'dst_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $shipment = Shipment::find($input['id']);
            if (!empty($shipment)) {
                $shipment->stage = $input['stage'];
                $shipment->rank = $input['rank'];
                $shipment->dst_id = $input['dst_id'];
                if ($shipment->update()) {
                    $sale = Sale::find($shipment->sale_id);
                    if (!empty($sale)) {
                        $sale->state = $input['stage'] + 1;
                        $sale->update();
                    }
                    $rows = Relocation::where('sale_id', $shipment->sale_id)->get();
                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            $row->dst_id = $shipment->dst_id;
                            $row->update();
                        }
                    }
                }
            }
            $msg = 'Данные наряда на сборку № ' . $shipment->doc_num . ' были обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return redirect()->back()->with('status', $msg);
        }
    }

    public function doneTask(Request $request)
    {
        if (!Role::granted('wh_work')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if (!empty($input['id'])) {
                $pos = Relocation::find($input['id']);
                $pos->stage = 1;
                if ($pos->update()) {
                    $content = '<td>' . $pos->src->title . '</td>
                                <td>' . $pos->dest_location . '</td>
                                <td>' . $pos->good->title . '</td>
                                <td>' . $pos->qty . '</td>
                                <td>' . $pos->unit->title . '</td>
                                <td style="width:70px;">
                                <button class="btn btn-success btn-sm"
                                                            type="button" title="Перемещено"><i
                                                            class="fa fa-cart-arrow-down fa-lg"
                                                            aria-hidden="true"></i>
                                                    </button>
                                </td>';
                    $work = Relocation::where(['sale_id' => $pos->sale_id, 'stage' => 0])->count('id');
                    $done = Relocation::where(['sale_id' => $pos->sale_id, 'stage' => 1])->count('id');
                    if ($work == 0) {
                        $doc = Shipment::where('sale_id', $pos->sale_id)->first();
                        $doc->stage = 1;
                        $doc->update();
                    }
                    $result = ['content' => $content, 'work' => $work, 'done' => $done];
                    return json_encode($result);
                }
            }
        }
        return 'NO';
    }

    public function print($id)
    {
        $doc = Shipment::find($id);
        $rows = Relocation::where('sale_id', $doc->sale_id)->get();
        $head = 'Наряд на сборку №' . $doc->doc_num;
        $pdf = PDF::loadView('workflow::shipment_print', compact('head', 'doc', 'rows')); //->setPaper('a4', 'landscape');
        return $pdf->download('task.pdf');
    }

    public function upload(Request $request)
    {
        if (!Role::granted('download') && !Role::granted('wh_work')) {
            abort(503, 'У Вас нет прав на загрузку файлов!');
        }
        if ($request->hasFile('file')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $shipment = Shipment::find($input['id']);
            $allow_ext = ['png', 'jpg', 'jpeg']; //разрешенные расширения для загрузки
            $err = '';
            $msg = '';
            foreach ($request->file() as $file) {
                foreach ($file as $f) {
                    //$name = time().'_'.$f->getClientOriginalName();
                    //$f->move(storage_path('upload'), $name);
                    $ext = $f->getClientOriginalExtension();
                    if (in_array($ext, $allow_ext)) {
                        $path = Storage::putFile('/public', new File($f));
                        $model = new ShipmentUpload();
                        $model->shipment_id = $shipment->id;
                        $model->path = $path;
                        $model->user_id = Auth::id();
                        $model->created_at = date('Y-m-d H:i:s');
                        $model->save();
                        $msg .= 'Файл '.$f->getClientOriginalName().' успешно загружен!'.PHP_EOL;
                    }
                    else{
                        $err .= 'Файл '.$f->getClientOriginalName().' запрещен к загрузке!'.PHP_EOL;
                    }
                }
            }
            return redirect()->back()->with('status', $msg)->with('error',$err);
        }
    }

    public function delFile(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $file = ShipmentUpload::find($input['id']);
            if (!User::hasRole('admin') || !User::isAuthor($file->user_id)) {//вызываем event
                return 'BAD';
            }
            $path =  Storage::url($file->path);
            $pos = strrpos($path, "/");
            if($pos){
                $pos ++;
                $path = substr($path,$pos);
            }
            $f = Storage::disk('public');
            if($f->delete($path)){
                $file->delete();
                return 'OK';
            }
        }
        return 'NO';
    }
}
