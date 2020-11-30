<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    //указываем имя таблицы
    protected $table = 'agreements';

    protected $fillable = ['doc_num','statuse_id','title','start','finish','organisation_id','currency_id','user_id','comment'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation','organisation_id','id');
    }

    public function statuse()
    {
        return $this->belongsTo('App\Models\Statuse','statuse_id','id');
    }
}
