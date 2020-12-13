<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblApplication extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_applications';

    protected $fillable = ['application_id','good_id','qty','car_id','unit_id','order_id','days','price','currency_id',
        'comment','supplier_num'];

    public function application()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Application','application_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function car()
    {
        return $this->belongsTo('App\Models\Car','car_id','id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Unit','unit_id','id');
    }

    public function order()
    {
        return $this->Order::find($this->order_id);
    }
}
