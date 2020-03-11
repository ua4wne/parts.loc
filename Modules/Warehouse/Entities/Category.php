<?php

namespace Modules\Warehouse\Entities;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['category','parent_id','position'];
}
