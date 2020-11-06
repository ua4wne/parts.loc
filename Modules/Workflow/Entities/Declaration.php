<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Declaration extends Model
{
    //указываем имя таблицы
    protected $table = 'declarations';

    protected $fillable = ['doc_num', 'firm_id', 'currency_id', 'organisation_id', 'contract_id', 'user_id','declaration_num',
        'who_register', 'broker_id', 'tax', 'fine', 'cost', 'rate', 'amount', 'vat', 'vat_amount', 'expense_id', 'country_id', 'comment'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }

    public function broker()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','broker_id','id');
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

    public function expense()
    {
        return $this->belongsTo('App\Models\Expense','expense_id','id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country','country_id','id');
    }
}
