<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M_type_karyawan extends BaseModel
{
    /** @use HasFactory<\Database\Factories\MTypeKaryawanFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
}
