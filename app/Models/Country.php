<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //указываем имя таблицы
    protected $table = 'countries';

    protected $fillable = ['title', 'code1', 'code2', 'code3', 'eaes','full_name'];
}
