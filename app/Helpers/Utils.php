<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Utils
{

    public static function getTarifActive()
    {
        $data = DB::select("SELECT mt.id, mt.type_karyawan, mt.tarif_perkg
                            FROM m_tarifs mt
                            JOIN (
                                SELECT MAX(id) AS max_id, type_karyawan
                                FROM m_tarifs
                                WHERE deleted_at IS NULL
                                GROUP BY type_karyawan
                            ) sub ON mt.id = sub.max_id AND mt.type_karyawan = sub.type_karyawan
                            WHERE mt.deleted_at IS NULL");


        $result = [
            'tarif_sopir_id' => null,
            'tarif_tkbm_id' => null,
            'tarif_tkbm_perkg' => 0,
            'tarif_sopir_perkg' => 0
        ];

        foreach ($data as $item) {
            if ($item->type_karyawan == 'SOPIR') {
                $result['tarif_sopir_id'] = $item->id;
                $result['tarif_sopir_perkg'] = $item->tarif_perkg;
            } elseif ($item->type_karyawan == 'TKBM') {
                $result['tarif_tkbm_id'] = $item->id;
                $result['tarif_tkbm_perkg'] = $item->tarif_perkg;
            }
        }

        return $result;
    }

    public static function getListTarif()
    {
        $data = DB::select("SELECT mt.id, mt.type_karyawan, mt.tarif_perkg
                    FROM m_tarifs mt
                    JOIN (
                        SELECT MAX(id) AS max_id, type_karyawan
                        FROM m_tarifs
                        WHERE deleted_at IS NULL
                        GROUP BY type_karyawan
                    ) sub ON mt.id = sub.max_id AND mt.type_karyawan = sub.type_karyawan
                    WHERE mt.deleted_at IS NULL");

        return $data;
    }

    public static function getOpsActive()
    {
        $data = DB::select("SELECT ops2.id, ops2.ops, ops2.deleted_at, x.is_active_ops, ops2.created_at from m_ops ops2 
                inner join (
                    select max(id) as max_id, true as is_active_ops from m_ops ops where ops.deleted_at is null
                ) as x on ops2.id = x.max_id
                where ops2.deleted_at is null
                order by ops2.id desc");


        $result = [
            // 'ops_id' => null,
            // 'ops' =>  0
        ];

        foreach ($data as $item) {
            $result[] = [
                'ops_id' => $item->id,
                'ops' => $item->ops
            ];
        }

        return $result;
    }





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

    public static function mappingDO_type(string $menu): array|null
    {
        $map = [
            'PLASMA' => ['id' => 1, 'text' => 'PLASMA'],
            'LU' => ['id' => 2, 'text' => 'LU (Lahan Usaha)'],
        ];

        return $map[$menu] ?? null;
    }
}
