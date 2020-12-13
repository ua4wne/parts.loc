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
            $last_pos_category = Category::where('parent_id', 0)->orderBy('position','desc')->first()->position;
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
                    if(empty($row[7])){
                        $category_id = 1;
                    }
                    else{
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
                    elseif(strstr($row[22],"Без НДС"))
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
                    Good::updateOrCreate(['vendor_code' => $vendor_code,'title'=>$title],
                        ['code' => $code,'descr' => $descr,'catalog_num' => $catalog_num,'brand'=>$brand,'category_id'=>$category_id,
                            'unit_id' => $unit_id,'weight' => $weight, 'capacity' => $capacity, 'length' => $length, 'area' => $area,
                            'group_id' => $group_id, 'vat' => $vat, 'gtd' => $gtd,'wx_position' => $wx_position]);
                    $num ++;

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

}
