<?php

namespace App\Http\Controllers;

use App\Models\Pembelian_tbs;
use Illuminate\Http\Request;

class TbsController extends Controller
{
    public function index(Request $request, string $menu)
    {

        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $_MAPPING_ID = NULL;
        $_MAPPING_TEXT = NULL;
        if ($menu == 'LAHAN') {
            $_MAPPING_TEXT = "TBS LAHAN";
            $_MAPPING_ID = 1;
        } else if ($menu == 'RUMAH') {
            $_MAPPING_TEXT = "TBS RUMAH";
            $_MAPPING_ID = 2;
        } else if ($menu == 'RAM') {
            $_MAPPING_TEXT = "TBS RAM";
            $_MAPPING_ID = 3;
        } else {
            return "NOT FOUND";
        }

        // $data = Pembelian_tbs::where('tbs_type_id', $_MAPPING_ID)->paginate($perPage);
        // ->appends(['per_page' => $perPage]);;



        $query = Pembelian_tbs::where('tbs_type_id', $_MAPPING_ID);

        // Filter tanggal (jika diisi)
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $tanggal);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_customer', 'ILIKE', "%$search%")
                    ->orWhere('tbs_type_id', 'ILIKE', "%$search%")
                    ->orWhere('netto', 'ILIKE', "%$search%")
                    ->orWhere('harga', 'ILIKE', "%$search%")
                    ->orWhere('uang', 'ILIKE', "%$search%")
                    ->orWhere('timbangan_first', 'ILIKE', "%$search%")
                    ->orWhere('timbangan_second', 'ILIKE', "%$search%")
                    ->orWhere('bruto', 'ILIKE', "%$search%")
                    ->orWhere('sortasi', 'ILIKE', "%$search%");
            });
        }

        $data = $query->paginate($perPage)->appends($request->query());

        // dd($data);
        return view('pages.pembelian_TBS.index', [
            'items' =>  $data,
            'title' => $_MAPPING_TEXT,
            'menu' => $menu

        ]);
    }
}
