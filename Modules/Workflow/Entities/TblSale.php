<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Entities\Reservation;
use Modules\Warehouse\Entities\Stock;

class TblSale extends Model
{
    protected $table = 'tbl_sales';

    protected $fillable = ['pos_num','sale_id','good_id','sub_good_id','comment','qty','unit_id','price','ratio','vat','reserved'];

    public function sale()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Sale', 'sale_id', 'id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good', 'good_id', 'id');
    }

    public function sub_good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','sub_good_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit', 'unit_id', 'id');
    }

    public function getAmountAttribute()
    {
        return $this->qty * $this->price * $this->ratio;
    }

    public function getVatAmountAttribute()
    {
        if ($this->vat)
            return round(($this->qty * $this->price * $this->ratio) / ($this->vat + 100) * $this->vat, 2);
        else
            return 0;
    }

    public function getReservedQtyAttribute()
    {
        return Reservation::where('tbl_sale_id',$this->id)->sum('qty');
    }

    public function getFreeQtyAttribute()
    {
        return Stock::where('good_id',$this->good_id)->sum('qty');
    }

    public function getNeedQtyAttribute()
    {
        return $this->qty - $this->reserved;
    }

}
