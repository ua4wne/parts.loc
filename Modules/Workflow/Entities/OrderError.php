<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderError extends Model
{
    //указываем имя таблицы
    protected $table = 'order_errors';

    protected $fillable = ['order_id','vendor_code','qty','unit','price','vat','multi'];

    public function order()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Order','order_id','id');
    }
}
