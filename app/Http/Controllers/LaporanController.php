<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function laporan_semua_stok()
    {
        $data = DB::select("SELECT 
                        x.bulan,
                        x.tahun,
                        SUM(x.bruto_masuk) AS total_bruto_masuk,
                        SUM(x.netto_masuk) AS total_netto_masuk,
                        SUM(x.bruto_keluar) AS total_bruto_keluar,
                        SUM(x.netto_keluar) AS total_netto_keluar,
                        SUM(x.bruto_masuk) - SUM(x.bruto_keluar) AS saldo_bruto_bulan_ini,
                        SUM(x.netto_masuk) - SUM(x.netto_keluar) AS saldo_netto_bulan_ini
                    FROM (
                        SELECT 
                            EXTRACT(MONTH FROM b.created_at) AS bulan,
                            EXTRACT(YEAR FROM b.created_at) AS tahun,
                            b.bruto AS bruto_masuk,
                            b.netto AS netto_masuk,
                            0 AS bruto_keluar,
                            0 AS netto_keluar
                        FROM pembelian_tbs b

                        UNION ALL

                        SELECT
                            EXTRACT(MONTH FROM s.created_at) AS bulan,
                            EXTRACT(YEAR FROM s.created_at) AS tahun,
                            0 AS bruto_masuk,
                            0 AS netto_masuk,
                            s.bruto AS bruto_keluar,
                            s.netto AS netto_keluar
                        FROM penjualans s
                    ) AS x
                    GROUP BY x.tahun, x.bulan
                    ORDER BY x.tahun, x.bulan;");

        // return $data;

        return view('pages.laporan.laporan-stock', [
            'items' => $data
        ]);
    }
}
