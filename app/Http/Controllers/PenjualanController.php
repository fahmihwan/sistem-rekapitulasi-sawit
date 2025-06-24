<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\Pembelian_tbs;
use App\Models\Penjualan;
use App\Models\Periode;
use App\Models\Tkbm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenjualanController extends Controller
{
    public function index(Request $request, string $menu)
    {

        $tanggal = $request->input('tanggal');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $DO_TYPE = Utils::mappingDO_type($menu);
        if ($DO_TYPE == null) {
            return "NOT FOUND";
        }

        // return Tkbm::all();
        $query = Penjualan::with([
            'tarif_sopir' => fn($q) => $q->withTrashed(),
            'tarif_tkbm' => fn($q) => $q->withTrashed(),
            'periode' => fn($q) => $q->withTrashed()->select('id', 'periode', 'periode_mulai', 'periode_berakhir', 'stok'),
            'pabrik:id,nama_pabrik',
            'sopir:id,nama',
            'tkbms:id,karyawan_id,penjualan_id',
            'tkbms.karyawan:id,nama'
        ])->where('do_type_id', $DO_TYPE['id']);


        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $tanggal);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('netto', 'ILIKE', "%$search%")
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

        $karyawans = M_karyawan::all();

        return view('pages.penjualan_TBS.index', [
            'items' =>  $data,
            'title' => $DO_TYPE['text'],
            'menu' => $menu,
            'karyawans' => $karyawans,
            'data_list_tarif' => Utils::getListTarif(),
            'data_tarif' => Utils::getTarifActive(),
            'data_pabrik' => M_pabrik::all(),
            'periodes' => Periode::where('periode_berakhir', null)->get()
        ]);
    }



    public function store(Request $request, $menu)
    {

        try {
            DB::beginTransaction();

            // $tarifActive  = Utils::getTarifActive();
            // if (empty($tarifActive['tarif_sopir_id'])) {
            //     return 'Tarif SOPIR belum di tentukan';
            // }

            // if (empty($tarifActive['tarif_tkbm_id'])) {
            //     return 'Tarif TKBM belum di tentukan';
            // }


            $request->merge([
                'uang' => str_replace('.', '', $request->input('uang')),
                // 'tarif_sopir_id' => $tarifActive['tarif_sopir_id'],
                // 'tarif_tkbm_id' => $tarifActive['tarif_tkbm_id']
            ]);

            $rules = [
                'tanggal_penjualan' => 'required|date',
                'periode_id' => 'required',
                'pabrik_id' => 'required|integer',
                'sopir_id' => 'required|integer',
                'tkbm_id' => 'required|array',
                'timbangan_first' => 'required|numeric',
                'timbangan_second' => 'required|numeric',
                'tarif_sopir_id' => 'required|numeric',
                'tarif_tkbm_id' => 'required|numeric',
                'sortasi' => 'required|numeric',
                'bruto' => 'required|numeric',
                'netto' => 'required|numeric',
                'harga' => 'required|numeric',
                'uang' => 'required|numeric'
            ];


            $validated = $request->validate($rules);

            $DO_TYPE =  Utils::mappingDO_type($menu);
            if ($DO_TYPE == null) {
                return "NOT FOUND";
            };
            $validated['do_type_id'] = $DO_TYPE['id'];

            $penjualan =  Penjualan::create($validated);

            $data = [];

            foreach ($validated['tkbm_id'] as $d) {
                $data[] = [
                    'id' => (string) Str::uuid(),
                    'karyawan_id' => $d,
                    'penjualan_id' => $penjualan->id
                ];
            }

            Tkbm::insert($data);
            DB::commit();
            return redirect('/penjualan/tbs/' . $menu . '/view')->with('success', 'Transaksi berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }


    public function update(Request $request, $menu, $id)
    {
        // return $request->all();


        try {
            DB::beginTransaction();
            $request->merge([
                'uang' => str_replace('.', '', $request->input('uang'))
            ]);

            $rules = [
                'pabrik_id' => 'required|integer',
                'sopir_id' => 'required|integer',
                'tkbm_id' => 'required|array',
                'timbangan_first' => 'required|numeric',
                'timbangan_second' => 'required|numeric',
                'tarif_sopir_id' => 'required|numeric',
                'tarif_tkbm_id' => 'required|numeric',
                'sortasi' => 'required|numeric',
                'bruto' => 'required|numeric',
                'netto' => 'required|numeric',
                'harga' => 'required|numeric',
                'uang' => 'required|numeric'
            ];


            $validated = $request->validate($rules);
            $DO_TYPE =  Utils::mappingDO_type($menu);
            if ($DO_TYPE == null) {
                return "NOT FOUND";
            };
            $validated['do_type_id'] = $DO_TYPE['id'];


            $penjualan = Penjualan::findOrFail($id);
            $penjualan->update($validated);

            Tkbm::where('penjualan_id', $id)->delete();
            $data = [];
            foreach ($validated['tkbm_id'] as $d) {
                $data[] = [
                    'id' => (string) Str::uuid(),
                    'karyawan_id' => $d,
                    'penjualan_id' => $penjualan->id
                ];
            }
            Tkbm::insert($data);
            DB::commit();
            return redirect('/penjualan/tbs/' . $menu . '/view')->with('success', 'Transaksi berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }


    public function destroy($menu, $id)
    {

        $DO_TYPE =  Utils::mappingDO_type($menu);

        if ($DO_TYPE == null) {
            return "NOT FOUND";
        }
        $karyawan = Penjualan::findOrFail($id);

        $karyawan->delete();
        return redirect('/penjualan/tbs/' . $menu . '/view')->with('success', 'Karyawan berhasil dihapus!');
    }
}
