<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M_jobs extends BaseModel
{
    /** @use HasFactory<\Database\Factories\MJobsFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
}
