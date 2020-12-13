<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    //указываем имя таблицы
    protected $table = 'cars';

    protected $fillable = ['title','descr'];
}
