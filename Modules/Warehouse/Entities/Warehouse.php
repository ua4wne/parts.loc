<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    protected $fillable = ['title','descr','user_id','organisation_id'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation','organisation_id','id');
    }
}
