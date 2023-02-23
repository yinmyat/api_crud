<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Api\Foundation\Eloquent\Traits\HasUUID;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory,HasUUID,SoftDeletes;

    protected $fillable = [
        'uuid', 'name','image'
    ];
}
