<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    //указываем имя таблицы
    protected $table = 'applications';

    protected $fillable = ['doc_num','priority_id','sale_id','user_id','author_id','statuse_id','comment','rank','state'];

    public function priority()
    {
        return $this->belongsTo('App\Models\Priority','priority_id','id');
    }

    public function sale()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Sale','sale_id','id');
    }

    public function statuse()
    {
        return $this->belongsTo('App\Models\Statuse','statuse_id','id');
    }

    public function author()
    {
        return $this->belongsTo('App\User','author_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
