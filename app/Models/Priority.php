<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    //указываем имя таблицы
    protected $table = 'priorities';

    protected $fillable = ['title', 'rank'];
}
