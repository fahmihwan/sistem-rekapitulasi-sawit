<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{

    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Periode::query();
        // $query = Penjualan::with([
        //     'pabrik:id,nama_pabrik',
        //     'sopir:id,nama',
        //     'tkbms:id,karyawan_id,penjualan_id',
        //     'tkbms.karyawan:id,nama'
        // ])->where('do_type_id', $DO_TYPE['id']);
        // // return $query->get();



        // if ($request->filled('tanggal')) {
        //     $query->whereDate('created_at', $tanggal);
        // }
        // if ($search) {
        //     $query->where(function ($q) use ($search) {
        //         $q->where('netto', 'ILIKE', "%$search%")
        //             ->orWhere('harga', 'ILIKE', "%$search%")
        //             ->orWhere('uang', 'ILIKE', "%$search%")
        //             ->orWhere('timbangan_first', 'ILIKE', "%$search%")
        //             ->orWhere('timbangan_second', 'ILIKE', "%$search%")
        //             ->orWhere('bruto', 'ILIKE', "%$search%")
        //             ->orWhere('sortasi', 'ILIKE', "%$search%");
        //     });
        // }
        $query->orderBy('periode', 'desc');

        $data = $query->paginate($perPage)->appends($request->query());



        return view('pages.periode.index', [
            'items' =>  $data,
            'get_first_periode' => Periode::orderBy('periode', 'desc')->first()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validated = $request->validate([
            'periode' => 'required|integer',
            'periode_mulai' => 'required|date'
        ]);

        $validated['periode_berakhir'] = null;
        $validated['stok'] = 0;

        Periode::create($validated);
        return redirect('/periode');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_pabrik' => 'required|max:50',
        ]);

        $karyawan =  Periode::findOrFail($id);
        $karyawan->update($validated);
        return redirect('/periode');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = Periode::findOrFail($id);

        $karyawan->delete();
        return redirect('/periode')->with('success', 'Karyawan berhasil dihapus!');
    }
}
