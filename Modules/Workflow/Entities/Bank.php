<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    //указываем имя таблицы
    protected $table = 'banks';

    protected $fillable = ['bik','swift','title','account','city','country'];

    public function accounts()
    {
        return $this->hasMany('Modules\Workflow\BankAccount','bank_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
