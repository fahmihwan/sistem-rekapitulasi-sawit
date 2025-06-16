<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pembelian_tbs extends BaseModel
{

    use HasFactory, SoftDeletes, UsesUuid;

    protected $guarded = ['id'];
}
