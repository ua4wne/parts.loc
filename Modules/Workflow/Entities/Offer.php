<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    //указываем имя таблицы
    protected $table = 'offers';

    protected $fillable = ['firm_id','good_id','price','markup','currency_id','unit_id','delivery_time','comment'];

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }
}
