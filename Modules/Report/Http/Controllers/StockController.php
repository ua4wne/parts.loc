<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Warehouse\Entities\Category;
use Modules\Warehouse\Entities\Stock;
use Modules\Warehouse\Entities\Warehouse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockController extends Controller
{
    public function index(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $warehouse = $input['warehouse_id'];
            if (isset($input['category_id']))
                $category = $input['category_id'];
            else
                $category = null;
            if (isset($input['export'])) {
                if (!Role::granted('export')) {
                    abort(503);
                }
                return $this->export($warehouse, $category);
            }
            $data = array();
            //$pie = array();
            if (empty($category)) {
                $query = "SELECT g.vendor_code AS vendor, g.analog_code AS analog, g.title AS title,st.cell AS cell,st.qty AS qty,u.title AS pack,st.cost AS cost from stocks st
                            JOIN goods g ON st.good_id = g.id
                            JOIN units u ON st.unit_id = u.id
                            WHERE warehouse_id = $warehouse ORDER BY cost desc";
                $qty = Stock::where(['warehouse_id' => $warehouse])->sum('qty');
                $cost = Stock::where(['warehouse_id' => $warehouse])->sum('cost');
                array_push($data, ["qty" => "$qty"]);
                array_push($data, ["cost" => "$cost"]);
            } else {
                $query = "SELECT g.vendor_code AS vendor, g.analog_code AS analog, g.title AS title,st.cell AS cell,st.qty AS qty,u.title AS pack,st.cost AS cost from stocks st
                            JOIN goods g ON st.good_id = g.id
                            JOIN units u ON st.unit_id = u.id
                            WHERE warehouse_id = $warehouse AND g.category_id=$category ORDER BY cost desc";
                $qty = DB::select("SELECT sum(st.qty) AS qty from stocks st
                                    JOIN goods g ON st.good_id = g.id
                                    WHERE warehouse_id = $warehouse AND g.category_id=$category");
                $cost = DB::select("SELECT sum(st.cost) AS cost from stocks st
                                    JOIN goods g ON st.good_id = g.id
                                    WHERE warehouse_id = $warehouse AND g.category_id=$category");
                array_push($data, ["qty" => $qty[0]->qty]);
                array_push($data, ["cost" => $cost[0]->cost]);
            }
            $rows = DB::select($query);
            $content = '<table class="table table-hover table-bordered"><tr>
                        <th>Артикул</th><th>Аналоги</th><th>Номенклатура</th><th>Ячейка</th><th>Кол-во</th><th>Ед. изм.</th>
                        <th>Стоимость, руб</th></tr>';
            foreach ($rows as $row) {
                $content .= '<tr><td>' . $row->vendor . '</td><td>' . $row->analog . '</td><td>' . $row->title . '</td><td>' . $row->cell . '</td>
                            <td>' . $row->qty . '</td><td>' . $row->pack . '</td><td>' . $row->cost . '</td></tr>';
            }
            $content .= '</table>';
            array_push($data, ["content" => $content]);
            //array_push($data,["pie"=>$pie]);
            return json_encode($data);
        }
        if (view()->exists('report::stock')) {
            $title = 'Складские остатки';
            $wrhs = Warehouse::all();
            foreach ($wrhs as $row) {
                $wxsel[$row->id] = $row->title; //массив для заполнения данных в select формы
            }
            $cats = Category::all();
            foreach ($cats as $row) {
                $catsel[$row->id] = $row->category; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'wxsel' => $wxsel,
                'catsel' => $catsel,
            ];
            return view('report::stock', $data);
        }
        abort(404);
    }

    private function export($warehouse, $category)
    {
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THICK,
                ),
                'bottom' => array(
                    'style' => Border::BORDER_THICK,
                ),
                'left' => array(
                    'style' => Border::BORDER_THICK,
                ),
                'right' => array(
                    'style' => Border::BORDER_THICK,
                ),
            )
        );
        $styleCell = array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_LEFT,
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
        $sheet->setTitle('Складские остатки');
        $k = 1;
        $sheet->setCellValue('A' . $k, 'Складские остатки по складу ' . Warehouse::find($warehouse)->title);
        $sheet->mergeCells('A' . $k . ':G' . $k);
        $sheet->getStyle('A' . $k . ':G' . $k)->getFont()->setBold(true);
        $sheet->getStyle('A' . $k . ':G' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k = 2;
        $sheet->setCellValue('A' . $k, 'по состоянию на  ' . date('Y-m-d H:i:s'));
        $sheet->mergeCells('A' . $k . ':G' . $k);
        //$sheet->getStyle('A'.$k.':B'.$k)->getFont()->setBold(true);
        $sheet->getStyle('A' . $k . ':G' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k = 3;
        if (empty($category)) {
            $query = "SELECT g.vendor_code AS vendor, g.analog_code AS analog, g.title AS title,st.cell AS cell,st.qty AS qty,u.title AS pack,st.cost AS cost from stocks st
                            JOIN goods g ON st.good_id = g.id
                            JOIN units u ON st.unit_id = u.id
                            WHERE warehouse_id = $warehouse ORDER BY cost desc";
            $qty = Stock::where(['warehouse_id' => $warehouse])->sum('qty');
            $cost = Stock::where(['warehouse_id' => $warehouse])->sum('cost');
            $sheet->setCellValue('A' . $k, 'Общее число товарных позиций ' . $qty . ' на сумму ' . $cost . ' руб.');
            $sheet->mergeCells('A' . $k . ':G' . $k);
            //$sheet->getStyle('A' . $k . ':G' . $k)->getFont()->setBold(true);
        } else {
            $query = "SELECT g.vendor_code AS vendor, g.analog_code AS analog, g.title AS title,st.cell AS cell,st.qty AS qty,u.title AS pack,st.cost AS cost from stocks st
                            JOIN goods g ON st.good_id = g.id
                            JOIN units u ON st.unit_id = u.id
                            WHERE warehouse_id = $warehouse AND g.category_id=$category ORDER BY cost desc";
            $qty = DB::select("SELECT sum(st.qty) AS qty from stocks st
                                    JOIN goods g ON st.good_id = g.id
                                    WHERE warehouse_id = $warehouse AND g.category_id=$category");
            $cost = DB::select("SELECT sum(st.cost) AS cost from stocks st
                                    JOIN goods g ON st.good_id = g.id
                                    WHERE warehouse_id = $warehouse AND g.category_id=$category");
            $sheet->setCellValue('A' . $k, 'Общее число товарных позиций ' . $qty[0]->qty . ' на сумму ' . $cost[0]->cost . ' руб.');
            $sheet->mergeCells('A' . $k . ':G' . $k);
            //$sheet->getStyle('A' . $k . ':G' . $k)->getFont()->setBold(true);
        }
        $sheet->getStyle('A' . $k . ':B' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $rows = DB::select($query);
        $content = '<table class="table table-hover table-bordered"><tr>
                        <th>Артикул</th><th>Аналоги</th><th>Номенклатура</th><th>Ячейка</th><th>Кол-во</th><th>Ед. изм.</th>
                        <th>Стоимость, руб</th></tr>';
        $k = 5;
        $sheet->getStyle('A' . $k . ':B' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A' . $k, 'Артикул');
        $sheet->setCellValue('B' . $k, 'Аналог');
        $sheet->setCellValue('C' . $k, 'Номенклатура');
        $sheet->setCellValue('D' . $k, 'Ячейка');
        $sheet->setCellValue('E' . $k, 'Кол-во');
        $sheet->setCellValue('F' . $k, 'Ед. изм.');
        $sheet->setCellValue('G' . $k, 'Стоимость, руб.');
        $sheet->getStyle('A' . $k . ':G' . $k)->applyFromArray($styleArray);
        $k++;
        foreach ($rows as $row) {
            //$sheet->getStyle('A' . $k . ':C' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $k, $row->vendor);
            $sheet->setCellValue('B' . $k, $row->analog);
            $sheet->setCellValue('C' . $k, $row->title);
            $sheet->setCellValue('D' . $k, $row->cell);
            $sheet->setCellValue('E' . $k, $row->qty);
            $sheet->setCellValue('F' . $k, $row->pack);
            $sheet->setCellValue('G' . $k, $row->cost);
            $sheet->getStyle('A' . $k . ':G' . $k)->applyFromArray($styleCell);
            $k++;
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "stock";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

}
