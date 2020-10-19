<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //указываем имя таблицы
    protected $table = 'purchases';

    protected $fillable = ['doc_num','firm_id','statuse_id','finish','currency_id','hoperation_id', 'organisation_id',
        'contract_id','warehouse_id','user_id','comment'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }

    public function statuse()
    {
        return $this->belongsTo('App\Models\Statuse','statuse_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function hoperation()
    {
        return $this->belongsTo('App\Models\Hoperation','hoperation_id','id');
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

    public function getAmountAttribute(){
        $amount = 0;
        $rows = TblPurchase::where('purchase_id',$this->id)->get();
        if(empty($rows)) return $amount;

        foreach ($rows as $row){
            $amount += ($row->qty * $row->price2);
        }
        return $amount;
    }

    public function getVatAmountAttribute(){
        $amount = 0;
        $rows = TblPurchase::where('purchase_id',$this->id)->get();
        if(empty($rows)) return $amount;

        foreach ($rows as $row){
            $amount += ($row->qty * $row->price2) / ($row->vat+100) * $row->vat;
        }
        return round($amount,2);
    }
}
