<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penggajian_tkbm extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PenggajianTkbmFactory> */
    use HasFactory, SoftDeletes, UsesUuid;

    protected $guarded = ['id'];


    public function karyawan()
    {
        return $this->belongsTo(M_karyawan::class);
    }

    public function penggajian()
    {
        return $this->belongsTo(Penggajian::class);
    }
}
