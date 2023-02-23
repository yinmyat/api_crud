<?php

namespace App\Api\Foundation\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Api\Foundation\Eloquent\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model as LaravelModel;

class Model extends LaravelModel
{
    use SoftDeletes, HasUUID;

    protected $hidden = ['deleted_at'];
}
