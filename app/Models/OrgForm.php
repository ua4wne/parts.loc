<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgForm extends Model
{
    protected $table = 'org_forms';

    protected $fillable = ['title','nameRU','nameEN'];
}
