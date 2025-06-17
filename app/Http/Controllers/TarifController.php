<?php

namespace App\Http\Controllers;

use App\Models\M_tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifController extends Controller
{

    public function index()
    {

        $rows = DB::select("SELECT mt.id, mt.tarif_perkg, mt.type_karyawan, mt.created_at, is_active_tarif
                            FROM m_tarifs mt
                            left JOIN (
                                SELECT mt2.type_karyawan, MAX(mt2.id) AS max_id, true as is_active_tarif
                                FROM m_tarifs mt2 where mt2.deleted_at is null 
                                GROUP BY type_karyawan
                            ) sub ON mt.type_karyawan = sub.type_karyawan AND mt.id = sub.max_id
                            WHERE mt.deleted_at IS NULL
                            order by mt.created_at desc");

        $data = collect($rows)->map(function ($item) {
            $item->formatted_created_at = Carbon::parse($item->created_at)
                ->timezone('Asia/Jakarta')
                ->translatedFormat('l, d-F-Y H:i') . " WIB";
            return $item;
        });

        return view('pages.master_tarif.index', [
            'items' =>  $data
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // return $request->all();

        // $table->integer('tarif_perkg');
        // $table->enum('type_karyawan', ['SOPIR', 'TKBM']);
        $validated = $request->validate([
            'tarif_perkg' => 'required|integer',
            'type_karyawan' => 'required|max:50',
        ]);

        M_tarif::create($validated);
        return redirect('/master/tarif');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tarif_perkg' => 'required|integer',
            'type_karyawan' => 'required|max:50',
        ]);

        $karyawan =  M_tarif::findOrFail($id);
        $karyawan->update($validated);
        return redirect('/master/tarif');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = M_tarif::findOrFail($id);

        $karyawan->delete();
        return redirect('/master/tarif')->with('success', 'Tarif berhasil dihapus!');
    }
}
