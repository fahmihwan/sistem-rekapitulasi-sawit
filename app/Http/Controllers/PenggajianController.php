<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\Penggajian;
use App\Models\Penggajian_karyawan;
use App\Models\Penggajian_penjualan;
use App\Models\Penjualan;
use App\Models\Periode;
use App\Models\Tkbm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PenggajianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Penggajian::with([
            'penggajian_karyawans:id,penggajian_id,karyawan_id,total_gaji,is_gaji_dibayarkan',
            'penggajian_karyawans.karyawan:id,nama,main_type_karyawan_id',
            'penggajian_karyawans.karyawan.main_type_karyawan:id,type_karyawan',
        ]);



        $penggajians = $query->paginate($perPage)->appends($request->query());



        return view('pages.penggajian.index', [
            'items' =>  $penggajians,
            'data_tarif_ops' => Utils::getOpsActive(),
            'get_first_periode' => Periode::orderBy('periode', 'desc')->first()
        ]);
    }

    public function show_karyawan_penggajian_ajax($penggajian_id)
    {
        $penggajian = DB::select("SELECT distinct p.id as penggajian_id, mk.id as karyawan_id, mk.nama,mtk.type_karyawan from penggajians p 
			 inner join penggajian_penjualans pp on p.id = pp.penggajian_id 
			 inner join penjualans p2 on p2.id = pp.penjualan_id 
			 inner join tkbms t on p2.id = t.penjualan_id 
			 inner join m_karyawans mk on mk.id = t.karyawan_id 
			 inner join m_type_karyawans mtk on mtk.id = mk.main_type_karyawan_id
             WHERE p.id = ? ", [$penggajian_id]);

        return response()->json($penggajian);
    }


    public function detail_gaji($penggajianid, $karyawanid)
    {

        $karyawan = M_karyawan::with(['main_type_karyawan'])->findOrFail($karyawanid);

        // $items = DB::select(" WITH cte_tkbm_borongan_tarif as (
        //     -- AMBIL TKBM + BORONGAN
        // 		 		select t.penjualan_id, p.tanggal_penjualan, count(t.id) as total, 
        //                         mt.tarif_perkg,p.netto,
        //                     case 
        //                       when p.model_kerja_id = 1 then p.netto * mt.tarif_perkg / count(t.id)
        //                       when p.model_kerja_id = 2 then p.tarif_tkbm_borongan 
        //                     end as jumlah_uang,
        //                     t.type_karyawan_id, p.model_kerja_id
        //                             from penjualans p 
        //                             inner join penggajian_penjualans pp on pp.penjualan_id = p.id and pp.penggajian_id = :penggajianid 
        //                             inner join tkbms t on p.id  = t.penjualan_id 
        //                             left join m_tarifs mt on mt.id = t.tarif_id 
        //                           where t.deleted_at is null 
        //                           and t.type_karyawan_id = 2 --TKBM
        //                           group by t.penjualan_id, p.netto,mt.tarif_perkg, p.tanggal_penjualan,t.type_karyawan_id, p.model_kerja_id,p.tarif_tkbm_borongan,p.tarif_sopir_borongan 
        //     ),
        //     cte_sopir_borongan_tarif as (
        //             -- AMBIL SOPIR + BORONGAN
        //               select t.penjualan_id, p.tanggal_penjualan, count(t.id) as total,
        //                             mt.tarif_perkg,p.netto, 
        // 	                        case 
        // 	                          when p.model_kerja_id = 1 then p.netto * mt.tarif_perkg 
        // 	                          when p.model_kerja_id = 2 then p.tarif_sopir_borongan 
        // 	                        end as jumlah_uang,
        // 	                        t.type_karyawan_id, p.model_kerja_id
        //                         from penjualans p 
        //                         inner join penggajian_penjualans pp on pp.penjualan_id = p.id and pp.penggajian_id = :penggajianid
        //                         inner join tkbms t on p.id  = t.penjualan_id 
        //                         left join m_tarifs mt on mt.id = t.tarif_id 
        //                       where t.deleted_at is null 
        //                       and t.type_karyawan_id = 1 --SOPIR
        //                       group by t.penjualan_id, p.netto,mt.tarif_perkg, p.tanggal_penjualan,t.type_karyawan_id, p.model_kerja_id,p.tarif_sopir_borongan
        //     ),
        //     cte_penjualan AS (
        //       SELECT p.id AS penjualan_id, p.tanggal_penjualan, p.netto, p.created_at, p.model_kerja_id,mp.nama_pabrik
        //       FROM penjualans p
        //       inner join penggajian_penjualans pp on pp.penjualan_id = p.id and pp.penggajian_id = :penggajianid
        //       inner join m_pabriks mp on mp.id = p.pabrik_id
        //     ),
        //     cte_karyawan_ikut AS (
        //       SELECT t.*, mk.nama, mk.main_type_karyawan_id
        //       FROM tkbms t
        //       inner join penggajian_penjualans pp on pp.penjualan_id = t.penjualan_id and pp.penggajian_id = :penggajianid
        //       JOIN m_karyawans mk ON mk.id = t.karyawan_id
        //       WHERE t.karyawan_id = :karyawanid
        //     )
        //     select * from (
        //       SELECT 
        //         p.penjualan_id,k.karyawan_id,k.nama,k.type_karyawan_id,
        //         case 
        //           when k.type_karyawan_id = 1 then 'SOPIR'
        //           when k.type_karyawan_id = 2 then 'TKBM'
        //           when k.type_karyawan_id is null then 'TKBM'
        //         end as keterangan,
        //         k.main_type_karyawan_id, cte_t.total,
        //         p.tanggal_penjualan,p.netto,p.model_kerja_id,p.nama_pabrik, 
        //         CASE 
        //           WHEN k.type_karyawan_id = 1 THEN cte_s.tarif_perkg
        //           WHEN k.type_karyawan_id = 2 THEN cte_t.tarif_perkg
        //         END AS tarif_perkg,
        //         xx.tkbms,
        //         xx_sopir.sopir,
        //         CASE 
        //           WHEN k.type_karyawan_id = 1 THEN cte_s.jumlah_uang
        //           WHEN k.type_karyawan_id = 2 THEN cte_t.jumlah_uang
        //           WHEN k.type_karyawan_id is null THEN 0
        //         END AS jumlah_uang,
        //         CASE 
        //           WHEN k.karyawan_id IS NULL THEN true ELSE false
        //         END AS is_tkbm_alpha
        //       FROM cte_penjualan p
        //       LEFT JOIN cte_karyawan_ikut k ON p.penjualan_id = k.penjualan_id
        //       LEFT JOIN cte_tkbm_borongan_tarif cte_t ON cte_t.penjualan_id = p.penjualan_id
        //       LEFT JOIN cte_sopir_borongan_tarif cte_s ON cte_s.penjualan_id = p.penjualan_id
        //       left join (
        //       		select t3.penjualan_id, p3.tanggal_penjualan,string_agg(mk3.nama,'~') as tkbms
        // 				from penjualans p3
        // 				inner join penggajian_penjualans pp3 on pp3.penjualan_id = p3.id and pp3.penggajian_id = :penggajianid
        // 				inner join tkbms t3 on p3.id  = t3.penjualan_id
        // 				inner join m_karyawans mk3 on mk3.id  = t3.karyawan_id  
        // 				where t3.deleted_at is null 
        // 				and t3.type_karyawan_id = 2 --TKBM
        // 				group by t3.penjualan_id, p3.netto, p3.tanggal_penjualan, p3.model_kerja_id,p3.tarif_tkbm_borongan,p3.tarif_sopir_borongan 

        //       ) as xx on xx.penjualan_id = p.penjualan_id
        //        left join (
        //       			select t3.penjualan_id, p3.tanggal_penjualan,mk3.nama as sopir
        // 					from penjualans p3 
        // 					inner join penggajian_penjualans pp3 on pp3.penjualan_id = p3.id and pp3.penggajian_id = :penggajianid
        // 					inner join tkbms t3 on p3.id  = t3.penjualan_id 
        // 					inner join m_karyawans mk3 on mk3.id  = t3.karyawan_id  
        // 					where t3.deleted_at is null 
        // 				and t3.type_karyawan_id = 1 --SOPIR 
        //       ) as xx_sopir on xx_sopir.penjualan_id = p.penjualan_id
        //       ORDER BY p.created_at DESC
        //     ) as x
        // 	 WHERE NOT (x.model_kerja_id = 2 AND x.is_tkbm_alpha = true)
        // 	 AND NOT (x.main_type_karyawan_id = 1 AND x.is_tkbm_alpha = true)", [
        //     'karyawanid' => $karyawanid,
        //     'penggajianid' => $penggajianid
        // ]);


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

                $item['tarif_perkg_rp'] = 'Rp ' . number_format($item['tarif_perkg'], 0, ',', '.');
                $item['jumlah_uang_rp'] = 'Rp ' . number_format($item['jumlah_uang'], 0, ',', '.');
                return $item;
            });



        $totalNetto = $mapItems->where('model_kerja_id', 1)->sum('netto');
        $totalUang   = $mapItems->where('model_kerja_id', 1)->sum('jumlah_uang');
        $colspanTkbm = $mapItems->max('total');





        $pabrik = M_pabrik::all();


        // return $mapItems;
        return view('pages.penggajianv2.detail', [
            'items' => $mapItems,
            'colspanTKBM' => $colspanTkbm,
            'colspanPABRIK' => count($pabrik),
            'pabriks' => $pabrik,
            'karyawan' => $karyawan,
            'totalNetto' => $totalNetto,
            'totalUang' => $totalUang,
        ]);
    }


    public function ambil_gaji_perhari($penggajianid, $karyawanid)
    {

        $karyawan = M_karyawan::with(['main_type_karyawan'])->findOrFail($karyawanid);
        // return $karyawan;



        $items = DB::select("SELECT DISTINCT ON (p.id) 
                    p.id,t.penjualan_id,t.karyawan_id,mk.nama,t.type_karyawan_id,
                    CASE 
                        WHEN t.type_karyawan_id = 1 THEN 'SOPIR'
                        WHEN t.type_karyawan_id = 2 THEN 'TKBM'
                    END AS keterangan,
                    mk.main_type_karyawan_id,
                    t.jumlah_tkbm AS total,p.tanggal_penjualan,p.netto,t.model_kerja_id,mp.nama_pabrik,mt.tarif_perkg,t.tkbm_agg as tkbms,mk_sopir.nama as sopir,t.jumlah_uang
                FROM penggajians ps
                    INNER JOIN penggajian_penjualans pp ON ps.id = pp.penggajian_id 
                    INNER JOIN penjualans p ON p.id = pp.penjualan_id 
                    INNER JOIN m_pabriks mp ON p.pabrik_id = mp.id 
                    LEFT JOIN tkbms t ON t.penjualan_id = p.id 
--                    LEFT JOIN tkbms t_a ON t_a.penjualan_id = p.id AND t_a.karyawan_id = :karyawanid 
                    LEFT JOIN m_karyawans mk ON mk.id = t.karyawan_id
                    LEFT JOIN m_tarifs mt ON mt.id = t.tarif_id 
                    INNER JOIN m_karyawans mk_sopir ON mk_sopir.id = p.sopir_id
                    where ps.id = :penggajianid and t.karyawan_id = :karyawanid and p.model_kerja_id = 1
                ORDER BY p.id, p.created_at DESC;", [
            'karyawanid' => $karyawanid,
            'penggajianid' => $penggajianid
        ]);

        $items = collect($items)
            ->map(function ($item) {
                $item = (array) $item;
                $item['tkbms'] = explode('~', $item['tkbms']);
                $tanggal = Carbon::parse($item['tanggal_penjualan']);
                $item['created_at_formatted'] = $tanggal->translatedFormat('l, d-F-Y');

                $item['tarif_perkg_rp'] = 'Rp ' . number_format($item['tarif_perkg'], 0, ',', '.');
                $item['jumlah_uang_rp'] = 'Rp ' . number_format($item['jumlah_uang'], 0, ',', '.');
                return $item;
            });



        // return $items;
        $totalNetto = $items->where('model_kerja_id', 1)->sum('netto');
        $totalUang   = $items->where('model_kerja_id', 1)->sum('jumlah_uang');


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



        $penggajian_karyawan =  Penggajian_karyawan::where([
            ['penggajian_id', '=', $penggajianid],
            ['karyawan_id', '=', $karyawanid],
        ])->first();





        return view('pages.penggajianv2.ambil-gaji-perhari', [
            'items' => $items,
            'penggajian_karyawan' => $penggajian_karyawan,
            'colspanTKBM' =>  $items->max('total'),
            'karyawan' => $karyawan,
            'totalNetto' => $totalNetto,
            'totalUang' => $totalUang,
            'pinjaman_saat_ini' => $pinjaman_saat_ini
        ]);
    }


    public function update_ambil_gaji(Request $request, $penggajianid, $karyawanid)
    {

        // return $request->all();

        try {
            DB::beginTransaction();

            $request->merge([
                'karyawan_id' => $karyawanid,
                'penanggung_jawab_id' => auth()->user()->id
            ]);


            $validated = $request->validate([
                'karyawan_id' => 'required|exists:m_karyawans,id',
                'total_gaji' => 'nullable|integer',
                'pinjaman_saat_ini' => 'nullable|integer',
                'potongan_pinjaman' => 'nullable|integer',
                'sisa_pinjaman' => 'nullable|integer',
                'gaji_yang_diterima' => 'nullable|integer',
                'penanggung_jawab_id' => 'required|exists:users,id',
                'is_gaji_dibayarkan' => 'boolean',
            ]);

            $penggajian_karyawan = Penggajian_karyawan::where([
                ['penggajian_id', '=', $penggajianid],
                ['karyawan_id', '=', $karyawanid],
            ]);

            $message = '';
            if ($penggajian_karyawan->exists()) {
                $penggajian_karyawan->update($validated);
                $message = 'Gaji karyawan barhasil di tambahkan!';
            } else {
                $validated['id'] = (string) Str::uuid();
                $validated['penggajian_id'] = $penggajianid;
                $penggajian_karyawan->insert($validated);
                $message = 'Gaji karyawan berhasil di perbarui!';
            }





            DB::commit();
            return redirect()->back()->with('success', $message);;
        } catch (\Throwable $th) {

            DB::rollBack();
            return redirect()->back()->withErrors($th->getMessage());
        }
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date',
        ]);



        try {
            DB::beginTransaction();


            $penggajian_id = Penggajian::create($validated)->id;

            $get_penjualan_id = Penjualan::whereBetween('tanggal_penjualan', [$validated['periode_awal'], $validated['periode_akhir']])->select('id')->get();

            $penggajian_penjualan = $get_penjualan_id->map(function ($item) use ($penggajian_id) {
                return [
                    'id' => Str::uuid(),
                    'penggajian_id' => $penggajian_id,
                    'penjualan_id' => $item->id,
                ];
            });


            Penggajian_penjualan::insert($penggajian_penjualan->toArray());
            DB::commit();

            // $arr = [];
            // foreach ($copy_tkbm as $key => $value) {
            //     $arr[] = [
            //         'id' => Str::uuid(),
            //         'penggajian_id' => $penggajian->id,
            //         'tkbm_id' => $value->tkbm_id,
            //         'karyawan_id' => $value->karyawan_id,
            //         'main_type_karyawan_id' => $value->main_type_karyawan_id,
            //         'penjualan_id' => $value->penjualan_id,
            //         'type_karyawan_id' => $value->type_karyawan_id,
            //         'tarif_perkg' => $value->tarif_perkg,
            //         'tkbm_agg' => $value->tkbm_agg,
            //         'total' => $value->total,
            //         'jumlah_uang' => $value->jumlah_uang,
            //         'is_gaji_dibayarkan' => false,
            //         'is_gaji_perhari_dibayarkan' => false,
            //         'pabrik_id' => $value->pabrik_id,
            //         'created_at' => now(),
            //         'updated_at' => now(),
            //     ];
            // }
            // Penggajian_tkbm::insert($arr);

            // $pinjaman_uang = DB::select("SELECT pt.karyawan_id, sum(pt.jumlah_uang) as jumlah_uang, case
            //                         when x.sisa_pinjaman is not null then  x.sisa_pinjaman
            //                         else 0
            //                     end as sisa_pinjaman
            //                     from penggajian_tkbms pt 
            //                     left join (
            //                         SELECT
            //                             pu.karyawan_id,(SUM(pu.nominal_peminjaman) - SUM(pu.nominal_pengembalian)) AS sisa_pinjaman	
            //                             FROM pinjaman_uangs pu
            //                             WHERE pu.deleted_at IS null
            //                         GROUP BY pu.karyawan_id
            //                     ) as x on x.karyawan_id = pt.karyawan_id 
            //                     inner join penjualans p on p.id = pt.penjualan_id 
            //                     where p.tanggal_penjualan between ? and ? and p.deleted_at is null
            //                     group by pt.karyawan_id, x.sisa_pinjaman,x.karyawan_id", [$validated['periode_awal'], $validated['periode_akhir']]);



            // $mapPinjaman = [];
            // foreach ($pinjaman_uang as $key => $value) {
            //     $mapPinjaman[] = [
            //         'id' => Str::uuid(),
            //         'penggajian_id' => $penggajian->id,
            //         "karyawan_id" => $value->karyawan_id,
            //         "total_gaji" => $value->jumlah_uang,
            //         "pinjaman_saat_ini" => $value->sisa_pinjaman,
            //         "potongan_pinjaman" => 0,
            //         "sisa_pinjaman" => $value->sisa_pinjaman,
            //         "gaji_yang_diterima" => 0,
            //         'is_gaji_dibayarkan' => null,
            //         "penanggung_jawab_id" => null
            //     ];
            // }
            // Penggajian_karyawan::insert($mapPinjaman);

            return redirect()->back()->with('success', 'Transaksi berhasil disimpan!');;
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withErrors($th->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            Penggajian::where('id', $id)->forceDelete();
            // Penggajian_tkbm::where('penggajian_id', $id)->forceDelete();

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus!');;
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withErrors($th->getMessage());
        }
    }
}





