<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class ShipmentUpload extends Model
{
    //указываем имя таблицы
    protected $table = 'shipment_uploads';

    protected $fillable = ['shipment_id','path','user_id'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function shipment()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Shipment', 'shipment_id', 'id');
    }
}
