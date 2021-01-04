<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = ['warehouse_id','good_id','location_id','qty','unit_id','cost','consignment'];

    public function warehouse()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Warehouse','warehouse_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function location()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Location','location_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }
}
