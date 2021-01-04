<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = ['location_id','tbl_sale_id','qty'];

    public function location()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Location','location_id','id');
    }

    public function tbl_sale()
    {
        return $this->belongsTo('Modules\Workflow\Entities\TblSale','tbl_sale_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }

    public function getFreeQtyAttribute()
    {
        if(!empty($this->good_id)){
            $good = Good::find($this->good_id);
            if(!empty($good)){
                //ищем резервирование товара
                $rsum = Reservation::where('good_id',$this->good_id)->sum('good_id');
                $free = $this->qty - $rsum;
                return $free;
            }
        }
        return 0;
    }
}
