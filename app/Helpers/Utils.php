<?php

namespace App\Helpers;

use App\Models\M_jobs;
use Illuminate\Support\Facades\DB;

class Utils
{

    public static function getKaryawanWithJobs()
    {
        $karyawans = M_jobs::join('m_karyawans', 'm_jobs.karyawan_id', '=', 'm_karyawans.id')
            ->join('m_type_karyawans', 'm_jobs.type_karyawan_id', '=', 'm_type_karyawans.id')
            ->select(
                'm_karyawans.id as id',
                'm_jobs.type_karyawan_id',
                'm_type_karyawans.type_karyawan',
                'm_karyawans.nama'
            )
            ->get();
        return $karyawans;
    }

    public static function getTarifActive()
    {

        // $data = DB::select("SELECT mt.id, mtk.type_karyawan, mt.tarif_perkg
        //                     FROM m_tarifs mt
        //                     JOIN (
        //                         SELECT MAX(id) AS max_id, type_karyawan_id
        //                         FROM m_tarifs
        //                         WHERE deleted_at IS NULL
        //                         GROUP BY type_karyawan_id
        //                     ) sub ON mt.id = sub.max_id AND mt.type_karyawan_id = sub.type_karyawan_id
        //                     inner join m_type_karyawans mtk ON mtk.id = mt.id
        //                     WHERE mt.deleted_at IS NULL");

        $data = DB::select("SELECT id, type_karyawan, tarif_perkg from (
						SELECT mt.id, mt.tarif_perkg, mtk.type_karyawan, mt.type_karyawan_id, x.is_active_tarif, mt.created_at  
							from m_tarifs mt 
							left join (
	                            SELECT mt2.type_karyawan_id, mtk2.type_karyawan, MAX(mt2.id) AS max_id, true as is_active_tarif
	                                FROM m_tarifs mt2 
	                                inner join m_type_karyawans mtk2 ON mtk2.id = mt2.type_karyawan_id
	                            where mt2.deleted_at is null 
	                            GROUP BY mtk2.type_karyawan, mt2.type_karyawan_id
							) as x on mt.id  = x.max_id
							inner join m_type_karyawans mtk ON mtk.id = mt.type_karyawan_id
 							where mt.deleted_at is null
 							order by mt.created_at desc
					) as x 
					where x.is_active_tarif = true");

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
        // $data = DB::select("SELECT mt.id, mt.type_karyawan, mt.tarif_perkg
        //             FROM m_tarifs mt
        //             JOIN (
        //                 SELECT MAX(id) AS max_id, type_karyawan
        //                 FROM m_tarifs
        //                 WHERE deleted_at IS NULL
        //                 GROUP BY type_karyawan
        //             ) sub ON mt.id = sub.max_id AND mt.type_karyawan = sub.type_karyawan
        //             WHERE mt.deleted_at IS NULL");

        $data = DB::select("SELECT mt.id, mtk.type_karyawan, mt.tarif_perkg
                    FROM m_tarifs mt
                    inner JOIN (
                        SELECT MAX(id) AS max_id, type_karyawan_id
                        FROM m_tarifs
                        WHERE deleted_at IS NULL
                        GROUP BY type_karyawan_id 
                    ) sub ON mt.id = sub.max_id AND mt.type_karyawan_id = sub.type_karyawan_id
					inner join m_type_karyawans mtk ON mtk.id = mt.id
                    WHERE mt.deleted_at IS null");

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
