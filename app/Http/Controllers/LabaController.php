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



        $pembelian = collect(DB::select("WITH pembelian_periode AS (
                                            SELECT 
                                                pt.tbs_type_id,
                                                pt.harga,
                                                pt.netto,
                                                p.periode
                                            FROM pembelian_tbs pt
                                            LEFT JOIN periodes p ON pt.periode_id = p.id
                                            WHERE pt.deleted_at IS null and p.periode = ?
                                        )

                                        SELECT 
                                            mt.type_tbs,
                                            COALESCE(SUM(pp.netto), 0) AS netto,
                                            COALESCE(MAX(pp.harga), 0) AS harga,
                                            COALESCE(SUM(pp.netto), 0) * COALESCE(MAX(pp.harga), 0) AS uang
                                        FROM m_type_tbs mt
                                        LEFT JOIN pembelian_periode pp ON pp.tbs_type_id = mt.id AND pp.periode = ?
                                        GROUP BY mt.type_tbs;
                                        ", [$periodeKe, $periodeKe]));


        $penjualan = collect(DB::select("WITH penjualan_periode AS (
								SELECT 
                                    pj.do_type_id,
                                    pj.netto,
                                    pj.harga,
                                    p.periode
                                from penjualans pj 
                                LEFT JOIN periodes p ON pj.periode_id = p.id 
                                WHERE p.deleted_at IS null and p.periode = ?
                            )
                           	SELECT 
							    dot.delivery_order_type,
                                COALESCE(SUM(pj.netto), 0) AS netto,
                                COALESCE(max(pj.harga), 0) AS harga,
                                COALESCE(SUM(pj.netto), 0) * COALESCE(max(pj.harga), 0) AS uang
							FROM m_delivery_order_types dot
							LEFT JOIN penjualan_periode pj on pj.do_type_id = dot.id AND pj.periode = ?
							GROUP BY dot.delivery_order_type", [$periodeKe, $periodeKe]));


        return view('pages.laba.detail', [
            'pembelian' => $pembelian,
            'penjualan' => $penjualan,
            'periode' => $periode
        ]);
    }
}
