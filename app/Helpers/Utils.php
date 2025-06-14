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


    public static function mappingTBS_type(string $menu): array|null
    {
        $map = [
            'LAHAN' => ['id' => 1, 'text' => 'TBS LAHAN'],
            'RUMAH' => ['id' => 2, 'text' => 'TBS RUMAH'],
            'RAM'   => ['id' => 3, 'text' => 'TBS RAM'],
        ];

        return $map[$menu] ?? null;
    }
}
