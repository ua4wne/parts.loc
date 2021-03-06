<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirmType extends Model
{
    //указываем имя таблицы
    protected $table = 'firm_types';

    protected $fillable = ['title'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
