<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblPurchase extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_purchases';

    protected $fillable = ['purchase_id','order_id','good_id','sub_good_id','qty','unit_id','price1','price2','vat'];

    public function purchase()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Purchase','purchase_id','id');
    }

    public function order()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Order','order_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function sub_good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','sub_good_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }

    public function getAmountAttribute(){
        return $this->qty * $this->price2;
    }

    public function getVatAmountAttribute(){
        if($this->vat)
            return round(($this->qty * $this->price2) / ($this->vat+100) * $this->vat,2);
        else
            return 0;
    }
}
