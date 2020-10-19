<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    protected $table = 'specifications';

    protected $fillable = ['good_id','brand_id','title'];

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function brand()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Brand','brand_id','id');
    }
}
