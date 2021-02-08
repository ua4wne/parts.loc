<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblOrder extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_orders';

    protected $fillable = ['order_id','good_id','sub_good_id','comment','qty','unit_id','price','vat'];

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
        return $this->qty * $this->price;
    }

    public function getVatAmountAttribute(){
        if($this->vat)
            return round(($this->qty * $this->price) / ($this->vat+100) * $this->vat,2);
        else
            return 0;
    }

    public function getPurchaseAttribute() {
        $pos = TblPurchase::where(['order_id'=>$this->order->id,'good_id'=>$this->good_id])->get();
        $html = '';
        if(!empty($pos)){
            foreach ($pos as $row){
                if(!empty($row->purchase_id))
                    $docs = Purchase::where('id',$row->purchase_id)->get();
                if(!empty($docs)){
                    foreach ($docs as $doc){
                        $html .= '<a href="/purchases/view/'.$doc->id.'" target="_blank">'.$doc->doc_num.' от '.$doc->created_at.' - '.$row->qty.' ед.</a></br>';
                    }

                }
            }
        }
        return $html;
    }

    public function getFreePosAttribute(){

        //общее количество в заказе
        $order_qty = TblOrder::where(['order_id'=>$this->order_id,'good_id'=>$this->good_id])->sum('qty');
        //какое количество забрали в поступления
        $purchase_qty = TblPurchase::where(['order_id'=>$this->order_id,'good_id'=>$this->good_id])->sum('qty');
        if($order_qty > $purchase_qty)
            return $order_qty - $purchase_qty;
        else
            return 0;
    }
}
