<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\M_ops;
use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{

    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Periode::with(['ops' => function ($q) {
            $q->withTrashed();
        }]);

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

        // return Utils::getOpsActive();
        return view('pages.periode.index', [
            'items' =>  $data,
            'data_tarif_ops' => Utils::getOpsActive(),
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
            'periode_mulai' => 'required|date',
            'ops_id' => 'nullable|integer|exists:m_ops,id',
        ]);

        $isExist = Periode::where('periode', $validated['periode'])->exists();

        if ($isExist) {
            return redirect('/periode')->with('error', 'periode sudah tersedia!');
        }


        $validated['periode_berakhir'] = null;
        $validated['stok'] = 0;

        Periode::create($validated);
        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'periode_mulai' => 'required|date',
            'periode_berakhir' => 'nullable|date',
            'ops_id' => 'nullable|integer'
        ]);

        $karyawan =  Periode::findOrFail($id);
        $karyawan->update($validated);
        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = Periode::findOrFail($id);
        $karyawan->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}
