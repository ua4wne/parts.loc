<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    //указываем имя таблицы
    protected $table = 'delivery_methods';

    protected $fillable = ['title'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
