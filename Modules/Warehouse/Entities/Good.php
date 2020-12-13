<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    protected $table = 'goods';

    protected $fillable = ['category_id','group_id','title','descr','bx_group','vendor_code','code', 'catalog_num',
                    'analog_code','brand', 'model','unit_id','weight','capacity','length','area','vat','gtd','wx_position','barcode'];

    public function category()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Category','category_id','id');
    }

    public function group()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Group','group_id','id');
    }

    /**
     * Единицы измерения, принадлежащие товару.
     */
    public function units()
    {
        return $this->belongsToMany('Modules\Warehouse\Entities\Unit');
    }

    /**
     * Характеристики, принадлежащие товару.
     */
    public function specifications()
    {
        return $this->belongsToMany('Modules\Warehouse\Entities\Specification');
    }

    public function getHasSpecificationAttribute(){
        $specs = Specification::where('good_id',$this->id)->count('id');
        if(empty($specs)) return false;
        else return true;
    }
}
