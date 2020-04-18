<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class WhCorrect extends Model
{
    protected $table = 'wh_corrects';

    protected $fillable = ['doc_num','warehouse_id','price_id','reason','user_id'];

    public function warehouse()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Warehouse','warehouse_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function price()
    {
        return $this->belongsTo('App\Models\Price','price_id','id');
    }
}
