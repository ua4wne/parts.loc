<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //указываем имя таблицы
    protected $table = 'sales';

    protected $fillable = ['doc_num','firm_id','organisation_id','contract_id','warehouse_id','currency_id','delivery_method_id',
        'delivery_id','destination','contact','to_door','delivery_in_price','user_id','date_agreement','has_vat','state',
        'doc_num_firm','date_firm','comment','agreement_id'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }

    public function agreement()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Agreement','agreement_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation','organisation_id','id');
    }

    public function contract()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Contract','contract_id','id');
    }

    public function warehouse()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Warehouse','warehouse_id','id');
    }

    public function delivery_method(){
        return $this->belongsTo('App\Models\DeliveryMethod','delivery_method_id','id');
    }

    public function delivery(){
        return $this->belongsTo('App\Models\Delivery','delivery_id','id');
    }

    public function getAmountAttribute(){
        $amount = 0;
        $rows = TblSale::where('sale_id',$this->id)->get();
        if(empty($rows)) return $amount;

        foreach ($rows as $row){
            $amount += ($row->qty * $row->price);
        }
        return $amount;
    }

    public function getVatAmountAttribute(){
        $amount = 0;
        $rows = TblSale::where('sale_id',$this->id)->get();
        if(empty($rows)) return $amount;

        foreach ($rows as $row){
            $amount += ($row->qty * $row->price) / ($row->vat+100) * $row->vat;
        }
        return round($amount,2);
    }

    public function getStatusAttribute(){
        switch ($this->state){
            case 0:
                return 'Создан';
                break;
            case 1:
                return 'Комплектуется';
                break;
            case 2:
                return 'Готов к отгрузке';
                break;
            case 3:
                return 'Отгружен';
                break;
        }
    }
}
