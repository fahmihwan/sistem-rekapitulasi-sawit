<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function laporan_semua_stok(Request $request)
    {

        // $data = DB::select("SELECT 
        //                 x.periode_id,x.periode,x.periode_mulai,x.periode_berakhir,x.created_at,
        //                 SUM(x.bruto_masuk) AS total_bruto_masuk,SUM(x.netto_masuk) AS total_netto_masuk,SUM(x.bruto_keluar) AS total_bruto_keluar,SUM(x.netto_keluar) AS total_netto_keluar
        //                 FROM (
        // 				 SELECT 
        // 				 	p.id as periode_id,p.periode,p.periode_mulai,p.periode_berakhir,p.created_at,
        //                     sum(b.bruto) AS bruto_masuk,sum(b.netto) AS netto_masuk,0 AS bruto_keluar,0 AS netto_keluar
        //                 FROM pembelian_tbs b
        //                 inner join periodes p on p.id  = b.periode_id 
        //                 group by p.id,p.periode, p.periode_mulai,p.periode_berakhir,p.created_at
        // 				UNION ALL
        //                 SELECT
        // 					p.id as periode_id,p.periode,p.periode_mulai,p.periode_berakhir,p.created_at,
        //                     0 AS bruto_masuk,0 AS netto_masuk,sum(s.bruto) AS bruto_keluar,sum(s.netto) AS netto_keluar
        //                 FROM penjualans s
        //                 inner join periodes p on p.id  = s.periode_id 
        //            		group by p.id,p.periode, p.periode_mulai,p.periode_berakhir,p.created_at
        //            	   ) AS x
        //             GROUP BY x.periode_id, x.periode,x.periode_mulai,x.periode_berakhir,x.created_at;");

        // Ambil bagian pembelian
        $pembelian = DB::table('pembelian_tbs as b')
            ->join('periodes as p', 'p.id', '=', 'b.periode_id')
            ->select(
                'p.id as periode_id',
                'p.periode',
                'p.periode_mulai',
                'p.periode_berakhir',
                'p.created_at',
                DB::raw('SUM(b.bruto) AS bruto_masuk'),
                DB::raw('SUM(b.netto) AS netto_masuk'),
                DB::raw('0 AS bruto_keluar'),
                DB::raw('0 AS netto_keluar')
            )
            ->groupBy('p.id', 'p.periode', 'p.periode_mulai', 'p.periode_berakhir', 'p.created_at');

        // Ambil bagian penjualan
        $penjualan = DB::table('penjualans as s')
            ->join('periodes as p', 'p.id', '=', 's.periode_id')
            ->select(
                'p.id as periode_id',
                'p.periode',
                'p.periode_mulai',
                'p.periode_berakhir',
                'p.created_at',
                DB::raw('0 AS bruto_masuk'),
                DB::raw('0 AS netto_masuk'),
                DB::raw('SUM(s.bruto) AS bruto_keluar'),
                DB::raw('SUM(s.netto) AS netto_keluar')
            )
            ->groupBy('p.id', 'p.periode', 'p.periode_mulai', 'p.periode_berakhir', 'p.created_at');

        // Gabungkan dengan UNION ALL
        $union = $pembelian->unionAll($penjualan);

        // Bungkus union sebagai subquery
        $subquery = DB::table(DB::raw("({$union->toSql()}) as x"))
            ->mergeBindings($union)
            ->select(
                'x.periode_id',
                'x.periode',
                'x.periode_mulai',
                'x.periode_berakhir',
                'x.created_at',
                DB::raw('SUM(x.bruto_masuk) as total_bruto_masuk'),
                DB::raw('SUM(x.netto_masuk) as total_netto_masuk'),
                DB::raw('SUM(x.bruto_keluar) as total_bruto_keluar'),
                DB::raw('SUM(x.netto_keluar) as total_netto_keluar')
            )
            ->groupBy(
                'x.periode_id',
                'x.periode',
                'x.periode_mulai',
                'x.periode_berakhir',
                'x.created_at'
            );

        // Eksekusi dan ambil data



        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = $subquery;

        if ($request->filled('tanggal')) {
            $query->whereDate('periode_mulai', $tanggal);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                // $q->where('periode', 'ILIKE', "%$search%");
                // ->orWhere('harga', 'ILIKE', "%$search%")
                // ->orWhere('uang', 'ILIKE', "%$search%");
            });
        }
        $query->orderBy('x.periode', 'desc');

        $data = $query->paginate($perPage)->appends($request->query());
        // return $data;

        return view('pages.laporan.laporan-stock', [
            'items' => $data
        ]);
    }
}