// public function store_v1(Request $request)
// {

//     $validated = $request->validate([
//         'periode_awal' => 'required|date',
//         'periode_akhir' => 'required|date',
//     ]);

//     // return $validated;
//     try {
//         DB::beginTransaction();

//         $copy_tkbm = Tkbm::select([
//             'tkbms.karyawan_id',
//             'pj.id as penjualan_id',
//             'pj.tanggal_penjualan',
//             'pj.tarif_tkbm_id',
//             'pj.netto',
//         ])
//             ->join('penjualans as pj', 'pj.id', '=', 'tkbms.penjualan_id')
//             ->whereBetween('tanggal_penjualan', [$validated['periode_awal'], $validated['periode_akhir']])
//             ->get();

//         // return $copy_tkbm;

//         if (count($copy_tkbm) == 0) {
//             return redirect()->back()->withErrors("data pekerja tkbm tidak ditemukan pada periode tersebut ");
//         }

//         $penggajian = Penggajian::create($validated);

//         $mapped = $copy_tkbm->map(function ($item) use ($penggajian) {
//             return [
//                 'id' => (string) Str::uuid(),
//                 'penggajian_id' => $penggajian->id,
//                 'karyawan_id' => $item->karyawan_id,
//                 'penjualan_id' => $item->penjualan_id,
//                 'tanggal_penjualan' => $item->tanggal_penjualan,
//                 'tarif_tkbm_id' => $item->tarif_tkbm_id,
//                 'netto' => $item->netto,
//                 'is_gaji_dibayarkan' => false,
//                 'is_gaji_perhari_dibayarkan' => false,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ];
//         });

//         Penggajian_tkbm::insert($mapped->toArray());
//         DB::commit();
//         return redirect()->back();
//     } catch (\Throwable $th) {
//         return redirect()->back()->withErrors($th->getMessage());
//     }
// }
