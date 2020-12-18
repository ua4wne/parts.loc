<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //указываем имя таблицы
    protected $table = 'contacts';

    protected $fillable = ['firm_id','lname','mname','fname','position','phone','phones','email','site','legal_address',
        'fact_address','post_address'];

    public function firm()
    {
        return $this->belongsTo('Modules\Entities\Firm','firm_id','id');
    }

    public function getFullNameAttribute()
    {
        return $this->lname." ".$this->fname;
    }
}
