<?php

namespace Modules\HR\Entities;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'positions';

    protected $fillable = ['title'];
}
