<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    //указываем имя таблицы
    protected $table = 'bank_accounts';

    protected $fillable = ['title','firm_id','bank_id','account','currency_id','status','is_main','for_pay'];

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }

    public function bank()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Bank','bank_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }
}
