<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    //указываем имя таблицы
    protected $table = 'firms';

    protected $fillable = ['firm_type_id','code','title','inn','kpp','okpo','name','country_id','tax_number','client','provider','other','foreigner','user_id'];

    public function firm_type()
    {
        return $this->belongsTo('App\Models\FirmType','firm_type_id','id');
    }

    public function contact()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Contact','id','firm_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
