<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //указываем имя таблицы
    protected $table = 'contracts';

    protected $fillable = ['status','doc_num','title','print_title','start','finish','type','organisation_id','org_acc','firm_id','firm_acc','user_id','uip','gosid','delivery_method','currency_id'];

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation','organisation_id','id');
    }

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
