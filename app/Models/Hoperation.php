<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hoperation extends Model
{
    //указываем имя таблицы
    protected $table = 'hoperations';

    protected $fillable = ['title'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
