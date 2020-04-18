<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class TblWhCorrect extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_wh_corrects';

    protected $fillable = ['wh_correct_id', 'good_id', 'cell', 'qty', 'price','unit_id','amount'];

    public function price()
    {
        return $this->belongsTo('App\Models\Price','price_id','id');
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
}
