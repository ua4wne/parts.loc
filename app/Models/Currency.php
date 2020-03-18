<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //указываем имя таблицы
    protected $table = 'currency';

    protected $fillable = ['title', 'dcode', 'scode', 'cource', 'unit'];
}
