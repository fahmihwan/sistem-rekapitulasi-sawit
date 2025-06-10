<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian_tbs extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PembelianTbsFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
}
