<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    protected $table = 'goods';

    protected $fillable = ['category_id','title','bx_group','vendor_code','analog_code','brand','model','unit_id'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category','category_id','id');
    }

    /**
     * Единицы измерения, принадлежащие товару.
     */
    public function units()
    {
        return $this->belongsToMany('App\Models\Unit');
    }
}
