<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoodController extends Controller
{
    public function index(){
        if (view()->exists('goods')) {
            $title = 'Номенклатура';
            $data = [
                'title' => $title,
                'head' => 'Справочник номенклатуры',
            ];
            return view('goods', $data);
        }
        abort(404);
    }
}
