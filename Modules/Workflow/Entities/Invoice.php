<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //указываем имя таблицы
    protected $table = 'invoices';

    protected $fillable = ['doc_num','order_id'];

    public function order()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Order','order_id','id');
    }
}
