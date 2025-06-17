<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\M_type_tbs;
use App\Models\Pembelian_tbs;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class TbsController extends Controller
{
    public function index(Request $request, string $menu)
    {


        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $TBS_TYPE =  Utils::mappingTBS_type($menu);
        if ($TBS_TYPE == null) {
            return "NOT FOUND";
        }


        $query = Pembelian_tbs::where('tbs_type_id', $TBS_TYPE['id']);

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
        $query->orderBy('created_at', 'desc');

        $data = $query->paginate($perPage)->appends($request->query());

        // dd($data);
        return view('pages.pembelian_TBS.index', [
            'items' =>  $data,
            'title' => $TBS_TYPE['text'],
            'menu' => $menu

        ]);
    }

    public function store(Request $request, $menu)
    {
        $request->merge([
            'uang' => str_replace('.', '', $request->input('uang'))
        ]);

        $rules = [
            'nama_customer' => 'required|max:50',
            'netto' => 'required|integer',
            'harga' => 'required|integer',
            'uang' => 'required|integer'
        ];

        $TBS_TYPE =  Utils::mappingTBS_type($menu);
        $validated = null;
        if ($TBS_TYPE == null) {
            return "NOT FOUND";
        }

        if ($menu == 'RUMAH') {
            $validated = $request->validate($rules);
            $validated['tbs_type_id'] = $TBS_TYPE['id'];
        } else if ($menu == 'LAHAN') {
            $validated = $request->validate($rules);
            $validated['tbs_type_id'] = $TBS_TYPE['id'];
        } else if ($menu == 'RAM') {
            $rules['timbangan_first'] = 'required|integer';
            $rules['timbangan_second'] = 'required|integer';
            $rules['sortasi'] = 'required|numeric';
            $rules['bruto'] = 'required|integer';
            $validated = $request->validate($rules);
            $validated['tbs_type_id'] = $TBS_TYPE['id'];
        } else {
            return "NOT FOUND";
        }

        Pembelian_tbs::create($validated);
        return redirect('pembelian/tbs/' . $menu . '/view');
    }

    public function update(Request $request, $menu, $id)
    {
        $request->merge([
            'uang' => str_replace('.', '', $request->input('uang'))
        ]);

        // return $request->all();
        $rules = [
            'nama_customer' => 'required|max:50',
            'netto' => 'required|integer',
            'harga' => 'required|integer',
            'uang' => 'required|integer'
        ];

        $TBS_TYPE =  Utils::mappingTBS_type($menu);
        $validated = null;
        if ($TBS_TYPE == null) {
            return "NOT FOUND";
        }

        if ($menu == 'RUMAH') {
            $validated = $request->validate($rules);
        } else if ($menu == 'LAHAN') {
            $validated = $request->validate($rules);
        } else if ($menu == 'RAM') {
            $rules['timbangan_first'] = 'required|integer';
            $rules['timbangan_second'] = 'required|integer';
            $rules['sortasi'] = 'required|numeric';
            $rules['bruto'] = 'required|integer';
            $validated = $request->validate($rules);
        } else {
            return "NOT FOUND";
        }
        $pembelianTBs =  Pembelian_tbs::findOrFail($id);

        $pembelianTBs->update($validated);
        return redirect('pembelian/tbs/' . $menu . '/view');
    }

    public function destroy($menu, $id)
    {
        $TBS_TYPE =  Utils::mappingTBS_type($menu);

        if ($TBS_TYPE == null) {
            return "NOT FOUND";
        }
        $karyawan = Pembelian_tbs::findOrFail($id);

        $karyawan->delete();
        return redirect('/pembelian/tbs/' . $menu . '/view')->with('success', 'Karyawan berhasil dihapus!');
    }
}
