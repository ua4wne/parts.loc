<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTable extends Model
{
    //указываем имя таблицы
    protected $table = 'price_tables';

    protected $fillable = ['price_id', 'good_id', 'cost_1', 'cost_2', 'cost_3'];

    public function price()
    {
        return $this->belongsTo('App\Models\Price','price_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Price','good_id','id');
    }
}
