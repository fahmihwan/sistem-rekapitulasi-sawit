<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Penjualan extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PenjualanFactory> */
    use HasFactory, SoftDeletes, UsesUuid;

    protected $guarded = ['id'];


    public function tkbms()
    {
        return $this->hasMany(Tkbm::class, 'penjualan_id');
    }

    public function sopir()
    {
        return $this->belongsTo(M_karyawan::class, 'sopir_id');
    }

    public function pabrik()
    {
        return $this->belongsTo(M_pabrik::class);
    }



    // UUID config;
}
