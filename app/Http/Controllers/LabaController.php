<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabaController extends Controller
{
    public function index(Request $request)
    {

        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Periode::query();

        if ($request->filled('tanggal')) {
            $query->whereDate('periode_mulai', $tanggal);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('periode', 'ILIKE', "%$search%");
                // ->orWhere('harga', 'ILIKE', "%$search%")
                // ->orWhere('uang', 'ILIKE', "%$search%");
            });
        }
        $query->orderBy('periode', 'desc');

        $data = $query->paginate($perPage)->appends($request->query());

        return view('pages.laba.index', [
            'items' =>  $data,
            'get_first_periode' => Periode::orderBy('periode', 'desc')->first()
        ]);
    }

    public function detail($id)
    {



        $periode = Periode::find($id);
        if (!$periode) {
            return redirect('/periode')->with('error', 'Data tidak ditemukan');
        }
        $periodeKe = $periode->periode;

        $pembelian = DB::select("SELECT 
                            mt.type_tbs,
                            COALESCE(SUM(pt.netto), 0) AS netto,
                            COALESCE(pt.harga, 0) AS harga,
                            COALESCE(SUM(pt.netto), 0) * COALESCE(pt.harga, 0) AS uang
                        FROM m_type_tbs mt
                        LEFT JOIN pembelian_tbs pt ON pt.tbs_type_id = mt.id
                        LEFT JOIN periodes p ON pt.periode_id = p.id and p.periode = ?
                        GROUP BY mt.type_tbs, COALESCE(pt.harga, 0);", [$periodeKe]);

        $penjualan = DB::select("SELECT 
                            dot.delivery_order_type,
                            COALESCE(SUM(pj.netto), 0) AS netto,
                            COALESCE(pj.harga, 0) AS harga,
                            COALESCE(SUM(pj.netto), 0) * COALESCE(pj.harga, 0) AS uang
                        FROM m_delivery_order_types dot
                        LEFT JOIN penjualans pj ON pj.do_type_id = dot.id
                        LEFT JOIN periodes p ON pj.periode_id = p.id and p.periode = ?
                        GROUP BY dot.delivery_order_type, COALESCE(pj.harga, 0)
                        ORDER BY dot.delivery_order_type;", [$periodeKe]);


        return view('pages.laba.detail', [
            'pembelian' => $pembelian,
            'penjualan' => $penjualan
        ]);
    }
}
