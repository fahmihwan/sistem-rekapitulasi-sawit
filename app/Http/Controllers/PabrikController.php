<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PabrikController extends Controller
{

    public function index()
    {
        return view('pages.master_karyawan.index', [
            'items' =>  M_karyawan::latest()->get()
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama' => 'required|max:50',
            'type_karyawan' => 'required'
        ]);

        M_karyawan::create($validated);
        return redirect('/master/karyawan');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama' => 'required|max:50',
            'type_karyawan' => 'required'
        ]);

        $karyawan =  M_karyawan::findOrFail($id);
        $karyawan->update($validated);
        return redirect('/master/karyawan');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = M_karyawan::findOrFail($id);

        $karyawan->delete();
        return redirect('/master/karyawan')->with('success', 'Karyawan berhasil dihapus!');
    }
}
