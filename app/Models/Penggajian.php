<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penggajian extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PenggajianFactory> */
    use HasFactory, SoftDeletes, UsesUuid;

    protected $guarded = ['id'];

    public function penggajian_tkbms()
    {
        return $this->hasMany(Penggajian_tkbm::class, 'penggajian_id');
    }
}
