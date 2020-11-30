<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\Lib\LibController;
use App\Models\Currency;
use App\Models\Delivery;
use App\Models\DeliveryMethod;
use App\Models\Organisation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Unit;
use Modules\Warehouse\Entities\Warehouse;
use Modules\Workflow\Entities\Agreement;
use Modules\Workflow\Entities\Firm;
use Modules\Workflow\Entities\Sale;
use Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!User::hasRole('admin') && !User::hasRole('manager') && !User::hasRole('director')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочников!');
        }
        if (view()->exists('workflow::sales_area')) {
            $title = 'Рабочее место менеджера';
            $rows = Good::offset(0)->limit(10)->get();
            $firms = Firm::all();
            $firmsel = array();
            foreach ($firms as $val) {
                $firmsel[$val->id] = $val->title;
            }
            $data = [
                'title' => $title,
                'head' => 'Помощник продаж',
                'firmsel' => $firmsel,
                'rows' => $rows,
            ];
            return view('workflow::sales_area', $data);
        }
        abort(404);
    }

    public function orders(){
        if (view()->exists('workflow::sale_orders')) {
            $rows = Sale::all();
            $data = [
                'title' => 'Заказы клиентов',
                'head' => 'Заказы клиентов',
                'rows' => $rows,
            ];

            return view('workflow::sale_orders', $data);
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
            $msg = 'Попытка создания нового заказа клиента!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
        }
        if (view()->exists('workflow::sale_add')) {
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
            $wxs = Warehouse::all();
            $wxsel = array();
            foreach ($wxs as $val) {
                $wxsel[$val->id] = $val->title;
            }
            $doc_num = LibController::GenNumberDoc('sales');
            $vat = env('VAT');
            $units = Unit::all();
            $unsel = array();
            foreach ($units as $val) {
                $unsel[$val->id] = $val->title;
            }
            $agreements = Agreement::select('id', 'title')->get();
            $agrsel = array();
            foreach ($agreements as $val) {
                $agrsel[$val->id] = $val->title;
            }
            $methods = DeliveryMethod::all();
            $dmethods = array();
            foreach ($methods as $val) {
                $dmethods[$val->id] = $val->title;
            }
            $deliveries = Delivery::all();
            $delivs = array();
            foreach ($deliveries as $val) {
                $delivs[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Заказы клиентов',
                'head' => 'Новый заказ клиента',
                'usel' => $usel,
                'cursel' => $cursel,
                'orgsel' => $orgsel,
                'doc_num' => $doc_num,
                'wxsel' => $wxsel,
                'vat' => $vat,
                'unsel' => $unsel,
                'agrsel' => $agrsel,
                'dmethods' => $dmethods,
                'delivs' => $delivs,
            ];
            return view('workflow::sale_add', $data);
        }
        abort(404);
    }

    public function findGoodAnalogs(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $filter = $input['filter'];
            $content = '';
            if (isset($filter) && isset($input['name'])) {
                switch ($filter) {
                    case 'name':
                        $arr = explode('(', $input['name']);
                        $name = trim($arr[0]);
                        $good = Good::where('title', $name)->first();
                        break;
                    case 'vendor':
                        $good = Good::where('vendor_code', $input['name'])->first();
                        break;
                    case 'analog':
                        $good = Good::where('analog_code', 'LIKE', '%' . $input['name'] . '%')->first();
                        break;
                    case 'catalog':
                        $good = Good::where('catalog_num', $input['name'])->first();
                        break;
                }
                if (!empty($good)) {
                    $content .= '<tr id="' . $good->id . '" class="text-bold text-green clicable"><td>' . $good->code . '</td><td>' . $good->vendor_code . '</td>
                                <td>' . $good->title . '</td><td>' . $good->category->category . '</td></tr>';
                    $analogs = Good::where('analog_code', 'LIKE', '%' . $good->analog_code . '%')->where('id', '!=', $good->id)->get();
                    if (!empty($analogs)) {
                        foreach ($analogs as $row) {
                            $content .= '<tr id="' . $row->id . '"><td>' . $row->code . '</td><td>' . $row->vendor_code . '</td>
                            <td>' . $row->title . '</td><td>' . $row->category->category . '</td></tr>';
                        }
                    }
                }
            }
            return $content;
        }
    }

    public function GoodParams(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $tab = $input['name'];
            $id = $input['good_id'];
            $content = '';
            if (!empty($tab) && !empty($id)) {
                switch ($tab) {
                    case 'common':
                        $good = Good::find($id);
                        if (!empty($good)) {
                            $content .= '<tr><td>Код 1С</td><td>' . $good->code . '</td></tr>';
                            $content .= '<tr><td>Наименование</td><td>' . $good->title . '</td></tr>';
                            $content .= '<tr><td>Описание</td><td>' . $good->descr . '</td></tr>';
                            $content .= '<tr><td>Артикул</td><td>' . $good->vendor_code . '</td></tr>';
                            $content .= '<tr><td>Аналоги</td><td>' . $good->analog_code . '</td></tr>';
                            $content .= '<tr><td>Каталожный №</td><td>' . $good->catalog_num . '</td></tr>';
                            $content .= '<tr><td>Бренд</td><td>' . $good->brand . '</td></tr>';
                            $content .= '<tr><td>Группа товара</td><td>' . $good->category->category . '</td></tr>';
                            $content .= '<tr><td>Складская группа</td><td>' . $good->group->title . '</td></tr>';
                            $unit = Unit::find($good->unit_id)->title;
                            $content .= '<tr><td>Основная ед. изм.</td><td>' . $unit . '</td></tr>';
                            $content .= '<tr><td>Штрих-код</td><td>' . $good->barcode . '</td></tr>';
                            $content .= '<tr><td>Ставка НДС</td><td>' . $good->vat . '</td></tr>';
                        }
                        break;
                }

            }
            return $content;
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('workflow::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('workflow::edit');
    }
}
