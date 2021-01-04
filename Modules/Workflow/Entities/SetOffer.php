<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class SetOffer extends Model
{
    //указываем имя таблицы
    protected $table = 'set_offers';

    protected $fillable = ['tbl_application_id','firm_id','delivery_time','amount','comment'];

    public function tbl_application()
    {
        return $this->belongsTo('Modules\Workflow\Entities\TblApplication','tbl_application_id','id');
    }

    public function firm()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Firm','firm_id','id');
    }
}
