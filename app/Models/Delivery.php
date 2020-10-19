<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    //указываем имя таблицы
    protected $table = 'deliveries';

    protected $fillable = ['title'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
