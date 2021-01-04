<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Warehouse\Entities\Category;
use Modules\Warehouse\Entities\Good;
use Modules\Warehouse\Entities\Group;
use Modules\Warehouse\Entities\Location;
use Modules\Warehouse\Entities\Specification;
use Modules\Warehouse\Entities\Stock;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class ExchangeController extends Controller
{
    public function Good(Request $request)
    {
        if (!User::hasRole('admin')) {
            return 'NO';
        }
        if ($request->hasFile('file')) {
            if (request()->file->getClientOriginalExtension() != "xlsx")
                return 'BAD_FILE';
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 1;
            $rows = 0;
            $err = 0;
            $last_pos_category = Category::where('parent_id', 0)->orderBy('position', 'desc')->first()->position;
            $last_pos_category++;
            set_time_limit(600);
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 1; $i < $rows; $i++) {
                    $row = $table[$i];
                    if (empty($row[0])) {
                        $err++;
                        continue;
                    }
                    if (empty($row[1])) {
                        $err++;
                        continue;
                    }
                    $title = $row[0];
                    $vendor_code = $row[1];
                    $code = $row[3];
                    $descr = $row[4];
                    $catalog_num = $row[5];
                    $brand = $row[6];
                    if (empty($row[7])) {
                        $category_id = 1;
                    } else {
                        $category = Category::where(['category' => $row[7]])->first();
                        if (empty($category)) {
                            $model = new Category();
                            $model->category = $row[7];
                            $model->parent_id = 0;
                            $model->position = $last_pos_category;
                            $model->created_at = date('Y-m-d H:i:s');
                            if ($model->save()) {
                                $last_pos_category++;
                                $category_id = $model->id;
                            }
                        } else
                            $category_id = $category->id;
                    }
                    switch ($row[8]) {
                        case "шт":
                            $unit_id = 1;
                            break;
                        case "л":
                            $unit_id = 3;
                            break;
                        case "м":
                            $unit_id = 5;
                            break;
                        case "пог.м":
                            $unit_id = 6;
                            break;
                        default:
                            $unit_id = 1;
                    }
                    if (!empty($row[10]))
                        //$weight = str_replace(',', '.', $row[10]);
                        $weight = floatval($row[10]);
                    else
                        $weight = $row[10];
                    if (!empty($row[13]))
                        $capacity = str_replace(',', '.', $row[13]);
                    else
                        $capacity = $row[13];
                    if (!empty($row[16]))
                        $length = str_replace(',', '.', $row[16]);
                    else
                        $length = $row[16];
                    if (!empty($row[19]))
                        $area = str_replace(',', '.', $row[19]);
                    else
                        $area = $row[19];
                    if (empty($row[21])) {
                        $group_id = 7;
                    } else {
                        $group = Group::where('title', $row[21])->first();
                        if (empty($group)) {
                            $model = new Group();
                            $model->title = $row[21];
                            $model->created_at = date('Y-m-d H:i:s');
                            if ($model->save())
                                $group_id = $model->id;
                        }
                        $group_id = $group->id;
                    }
                    if (empty($row[22]))
                        $vat = 20;
                    elseif (strstr($row[22], "Без НДС"))
                        $vat = 0;
                    else
                        $vat = substr($row[22], 0, 2);
                    if ($row[23] == "Да")
                        $gtd = 1;
                    else
                        $gtd = 0;
                    if ($row[24] == "Да")
                        $wx_position = 1;
                    else
                        $wx_position = 0;
                    Good::updateOrCreate(['vendor_code' => $vendor_code, 'title' => $title],
                        ['code' => $code, 'descr' => $descr, 'catalog_num' => $catalog_num, 'brand' => $brand, 'category_id' => $category_id,
                            'unit_id' => $unit_id, 'weight' => $weight, 'capacity' => $capacity, 'length' => $length, 'area' => $area,
                            'group_id' => $group_id, 'vat' => $vat, 'gtd' => $gtd, 'wx_position' => $wx_position]);
                    $num++;

                }
                break;
            }
            //$num-=3;
            //$rows-=3;
            $result = ['rows' => $rows, 'num' => $num, 'err' => $err];
            return json_encode($result);
        }
        if (view()->exists('admin::good_exchange')) {
            $data = [
                'title' => 'Новый обмен',
            ];
            return view('admin::good_exchange', $data);
        }
        abort(404);
    }

    public function GoodSpec(Request $request)
    {
        if (!User::hasRole('admin')) {
            return 'NO';
        }
        if ($request->hasFile('file')) {
            if (request()->file->getClientOriginalExtension() != "xlsx")
                return 'BAD_FILE';
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 1;
            $rows = 0;
            $err = 0;
            $brand_id = 1;
            //set_time_limit(600);
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 1; $i < $rows; $i++) {
                    $row = $table[$i];
                    if (empty($row[1])) {
                        $err++;
                        continue;
                    }
                    $good = Good::where('vendor_code', $row[1])->first();
                    if (empty($good)) {
                        $err++;
                        continue;
                    }
                    //$title = $row[3];
                    Specification::updateOrCreate(['good_id' => $good->id, 'title' => $row[3]], ['brand_id' => $brand_id]);
                    $num++;
                }
                break;
            }
            $num -= 1;
            $rows -= 1;
            $result = ['rows' => $rows, 'num' => $num, 'err' => $err];
            return json_encode($result);
        }
        if (view()->exists('admin::spec_exchange')) {
            $data = [
                'title' => 'Новый обмен',
            ];
            return view('admin::spec_exchange', $data);
        }
        abort(404);
    }

    public function Stock(Request $request)
    {
        if (!User::hasRole('admin')) {
            return 'NO';
        }
        if ($request->hasFile('file')) {
            if (request()->file->getClientOriginalExtension() != "xlsx")
                return 'BAD_FILE';
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 1;
            $rows = 0;
            $err = 0;
            $location_id = null;
            $old_good_id = null;
            $good_id = null;
            //set_time_limit(600);
            // Цикл по листам Excel-файла
            foreach ($tables as $table) {
                $rows = count($table);
                for ($i = 8; $i < $rows; $i++) {
                    $row = $table[$i];
                    if (empty($row[0])) {
                        $err++;
                        continue;
                    }
                    if(strlen($row[10]) > 0)
                        $qty = $row[10];
                    else
                        $qty = 0;
                    $cells = ["Офис-склад","Приемка"];
                    if(strpos($row[0],'.') == 1 || (in_array($row[0],$cells) )) {// это название ячейки
                        $cell = Location::where('title',$row[0])->first();
                        if(empty($cell)){
                            $loc = new Location();
                            $loc->title = $row[0];
                            $loc->warehouse_id = 1;
                            $loc->capacity = 1;
                            $loc->priority = 0;
                            $loc->in_lock = 0;
                            $loc->out_lock = 0;
                            $loc->created_at = date('Y-m-d H:i:s');
                            $loc->save();
                            $location_id = $loc->id;
                            $old_good_id = $good_id;
                        }
                        else {
                            $location_id = $cell->id;
                            $old_good_id = $good_id;
                        }
                        switch ($row[1]) {
                            case "шт":
                                $unit_id = 1;
                                break;
                            case "л":
                                $unit_id = 3;
                                break;
                            case "м":
                                $unit_id = 5;
                                break;
                            case "пог.м":
                                $unit_id = 6;
                                break;
                            default:
                                $unit_id = 1;
                        }
                    }
                    else { // это скорее всего артикул
                        $good = Good::where('vendor_code', $row[0])->first();
                        if (empty($good)) {
                            $err++;
                            continue;
                        }
                        else {
                            $good_id = $good->id;
                        }
                    }
                    if(($good_id == $old_good_id) && $good_id){
                        Stock::updateOrCreate(['good_id' => $good_id, 'location_id' => $location_id], ['warehouse_id'=>1,
                            'qty'=>$qty,'unit_id'=>$unit_id,'cost'=>0]);
                    }
                    $num++;
                }
                break;
            }
            $num -= 8;
            $rows -= 8;
            $result = ['rows' => $rows, 'num' => $num, 'err' => $err];
            return json_encode($result);
        }
        if (view()->exists('admin::stock_exchange')) {
            $data = [
                'title' => 'Новый обмен',
            ];
            return view('admin::stock_exchange', $data);
        }
        abort(404);
    }

}
