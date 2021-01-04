<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class TblWhCorrect extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_wh_corrects';

    protected $fillable = ['wh_correct_id', 'good_id', 'location_id', 'qty', 'price','unit_id'];

    public function location()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Location','location_id','id');
    }

    public function whcorrect()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\WhCorrect','wh_correct_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }

    public function getAmountAttribute()
    {
        return round($this->qty * $this->price,2);
    }
}
