<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblDeclaration extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_declarations';

    protected $fillable = ['declaration_id','good_id','comment','qty','unit_id','amount','duty','vat','purchase_id'];

    public function declaration()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Declaration','declaration_id','id');
    }

    public function purchase()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Purchase','purchase_id','id');
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
