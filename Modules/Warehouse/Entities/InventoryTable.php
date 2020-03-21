<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class InventoryTable extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_inventories';

    protected $fillable = ['inventory_id', 'good_id', 'cell', 'qty', 'price','unit_id','amount'];

    public function inventory()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Inventory','inventory_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }
}
