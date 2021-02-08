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
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Unit;
use Modules\Workflow\Entities\Application;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Sale;
use Modules\Workflow\Entities\SetOffer;
use Modules\Workflow\Entities\TblApplication;
use Modules\Workflow\Entities\TblSale;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
            $title = 'Запрос цен';
            $rows = Application::orderBy('rank','desc')->get();
            $data = [
                'title' => $title,
                'head' => 'Запросы по ценам',
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
            $msg = 'Попытка создания нового запроса по ценам!';
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
                $msg = 'Запрос по ценам № ' . $input['doc_num'] . ' был успешно добавлен!';
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
                'title' => 'Запросы цен',
                'head' => 'Новый запрос по ценам',
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
                'title' => 'Запросы цен',
                'head' => 'Запрос по ценам № ' . $app->doc_num,
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
            $msg = 'Попытка редактирования запроса по ценам №' . $app->doc_num . '!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование запросов по ценам!');
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
                $msg = 'Данные запроса по ценам № ' . $app->doc_num . ' были успешно обновлены!';
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
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть целым числом!',
                'numeric' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input, [
                'good_id' => 'required|integer',
                'qty' => 'required|integer',
                'car_id' => 'required|integer',
                'price' => 'nullable|numeric',
                'application_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return 'NOVALIDATE';
            }
            $model = new TblApplication();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                $msg = 'Добавлена новая позиция ' . $model->good->title . ' к запросу по ценам №' . $model->application->doc_num;
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                $content = '<tr id="' . $model->id . '">
                    <td>' . $model->good->catalog_num . '</td>
                    <td>' . $model->good->analog_code . '</td>
                    <td>' . $model->good->title . '</td>
                    <td>' . $model->qty . '</td>
                    <td>' . $model->car->title . '</td>
                    <td>' . $model->price . '</td>
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

    public function delPosition(Request $request)
    {
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $pos = TblApplication::find($input['id']);
            $app_id = $pos->application_id;
            //проверяем закрыта заявка или нет
            $closed = Application::find($app_id)->state;
            if($closed)
                return 'NOT';
            if (!empty($pos) && !$closed) {
                //ищем связанные предложения поставщиков
                $offers = SetOffer::where('tbl_application_id',$input['id'])->get();
                if(!empty($offers)){
                    foreach ($offers as $row){
                        $row->delete();
                    }
                }
                $pos->delete();
                return 'OK';
            }
        }
        return 'NO';
    }

    public function delete(Request $request){
        if (!Role::granted('orders')) {//вызываем event
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $app = Application::find($input['id']);
            if(!empty($app)){
                //ищем табличную часть
                $tbl_app = TblApplication::where('application_id',$input['id'])->get();
                if(!empty($tbl_app)){
                    foreach ($tbl_app as $tbl){
                        //ищем связанные предложения поставщиков
                        $offers = SetOffer::where('tbl_application_id',$tbl->id)->get();
                        if(!empty($offers)){
                            foreach ($offers as $row){
                                $row->delete();
                            }
                        }
                        $tbl->delete();
                    }
                }
                $app->delete();
            }
            return 'OK';
        }
        return 'NO';
    }

    public function close(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $app = Application::find($id);
            if (!Role::granted('orders')) {//вызываем event
                return 'BAD';
            }
            $app->state = 1;
            if ($app->update()) {
                //обновляем цены и артикулы в связанной заявке клиента со своей наценкой
                $rows = TblApplication::where('application_id',$id)->get();
                if(!empty($rows)){
                    foreach ($rows as $row){
                        $pos = TblSale::find($row->tbl_sale_id);
                        if(!empty($pos)){
                            $pos->sub_good_id = $row->good_id;
                            $pos->price = $row->price;
                            $pos->update();
                        }
                    }
                }
                $msg = 'Запрос по ценам № ' . $app->doc_num . ' был закрыт!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return 'OK';
            }
        }
        return 'NO';
    }

    public function open(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['id'];
            $app = Application::find($id);
            if (!Role::granted('orders')) {//вызываем event
                return 'BAD';
            }
            $app->state = 0;
            if ($app->update()) {
                $msg = 'Запрос по ценам № ' . $app->doc_num . ' был открыт!';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return 'OK';
            }
        }
        return 'NO';
    }

    public function getPrice(Request $request)
    {
        if (!Role::granted('export') && !Role::granted('orders')) {
            abort(503, 'У Вас нет прав на запрос предложений от поставщиков!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $rows = TblApplication::where('application_id',$input['application_id'])->get();
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => Border::BORDER_THIN,
                    ),
                )
            );
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Запрос поставщику');
            $k = 4;
            $sheet->getStyle('A' . $k . ':Q' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $k, '№ п/п');
            $sheet->setCellValue('B' . $k, 'Каталожный №');
            $sheet->setCellValue('C' . $k, 'Артикул');
            $sheet->setCellValue('D' . $k, 'Аналог');
            $sheet->setCellValue('E' . $k, 'Наименование');
            $sheet->setCellValue('F' . $k, 'Кол-во');
            $sheet->setCellValue('G' . $k, 'Техника');
            $sheet->setCellValue('H' . $k, 'Дата запроса');
            $sheet->setCellValue('I' . $k, 'Дни');
            $sheet->setCellValue('J' . $k, 'Цена');
            $sheet->getStyle('A' . $k . ':J' . $k)->applyFromArray($styleArray);
            $k++;
            $num = 1;
            $date = date('Y-m-d');
            if(!empty($rows)){
                foreach ($rows as $row) {
                    //записываем в историю запросов для заявки
                    foreach ($input['firm_id'] as $firm){
                        SetOffer::updateOrCreate(['tbl_application_id' => $row->id,'firm_id' => $firm],
                            ['amount' => 0, 'comment' => 'отправлен запрос']);
                    }

                    $sheet->setCellValue('A' . $k, $num);
                    $sheet->setCellValue('B' . $k, $row->good->catalog_num);
                    $sheet->setCellValue('C' . $k, $row->good->vendor_code);
                    $sheet->setCellValue('D' . $k, '');
                    $sheet->setCellValue('E' . $k, $row->good->title);
                    $sheet->setCellValue('F' . $k, $row->qty);
                    $sheet->setCellValue('G' . $k, $row->car->title);
                    $sheet->setCellValue('H' . $k, $date);
                    $sheet->setCellValue('I' . $k, '');
                    $sheet->setCellValue('J' . $k, '');
                    $sheet->getStyle('A' . $k . ':A' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $k . ':F' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $k++;
                    $num++;
                }
            }

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "Запрос от ". $date;
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

    public function setPrice(Request $request)
    {
        if (!Role::granted('import') && !Role::granted('orders')) {
            return 'NO';
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 0;
            $rows = 0;
            $firm_id = $request['firm_id'];
            $app_id = $request['app_id'];
            if(!empty($firm_id) && !empty($app_id)){
                foreach ($tables as $table) {
                    $rows = count($table);
                    for ($i = 4; $i < $rows; $i++) {
                        $row = $table[$i];
                        if (!empty($row[1])) {
                            $vendor = trim($row[1]);
                            $new_vendor = trim($row[2]);
                            $dt = $row[7];
                            $amount = $row[8];
                            $good = Good::where(['vendor_code' => $vendor])->first();
                            //ищем tbl_application_id
                            if(!empty($good)){
                                $tbl_application = TblApplication::where(['application_id'=>$app_id,'good_id'=>$good->id])->first();
                                if (!empty($tbl_application)) {
                                    if(!empty($new_vendor)) { //если есть замена
                                        $new_good = Good::where(['vendor_code' => $new_vendor])->first();
                                        if(!empty($new_good)){
                                            $tbl_application->good_id = $new_good->id;
                                            $tbl_application->update();
                                        }
                                    }

                                    // создаст или обновит запись в таблице set_offers в зависимости от того
                                    // есть такая запись или нет
                                    SetOffer::updateOrCreate(['tbl_application_id' => $tbl_application->id,'firm_id' => $firm_id],
                                        ['delivery_time'=>$dt, 'amount' => $amount, 'comment' => '']);
                                    $num++;
                                }
                            }
                        }
                    }
                    break;
                }
                $rows -=4;
                $result = ['rows' => $rows, 'num' => $num];
                return json_encode($result);
            }
        }
        return 'ERR';
    }

    public function setPosPrice(Request $request){
        if (!Role::granted('sales')) {
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $tbl_app = TblApplication::find($input['id']);
            if(!empty($tbl_app)){
                $tbl_app->price = $input['price'];
                $tbl_app->firm_id = SetOffer::find($input['ofr_id'])->firm_id;
                if($tbl_app->update())
                    return 'OK';
            }
            return 'NO';
        }
    }

    public function setPosComment(Request $request){
        if (!Role::granted('orders')) {
            return 'BAD';
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = substr($input['id'],3);
            $off_row = SetOffer::find($id);
            if(!empty($off_row)){
                $off_row->comment = $input['comment'];
                if($off_row->update())
                    return 'OK';
            }
            return 'NO';
        }
    }
}
