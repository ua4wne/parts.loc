<?php

namespace App\Http\Controllers\Lib;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LibController extends Controller
{
    //генератор номеров документов
    public static function GenNumberDoc($table){
        $year =  date("Y");
        //получаем текущий номер документа для выбранной таблицы
        $row = DB::table('numbers')->where('alias', $table)->first();
        if(empty($row)){
            DB::table('numbers')->insert(
                ['serial' => 2, 'alias' => $table, 'created_at'=>date('Y-m-d H:i:s')]
            );
            $num = 1;
        }
        else{
            $num = $row->serial;
            $new = $num + 1;
            DB::table('numbers')
                ->where('alias', $table)
                ->update(['serial' => $new, 'updated_at'=>date('Y-m-d H:i:s')]);
        }
        while(strlen($num)<10){
            $num = '0' . $num;
        }
        $num = $year . '-' . $num;

        return $num;
    }
}
