<?php

namespace Modules\HR\Entities;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $table = 'personals';

    protected $fillable = ['user_id','position_id','organisation_id','signing'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function position()
    {
        return $this->belongsTo('Modules\HR\Entities\Position','position_id','id');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation','organisation_id','id');
    }
}
