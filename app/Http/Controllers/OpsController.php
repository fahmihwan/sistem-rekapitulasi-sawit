<?php

namespace App\Http\Controllers;

use App\Models\M_ops;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpsController extends Controller
{
    public function index()
    {
        $rows = DB::select("SELECT ops2.id, ops2.ops, ops2.deleted_at, x.is_active_ops, ops2.created_at from m_ops ops2 
                            left join (
                                select max(id) as max_id, true as is_active_ops from m_ops ops where ops.deleted_at is null
                            ) as x on ops2.id = x.max_id
                            where ops2.deleted_at is null
                            order by ops2.id desc");

        $data = collect($rows)->map(function ($item) {
            $item->formatted_created_at = Carbon::parse($item->created_at)
                ->timezone('Asia/Jakarta')
                ->translatedFormat('l, d-F-Y H:i') . " WIB";
            return $item;
        });

        return view('pages.master_ops.index', [
            'items' =>  $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ops' => 'required|integer',
        ]);

        M_Ops::create($validated);
        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'ops' => 'required|integer',
        ]);

        $karyawan =  M_ops::findOrFail($id);
        $karyawan->update($validated);
        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = M_ops::findOrFail($id);

        $karyawan->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}
