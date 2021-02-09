<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    //указываем имя таблицы
    protected $table = 'shipments';

    protected $fillable = ['doc_num', 'rank', 'sale_id', 'warehouse_id', 'author_id', 'user_id', 'stage', 'dst_id'];

    public function sale()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Sale', 'sale_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Warehouse', 'warehouse_id', 'id');
    }

    public function dst()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Location', 'dst_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function getStatusAttribute()
    {
        switch ($this->stage) {
            case 0:
                return 'Комплектуется';
                break;
            case 1:
                return 'Собран';
                break;
            case 2:
                return 'Собран частично';
                break;
            case 3:
                return 'Оформление документов';
                break;
            case 4:
                return 'Отгружен';
                break;
            case 5:
                return 'Отгружен частично';
                break;
        }
    }

    public function getPickedAttribute()
    {
        $cnt = Relocation::where('sale_id', $this->sale_id)->count('id');
        $yes = Relocation::where(['sale_id' => $this->sale_id, 'stage' => 1])->count('id');
        if ($cnt == $yes)
            return 1;
        else
            return 0;
    }
}
