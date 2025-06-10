<?php

namespace App\Helpers;

class Utils
{
    public static function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    public static function generateKodeUnik($prefix = 'INV')
    {
        return $prefix . '-' . strtoupper(uniqid());
    }
}
