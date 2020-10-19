<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblOrder extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_orders';

    protected $fillable = ['order_id','good_id','comment','qty','unit_id','price','vat'];

    public function order()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Order','order_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }

    public function getAmountAttribute(){
        return $this->qty * $this->price;
    }

    public function getVatAmountAttribute(){
        if($this->vat)
            return round(($this->qty * $this->price) / ($this->vat+100) * $this->vat,2);
        else
            return 0;
    }

    public function getPurchaseAttribute() {
        $doc = TblPurchase::where(['order_id'=>$this->order->id,'good_id'=>$this->good_id])->first();
        if(!empty($doc->purchase_id))
            $doc = Purchase::find($doc->purchase_id);
        if(!empty($doc)){
            return '<a href="/purchases/view/'.$doc->id.'" target="_blank">'.$doc->doc_num.' от '.$doc->created_at.'</a>';
        }
        return '';
    }
}
