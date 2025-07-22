<?php

namespace App\Http\Controllers;

use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\Penggajian_karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PdfExportController extends Controller
{
    public function gaji_karyawan($penggajianid, $karyawanid)
    {

        $karyawan = M_karyawan::with(['main_type_karyawan'])->findOrFail($karyawanid);


        $items = DB::select("SELECT * from (
               SELECT
               		DISTINCT ON (p.id)
                    p.id,
                    t.penjualan_id,t_a.karyawan_id,mk.nama,t.type_karyawan_id,
                    CASE 
                        WHEN t_a.type_karyawan_id = 1 THEN 'SOPIR'
                        WHEN t_a.type_karyawan_id = 2 THEN 'TKBM'
                    END AS keterangan,
                    mk.main_type_karyawan_id,
                    t.jumlah_tkbm AS total,p.tanggal_penjualan,p.netto,t_a.model_kerja_id,mp.nama_pabrik, mt.tarif_perkg,
                    t.tkbm_agg as tkbms,mk_sopir.nama as sopir,t_a.jumlah_uang,
                    case 
                        when t_a.karyawan_id is null then true
                        when t_a.karyawan_id is not null then false
                    end AS is_tkbm_alpha
                FROM penggajians ps
                    INNER JOIN penggajian_penjualans pp ON ps.id = pp.penggajian_id 
                    INNER JOIN penjualans p ON p.id = pp.penjualan_id 
                    INNER JOIN m_pabriks mp ON p.pabrik_id = mp.id 
                    LEFT JOIN tkbms t ON t.penjualan_id = p.id 
                    LEFT JOIN tkbms t_a ON t_a.penjualan_id = p.id AND t_a.karyawan_id = :karyawanid 
                    LEFT JOIN m_karyawans mk ON mk.id = t_a.karyawan_id
                    left JOIN m_tarifs mt ON mt.id = t_a.tarif_id 
                    INNER JOIN m_karyawans mk_sopir ON mk_sopir.id = p.sopir_id
                    where ps.id = :penggajianid and (p.model_kerja_id = 1 or t_a.karyawan_id = :karyawanid)
               ) as x
               order by x.tanggal_penjualan desc", [
            'karyawanid' => $karyawanid,
            'penggajianid' => $penggajianid
        ]);


        $mapItems = collect($items)
            ->map(function ($item) {
                $item = (array) $item;
                $item['tkbms'] = explode('~', $item['tkbms']);
                $tanggal = Carbon::parse($item['tanggal_penjualan']);
                $item['created_at_formatted'] = $tanggal->translatedFormat('l, d-F-Y');

                $item['tarif_perkg_rp'] =  number_format($item['tarif_perkg'], 0, ',', '.');
                $item['jumlah_uang_rp'] = 'Rp ' . number_format($item['jumlah_uang'], 0, ',', '.');
                return $item;
            });


        $totalNetto = $mapItems->where('model_kerja_id', 1)->sum('netto');

        $colspanTkbm = $mapItems->max('total');

        $jumlah_uang = $mapItems->where('model_kerja_id', 1)->sum('jumlah_uang');

        $pabrik = M_pabrik::all();

        $penggajian_karyawan =  Penggajian_karyawan::where([
            ['penggajian_id', '=', $penggajianid],
            ['karyawan_id', '=', $karyawanid],
        ])->first();






        $pinjaman_saat_ini = DB::select("SELECT
                            pu.karyawan_id,
                            mk.nama,
                            mtk.type_karyawan,
                            (SUM(pu.nominal_peminjaman) - SUM(pu.nominal_pengembalian)) AS sisa_pinjaman
                        FROM pinjaman_uangs pu
                        INNER JOIN m_karyawans mk ON mk.id = pu.karyawan_id
                        INNER JOIN m_type_karyawans mtk ON mtk.id = mk.main_type_karyawan_id
                        WHERE pu.deleted_at IS null
                        and pu.karyawan_id = ?
                        GROUP BY pu.karyawan_id, mk.nama, mtk.type_karyawan", [$karyawanid]);
        if (count($pinjaman_saat_ini) == 1) {
            $pinjaman_saat_ini = $pinjaman_saat_ini[0]->sisa_pinjaman;
        } else {
            $pinjaman_saat_ini = 0;
        }




        $pdf = Pdf::loadView('pages.export_pdf.penggajian-pdf', [
            'items' => $mapItems,
            'colspanTKBM' => $colspanTkbm,
            'colspanPABRIK' => count($pabrik),
            'pabriks' => $pabrik,
            'karyawan' => $karyawan,
            'totalNetto' => $totalNetto,
            'totalUang' => $jumlah_uang,
            'penggajian_karyawan' => $penggajian_karyawan,
            'pinjaman_saat_ini' => $pinjaman_saat_ini
        ])->setPaper('a4', 'portrait');
        // landscape
        // portrait
        return $pdf->download('gaji-karyawan.pdf');


        // return $mapItems;
        return view('pages.export_pdf.penggajian-pdf', [
            'items' => $mapItems,
            'colspanTKBM' => $colspanTkbm,
            'colspanPABRIK' => count($pabrik),
            'pabriks' => $pabrik,
            'karyawan' => $karyawan,
            'totalNetto' => $totalNetto,
            'totalUang' => $jumlah_uang,
        ]);
    }
}
