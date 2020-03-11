<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'organisations';

    protected $fillable = ['title','org_form_id','print_name','short_name','inn','ogrn','kpp','status','prefix','account',
                            'legal_address','post_address','phone','email'];

    public function org_form()
    {
        return $this->belongsTo('App\Models\OrgForm','org_form_id','id');
    }
}
