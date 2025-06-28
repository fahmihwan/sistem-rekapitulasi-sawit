<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\Penggajian;
use App\Models\Penggajian_tkbm;
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

        // $query = Penggajian::query();
        $query = Penggajian::with(['penggajian_tkbms.karyawan']);

        // if ($request->filled('tanggal')) {
        //     $query->whereDate('periode_mulai', $tanggal);
        // }
        // if ($search) {
        // $query->where(function ($q) use ($search) {
        // $q->where('periode', 'ILIKE', "%$search%");
        // ->orWhere('harga', 'ILIKE', "%$search%")
        // ->orWhere('uang', 'ILIKE', "%$search%");
        // });
        // }
        // $query->orderBy('periode', 'desc');

        $penggajians = $query->paginate($perPage)->appends($request->query());


        $response = $penggajians->getCollection()->map(function ($penggajian) {
            return [
                'id' => $penggajian->id,
                'periode_awal' => $penggajian->periode_awal,
                'periode_akhir' => $penggajian->periode_akhir,
                'karyawans' => $penggajian->penggajian_tkbms
                    ->groupBy('karyawan_id')
                    ->map(function ($items) {
                        $karyawan = $items->first()->karyawan;
                        return [
                            'id' => $karyawan->id,
                            'nama' => $karyawan->nama,
                        ];
                    })
                    ->values(),
            ];
        });



        // Bungkus ulang koleksi hasil map ke pagination
        $paginatedResponse = new \Illuminate\Pagination\LengthAwarePaginator(
            $response,
            $penggajians->total(),
            $penggajians->perPage(),
            $penggajians->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // return $paginatedResponse;
        // return $paginatedResponse;
        // return Utils::getOpsActive();
        return view('pages.penggajian.index', [
            'items' =>  $paginatedResponse,
            'data_tarif_ops' => Utils::getOpsActive(),
            'get_first_periode' => Periode::orderBy('periode', 'desc')->first()
        ]);
    }



    public function detail_gaji($penggajianid, $karyawanid)
    {

        $karyawan = M_karyawan::with(['main_type_karyawan'])->findOrFail($karyawanid);

        // return $karyawan;

        // $items =  DB::select("SELECT 
        //                         p.id,
        //                         p.tanggal_penjualan,
        //                         p.netto,
        //                         mt.tarif_perkg,
        //                         STRING_AGG(mk.nama, '~') AS tkbms,
        //                         count(pt.id) as total,
        //                         CASE 
        //                             WHEN SUM(CASE WHEN mk.id = ? THEN 1 ELSE 0 END) = 0 THEN true
        //                         ELSE false
        //                         END AS alpha,
        //                         CASE 
        //                             WHEN SUM(CASE WHEN mk.id = ? THEN 1 ELSE 0 END) != 0 THEN (p.netto * mt.tarif_perkg)/count(pt.id)
        //                         ELSE 0
        //                             END AS jumlah_uang,
        //                         CASE 
        //                             WHEN SUM(CASE WHEN mk.id = ? THEN 1 ELSE 0 END) != 0 
        //                             THEN CEIL((p.netto * mt.tarif_perkg) / COUNT(pt.id))
        //                             ELSE 0	
        //                         END AS jumlah_uang_bulat
        //                     from penjualans p 
        //                     inner JOIN penggajian_tkbms pt ON pt.penjualan_id = p.id
        //                     inner join penggajians ps on ps.id = pt.penggajian_id 
        //                     INNER JOIN m_karyawans mk ON mk.id = pt.karyawan_id
        //                     inner join m_tarifs mt on mt.id = p.tarif_tkbm_id 	
        //                     where  
        //                         ps.id =?
        //                         AND p.deleted_at is null
        //                         AND pt.deleted_at is null
        //                     GROUP BY p.id, p.created_at,mt.tarif_perkg
        //                     ", [$karyawanid, $karyawanid, $karyawanid, $penggajianid]);


        // V2 - BUG jumlah uang tidak 0 wloupun dia ga masuk
        // $items = DB::select("SELECT pt.penjualan_id,p.tanggal_penjualan, p.netto, pt.tarif_perkg, pt.tkbm_agg as tkbms, pt.total, pt.jumlah_uang,pt.main_type_karyawan_id,
        //                         CASE
        //                             WHEN pt.main_type_karyawan_id = 2 THEN 
        //                                 SUM(CASE WHEN pt.karyawan_id = ? THEN 1 ELSE 0 END) = 0
        //                             ELSE null
        //                         END AS is_tkbm_alpha
        //                         from penggajian_tkbms pt
        //                             inner join penjualans p on p.id = pt.penjualan_id 
        //                             inner join m_karyawans mk on pt.karyawan_id = mk.id
        //                             where pt.penggajian_id = ? 
        //                         AND p.deleted_at is null AND pt.deleted_at is null 
        //                         group by pt.penjualan_id,p.tanggal_penjualan, p.netto, pt.tarif_perkg, pt.tkbm_agg, pt.total, pt.jumlah_uang, pt.main_type_karyawan_id", [$karyawanid, $penggajianid]);

        $items = DB::select("SELECT 
			x.penjualan_id,x.tanggal_penjualan, x.netto, x.tarif_perkg, x.tkbms, x.total, mp.nama_pabrik,
            x.is_tkbm_alpha,
			case 
				when x.is_tkbm_alpha is null then x.jumlah_uang
				when x.is_tkbm_alpha = true then 0
				when x.is_tkbm_alpha = false then x.jumlah_uang
			end as jumlah_uang,			
			x.main_type_karyawan_id, x.is_tkbm_alpha
		from (
			select pt.penjualan_id,p.tanggal_penjualan, p.netto, pt.tarif_perkg, pt.tkbm_agg as tkbms, pt.total, pt.jumlah_uang,pt.main_type_karyawan_id, pt.pabrik_id,
			CASE
			    WHEN pt.main_type_karyawan_id = 2 THEN 
			        SUM(CASE WHEN pt.karyawan_id = :karyawanid THEN 1 ELSE 0 END) = 0
			    ELSE null
			END AS is_tkbm_alpha
			from penggajian_tkbms pt
				inner join penjualans p on p.id = pt.penjualan_id 
				inner join m_karyawans mk on pt.karyawan_id = mk.id
				where pt.penggajian_id = :penggajianid
			AND p.deleted_at is null AND pt.deleted_at is null 
            and (pt.main_type_karyawan_id = 1 AND pt.karyawan_id = :karyawanid)OR pt.main_type_karyawan_id = 2
			group by pt.penjualan_id,p.tanggal_penjualan, p.netto, pt.tarif_perkg, pt.tkbm_agg, pt.total, pt.jumlah_uang, pt.main_type_karyawan_id,pt.pabrik_id
		) as x
        inner join m_pabriks mp on x.pabrik_id = mp.id
        where x.main_type_karyawan_id = :main_type_karyawan_id", [
            'karyawanid' => $karyawanid,
            'penggajianid' => $penggajianid,
            'main_type_karyawan_id' => $karyawan->main_type_karyawan_id
        ]);



        // return $items;


        // $mapItems = collect($items)->filter(function ($f) use ($karyawan) {
        //     return $f->main_type_karyawan_id == $karyawan->main_type_karyawan_id;
        // })->map(function ($item) {
        //     $item = (array) $item;
        //     $item['tkbms'] = explode('~', $item['tkbms']);
        //     $tanggal = Carbon::parse($item['tanggal_penjualan']);
        //     $item['created_at_formatted'] = $tanggal->translatedFormat('l, d-F-Y H:i') . ' WIB';

        //     $item['tarif_perkg_rp'] = 'Rp ' . number_format($item['tarif_perkg'], 0, ',', '.');
        //     $item['jumlah_uang_rp'] = 'Rp ' . number_format($item['jumlah_uang'], 0, ',', '.');
        //     return $item;
        // });

        $mapItems = collect($items)->map(function ($item) {
            $item = (array) $item;
            $item['tkbms'] = explode('~', $item['tkbms']);
            $tanggal = Carbon::parse($item['tanggal_penjualan']);
            $item['created_at_formatted'] = $tanggal->translatedFormat('l, d-F-Y');

            $item['tarif_perkg_rp'] = 'Rp ' . number_format($item['tarif_perkg'], 0, ',', '.');
            $item['jumlah_uang_rp'] = 'Rp ' . number_format($item['jumlah_uang'], 0, ',', '.');
            return $item;
        });


        // return $mapItems;



        $totalNetto = $mapItems->sum('netto');
        $totalUang   = 'Rp ' . number_format($mapItems->sum('jumlah_uang'), 0, ',', '.');



        $colspanTkbm = $mapItems->max('total');
        $pabrik = M_pabrik::all();


        // return $mapItems;
        return view('pages.penggajian.detail', [
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

        $karyawan = M_karyawan::findOrFail($karyawanid);

        $items = collect(DB::select("SELECT
                    pt.id,
                    p.tanggal_penjualan,
                    mk.nama,
                    -- mk.type_karyawan,
                    p.netto,
                    pt.tarif_perkg as tarif_perkg_rp,
                    pt.tkbm_agg,
                    pt.total,
                    pt.jumlah_uang as jumlah_uang_rp,
                    pt.is_gaji_perhari_dibayarkan,
	                pt.is_gaji_dibayarkan
                from penggajian_tkbms pt 
                inner join m_karyawans mk on mk.id = pt.karyawan_id
                inner join penjualans p on p.id = pt.penjualan_id
                where pt.penggajian_id = ? and pt.karyawan_id = ?", [$penggajianid, $karyawanid]))
            ->map(function ($item) {
                $item = (array) $item;
                $item['tkbms'] = explode('~', $item['tkbm_agg']);
                $tanggal = Carbon::parse($item['tanggal_penjualan']);
                $item['jumlah_uang'] = $item['jumlah_uang_rp'];
                $item['created_at_formatted'] = $tanggal->translatedFormat('l, d-F-Y H:i') . ' WIB';
                $item['tarif_perkg_rp'] = 'Rp ' . number_format($item['tarif_perkg_rp'], 0, ',', '.');
                $item['jumlah_uang_rp'] = 'Rp ' . number_format($item['jumlah_uang_rp'], 0, ',', '.');

                return $item;
            });



        $totalNetto = $items->sum('netto');
        $totalUang   = 'Rp ' . number_format($items->sum('jumlah_uang'), 0, ',', '.');



        return view('pages.penggajian.ambil-gaji-perhari', [
            'items' => $items,
            'colspanTKBM' =>  $items->max('total'),
            'karyawan' => $karyawan,
            'totalNetto' => $totalNetto,
            'totalUang' => $totalUang
        ]);
    }



    public function store(Request $request)
    {

        $validated = $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date',
        ]);

        try {
            DB::beginTransaction();


            // $copy_tkbm = DB::select("SELECT t.id as tkbm_id, 
            //                         t.karyawan_id,
            //                         mk2.nama, mk2.type_karyawan, 
            //                         x.id as penjualan_id,
            //                         x.tanggal_penjualan, x.netto, 
            //                         x.tarif_perkg, 
            //                         x.tkbm_agg, 
            //                         x.total, 
            //                         x.jumlah_uang
            //                     from tkbms t
            //                     inner join  (
            //                             select 
            //                             p.id,
            //                             p.tanggal_penjualan,
            //                             p.netto,
            //                             mt.tarif_perkg,
            //                             string_agg(mk.nama,'~') as tkbm_agg,
            //                             count(t.id) as total,
            //                             p.netto * mt.tarif_perkg / count(t.id) as jumlah_uang
            //                         from penjualans p 
            //                             inner join tkbms t on p.id = t.penjualan_id 
            //                             inner join m_karyawans mk on mk.id = t.karyawan_id 
            //                             inner join m_tarifs mt on mt.id = p.tarif_tkbm_id 
            //                             where p.tanggal_penjualan between ? and ?
            //                                 and t.deleted_at is null
            //                                 and p.deleted_at is null
            //                         group by p.id, p.tanggal_penjualan, mt.tarif_perkg
            //                         order by p.tanggal_penjualan desc
            //                     ) as x on x.id = t.penjualan_id 
            //                     inner join m_karyawans mk2 on mk2.id = t.karyawan_id 
            //                     where t.deleted_at is null", [$validated['periode_awal'], $validated['periode_akhir']]);

            $copy_tkbm = DB::select("SELECT t2.id as tkbm_id, t2.karyawan_id, mk.main_type_karyawan_id, y.* from tkbms t2
			inner join m_karyawans mk on t2.karyawan_id = mk.id 
		    inner join (
			select 
               		x.penjualan_id,x.tanggal_penjualan,x.tarif_perkg,x.type_karyawan_id,x.netto,
               		case 
               			when x.type_karyawan_id = 2 then x.netto * x.tarif_perkg / count(x.tkbm_id) 
               			when x.type_karyawan_id = 1 then x.netto * x.tarif_perkg 
               		end as jumlah_uang,
               		case when x.type_karyawan_id = 2 then count(x.tkbm_id) else 0 end as total,
               		case when x.type_karyawan_id = 2 then string_agg(x.nama, '~') else null end as tkbm_agg,
               		x.pabrik_id
               from (
	               select p.id as penjualan_id, p.tanggal_penjualan,p.netto,mt.tarif_perkg,t.type_karyawan_id,mk.nama,t.id as tkbm_id,p.pabrik_id
					from penjualans p 
						inner join tkbms t on p.id = t.penjualan_id 
						inner join m_karyawans mk on mk.id = t.karyawan_id 
						inner join m_tarifs mt on mt.id  = t.tarif_id 
					where p.tanggal_penjualan between ? and ? and p.deleted_at is null
               ) as x
               group by x.penjualan_id, x.tanggal_penjualan, x.tarif_perkg, x.type_karyawan_id, x.netto,x.pabrik_id
		    ) as y on y.type_karyawan_id = t2.type_karyawan_id and t2.penjualan_id = y.penjualan_id", [$validated['periode_awal'], $validated['periode_akhir']]);

            if (count($copy_tkbm) == 0) {
                return redirect()->back()->withErrors("data pekerja tkbm tidak ditemukan pada periode tersebut ");
            }

            $penggajian = Penggajian::create($validated);

            $arr = [];
            foreach ($copy_tkbm as $key => $value) {
                $arr[] = [
                    'id' => Str::uuid(),
                    'penggajian_id' => $penggajian->id,
                    'tkbm_id' => $value->tkbm_id,
                    'karyawan_id' => $value->karyawan_id,
                    'main_type_karyawan_id' => $value->main_type_karyawan_id,
                    'penjualan_id' => $value->penjualan_id,
                    'type_karyawan_id' => $value->type_karyawan_id,
                    'tarif_perkg' => $value->tarif_perkg,
                    'tkbm_agg' => $value->tkbm_agg,
                    'total' => $value->total,
                    'jumlah_uang' => $value->jumlah_uang,
                    'is_gaji_dibayarkan' => false,
                    'is_gaji_perhari_dibayarkan' => false,
                    'pabrik_id' => $value->pabrik_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // return $arr;
            Penggajian_tkbm::insert($arr);
            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withErrors($th->getMessage());
        }
    }


    public function destroy($id)
    {


        Penggajian::where('id', $id)->delete();
        return redirect()->back();
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
