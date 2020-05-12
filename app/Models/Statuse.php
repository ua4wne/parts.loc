<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statuse extends Model
{
    //указываем имя таблицы
    protected $table = 'statuses';

    protected $fillable = ['title', 'style'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
