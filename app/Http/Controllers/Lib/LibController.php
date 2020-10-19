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

    // Форматирование цен.
    public static function format_price($value)
    {
        return number_format($value, 2, ',', ' ');
    }

    // Сумма прописью.
    public static function str_price($value)
    {
        $value = explode('.', number_format($value, 2, '.', ''));

        $f = new \NumberFormatter('ru', \NumberFormatter::SPELLOUT);
        $str = $f->format($value[0]);

        // Первую букву в верхний регистр.
        $str = mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));

        // Склонение слова "рубль".
        $num = $value[0] % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1: $rub = 'рубль'; break;
            case 2:
            case 3:
            case 4: $rub = 'рубля'; break;
            default: $rub = 'рублей';
        }

        return $str . ' ' . $rub . ' ' . $value[1] . ' копеек.';
    }
}
