<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tkbm extends Model
{
    /** @use HasFactory<\Database\Factories\TkbmFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(M_karyawan::class);
    }
}
