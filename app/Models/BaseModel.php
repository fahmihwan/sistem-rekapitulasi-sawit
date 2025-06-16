<?php

namespace App\Models;

use App\Helpers\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model
{

    public function getTimbanganSecondFormattedAttribute()
    {
        return $this->timbangan_first . ' kg';
    }

    public function getTimbanganFirstFormattedAttribute()
    {
        return $this->timbangan_first . ' kg';
    }

    public function getSortasiFormattedAttribute()
    {
        return $this->sortasi . ' kg';
    }

    public function getNettoFormattedAttribute()
    {
        return $this->netto . ' kg';
    }

    public function getBrutoFormattedAttribute()
    {
        return $this->bruto . ' kg';
    }

    public function getHargaFormattedAttribute()
    {
        return Utils::formatRupiah($this->harga);
    }

    // Format harga
    public function getUangFormattedAttribute()
    {
        return Utils::formatRupiah($this->uang);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)
            ->timezone('Asia/Jakarta')
            ->translatedFormat('l, d-F-Y H:i') . " WIB";
    }
}
