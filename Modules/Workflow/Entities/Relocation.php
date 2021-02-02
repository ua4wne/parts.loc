<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Entities\Location;

class Relocation extends Model
{
    //указываем имя таблицы
    protected $table = 'relocations';

    protected $fillable = ['sale_id','order_id','src_id','dst_id','good_id','qty','unit_id','stage'];

    public function src(){
        return $this->belongsTo('Modules\Warehouse\Entities\Location', 'src_id', 'id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good', 'good_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit', 'unit_id', 'id');
    }

    public function getDestLocationAttribute(){
        return Location::find($this->dst_id)->title;
    }

}
