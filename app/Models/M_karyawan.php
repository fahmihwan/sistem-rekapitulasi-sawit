<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M_karyawan extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
}
