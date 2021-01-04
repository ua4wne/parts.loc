<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = ['title','barcode','warehouse_id','length','widht','height','capacity','priority','in_lock',
        'out_lock'];

    public function warehouse()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Warehouse','warehouse_id','id');
    }

    public function getLocationSizeAttribute()
    {
        return round($this->length * $this->widht * $this->height,2);
    }
}
