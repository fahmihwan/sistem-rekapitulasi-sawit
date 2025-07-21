<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\M_jobs;
use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\M_tarif;
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

        $query = Penjualan::with([
            'model_kerja:id,model_kerja',
            'tarif_sopir' => fn($q) => $q->withTrashed(),
            'tarif_tkbm' => fn($q) => $q->withTrashed(),
            'periode' => fn($q) => $q->withTrashed()->select('id', 'periode', 'periode_mulai', 'periode_berakhir', 'stok'),
            'pabrik' => fn($q) => $q->withTrashed()->select("id", "nama_pabrik"),
            'sopir:id,nama',
            'tkbms:id,karyawan_id,penjualan_id,type_karyawan_id,tarif_tkbm_borongan,tarif_sopir_borongan',
            'tkbms.karyawan:id,nama'
        ])->where('do_type_id', $DO_TYPE['id']);



        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $tanggal);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('netto', 'ILIKE', "%$search%")
                    ->orWhere('harga', 'ILIKE', "%$search%")
                    // ->orWhere('nama', 'ILIKE', "%$search%")
                    ->orWhere('uang', 'ILIKE', "%$search%")
                    ->orWhere('timbangan_first', 'ILIKE', "%$search%")
                    ->orWhere('timbangan_second', 'ILIKE', "%$search%")
                    ->orWhere('bruto', 'ILIKE', "%$search%")
                    ->orWhere('sortasi', 'ILIKE', "%$search%")
                    ->orWhereHas('tkbms.karyawan', function ($q) use ($search) {
                        $q->where('nama', 'ILIKE', "%$search%");
                    });;
            });
        }

        $query->orderBy('created_at', 'desc');

        // return $query->get();

        $data = $query->paginate($perPage)->appends($request->query());

        // return Utils::getKaryawanWithJobs();

        return view('pages.penjualan_TBS.index', [
            'items' =>  $data,
            'title' => $DO_TYPE['text'],
            'menu' => $menu,
            'karyawans' => Utils::getKaryawanWithJobs(),
            'data_list_tarif' => Utils::getListTarif(),
            'data_tarif' => Utils::getTarifActive(),
            'data_pabrik' => M_pabrik::all(),
            'periodes' => Periode::where('periode_berakhir', null)->get()
        ]);
    }



    public function store(Request $request, $menu)
    {


        if ($request->input('model_kerja_id') == 2) { //BORONGAN
            $request->merge([
                'tarif_sopir_id' => null,
                'tarif_tkbm_id' => null,
                'sopir_id' => $request->input('sopir_borongan_id'),
                'tkbm_id' => $request->input('tkbm_borongan_id'),
                'tarif_sopir_borongan' => $request->input('tarif_sopir_borongan'),
                'tarif_tkbm_borongan' => $request->input('tarif_tkbm_borongan'),
                'model_kerja_id' => 2,
                'data_tkbm_dinamis_borongan_json' => json_decode($request->input('data_tkbm_dinamis_borongan_json'))
            ]);
        } else if ($request->input('model_kerja_id') == 1) { //TONASE
            $request->merge([
                'tarif_sopir_id' => $request->input('tarif_sopir_id'),
                'tarif_tkbm_id' => $request->input('tarif_tkbm_id'),
                'sopir_id' => $request->input('sopir_id'),
                'tkbm_id' => $request->input('tkbm_id'),
                'tarif_sopir_borongan' => null,
                'tarif_tkbm_borongan' => null,
                'model_kerja_id' => 1,
                'data_tkbm_dinamis_borongan_json' => []
            ]);
        }

        // return $request->all();

        $request->merge([
            'uang' => str_replace('.', '', $request->input('uang')),
        ]);


        $rules = [
            'tanggal_penjualan' => 'required|date',
            'periode_id' => 'required',
            'pabrik_id' => 'required|integer',
            'sopir_id' => 'required|integer',
            'tkbm_id' => 'nullable|array',
            'timbangan_first' => 'required|numeric',
            'timbangan_second' => 'required|numeric',
            'model_kerja_id' => 'required',
            'tarif_sopir_id' => 'nullable|numeric',
            'tarif_tkbm_id' => 'nullable|numeric',
            'tarif_sopir_borongan' => 'nullable|numeric',
            'data_tkbm_dinamis_borongan_json' => 'nullable|array',
            'sortasi' => 'required|numeric',
            'bruto' => 'required|integer',
            'netto' => 'required|integer',
            'harga' => 'required|integer',
            'uang' => 'required|integer'
        ];

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();
            $DO_TYPE =  Utils::mappingDO_type($menu);
            if ($DO_TYPE == null) {
                return "NOT FOUND";
            };

            $validated['do_type_id'] = $DO_TYPE['id'];

            $penjualan =  Penjualan::create($validated);

            $data = [];
            if ($validated['model_kerja_id'] == 1) { //tkbm

                $get_tkbm_agg =  M_karyawan::withTrashed()->whereIn('id', $validated['tkbm_id'])->select('nama')->pluck('nama')->toArray();
                $tkbm_agg = implode('~', $get_tkbm_agg);

                $tarif_tkbm = M_tarif::where('id', $validated['tarif_tkbm_id'])->first();

                $jumlah_uang_tkbm =  $validated['netto'] * $tarif_tkbm->tarif_perkg / count($validated['tkbm_id']);

                $tarif_sopir = M_tarif::where('id', $validated['tarif_sopir_id'])->first();
                $jumlah_uang_sopir =  $validated['netto'] * $tarif_sopir->tarif_perkg;

                foreach ($validated['tkbm_id'] as $d) {
                    $data[] = [
                        'id' => (string) Str::uuid(),
                        'karyawan_id' => $d,
                        'penjualan_id' => $penjualan->id,
                        'type_karyawan_id' => 2, //TKBM
                        'model_kerja_id' => $validated['model_kerja_id'],
                        'tarif_id' => $validated['tarif_tkbm_id'] ? $validated['tarif_tkbm_id'] : null,
                        'tarif_tkbm_borongan' =>  null,
                        'tarif_sopir_borongan' => null,
                        'is_gaji_dibayarkan' => false,
                        'tkbm_agg' => $tkbm_agg,
                        'jumlah_tkbm' => count($validated['tkbm_id']),
                        'jumlah_uang' => ceil($jumlah_uang_tkbm)
                    ];
                }

                $data[] = [
                    'id' => (string) Str::uuid(),
                    'karyawan_id' => $validated['sopir_id'],
                    'penjualan_id' => $penjualan->id,
                    'type_karyawan_id' => 1, //SOPIR
                    'model_kerja_id' => $validated['model_kerja_id'],
                    'tarif_id' => $validated['tarif_sopir_id'],
                    'tarif_tkbm_borongan' => null,
                    'tarif_sopir_borongan' => null,
                    'is_gaji_dibayarkan' => false,
                    'tkbm_agg' => $tkbm_agg,
                    'jumlah_tkbm' => count($validated['tkbm_id']),
                    'jumlah_uang' => ceil($jumlah_uang_sopir)
                ];
                Tkbm::insert($data);
            } elseif ($validated['model_kerja_id'] == 2) {

                $karyawanIds = collect($request->data_tkbm_dinamis_borongan_json)
                    ->pluck('karyawan_id')
                    ->all();

                $get_tkbm_agg =  M_karyawan::withTrashed()->whereIn('id', $karyawanIds)->select('nama')->pluck('nama')->toArray();
                $tkbm_agg = implode('~', $get_tkbm_agg);




                foreach ($validated['data_tkbm_dinamis_borongan_json'] as $d) {
                    $data[] = [
                        'id' => (string) Str::uuid(),
                        'karyawan_id' => $d->karyawan_id,
                        'penjualan_id' => $penjualan->id,
                        'type_karyawan_id' => 2, //TKBM
                        'model_kerja_id' => $validated['model_kerja_id'],
                        'tarif_id' => null,
                        'tarif_tkbm_borongan' => $d->tarif_borongan,
                        'tarif_sopir_borongan' => null,
                        'is_gaji_dibayarkan' => false,
                        'tkbm_agg' => $tkbm_agg,
                        'jumlah_tkbm' => count($validated['data_tkbm_dinamis_borongan_json']),
                        'jumlah_uang' => ceil($d->tarif_borongan)
                    ];
                }

                $data[] = [
                    'id' => (string) Str::uuid(),
                    'karyawan_id' => $validated['sopir_id'],
                    'penjualan_id' => $penjualan->id,
                    'type_karyawan_id' => 1, //SOPIR
                    'model_kerja_id' => $validated['model_kerja_id'],
                    'tarif_id' => null,
                    'tarif_tkbm_borongan' => null,
                    'tarif_sopir_borongan' => $validated['tarif_sopir_borongan'],
                    'is_gaji_dibayarkan' => false,
                    'tkbm_agg' => $tkbm_agg,
                    'jumlah_tkbm' => count($validated['data_tkbm_dinamis_borongan_json']),
                    'jumlah_uang' => ceil($validated['tarif_sopir_borongan']) 
                ];
                Tkbm::insert($data);
            }




            DB::commit();
            return redirect('/penjualan/tbs/' . $menu . '/view')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }


    public function update(Request $request, $menu, $id)
    {

        // return $request->all();
        if ($request->input('model_kerja_id') == 2) { //BORONGAN
            $request->merge([
                'tarif_sopir_id' => null,
                'tarif_tkbm_id' => null,
                'sopir_id' => $request->input('sopir_borongan_id'),
                'tkbm_id' => $request->input('tkbm_borongan_id'),
                'tarif_sopir_borongan' => $request->input('tarif_sopir_borongan'),
                'tarif_tkbm_borongan' => $request->input('tarif_tkbm_borongan'),
                'model_kerja_id' => 2,
                'data_tkbm_dinamis_borongan_json' => json_decode($request->input('data_tkbm_dinamis_borongan_json'))
            ]);
        } else if ($request->input('model_kerja_id') == 1) { //TONASE
            $request->merge([
                'tarif_sopir_id' => $request->input('tarif_sopir_id'),
                'tarif_tkbm_id' => $request->input('tarif_tkbm_id'),
                'sopir_id' => $request->input('sopir_id'),
                'tkbm_id' => $request->input('tkbm_id'),
                'tarif_sopir_borongan' => null,
                'tarif_tkbm_borongan' => null,
                'model_kerja_id' => 1,
                'data_tkbm_dinamis_borongan_json' => []
            ]);
        }


        try {
            DB::beginTransaction();
            $request->merge([
                'uang' => str_replace('.', '', $request->input('uang'))
            ]);


            
            $rules = [
                // 'model_kerja_id' => 'required',
                // 'pabrik_id' => 'required|integer',
                // 'sopir_id' => 'required|integer',
                // 'tkbm_id' => 'required|array',
                // 'timbangan_first' => 'required|numeric',
                // 'timbangan_second' => 'required|numeric',
                // 'tarif_sopir_id' => 'nullable|numeric',
                // 'tarif_tkbm_id' => 'nullable|numeric',
                // 'tarif_sopir_borongan' => 'nullable|numeric',
                // 'tarif_tkbm_borongan' => 'nullable|numeric',
                // 'sortasi' => 'required|numeric',
                // 'bruto' => 'required|numeric',
                // 'netto' => 'required|numeric',
                // 'harga' => 'required|numeric',
                // 'uang' => 'required|numeric'
                'pabrik_id' => 'required|integer',
                'sopir_id' => 'required|integer',
                'tkbm_id' => 'nullable|array',
                'timbangan_first' => 'required|numeric',
                'timbangan_second' => 'required|numeric',
                'model_kerja_id' => 'required',
                'tarif_sopir_id' => 'nullable|numeric',
                'tarif_tkbm_id' => 'nullable|numeric',
                'tarif_sopir_borongan' => 'nullable|numeric',
                'data_tkbm_dinamis_borongan_json' => 'nullable|array',
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

            

            Tkbm::where('penjualan_id', $id)->forceDelete();

            // $data = [];
            // foreach ($validated['tkbm_id'] as $d) {
            //     $data[] = [
            //         'id' => (string) Str::uuid(),
            //         'karyawan_id' => $d,
            //         'penjualan_id' => $penjualan->id,
            //         'type_karyawan_id' => 2, //TKBM
            //         'model_kerja_id' => $validated['model_kerja_id'],
            //         'tarif_id' => $validated['tarif_tkbm_id'] ? $validated['tarif_tkbm_id'] : null,
            //         'tarif_tkbm_borongan' => $validated['tarif_tkbm_borongan'] ? $validated['tarif_tkbm_borongan'] : null,
            //         'tarif_sopir_borongan' => null,
            //     ];
            // }
            // $data[] = [
            //     'id' => (string) Str::uuid(),
            //     'karyawan_id' => $validated['sopir_id'],
            //     'penjualan_id' => $penjualan->id,
            //     'model_kerja_id' => $validated['model_kerja_id'],
            //     'type_karyawan_id' => 1, //SOPIR
            //     'tarif_id' => $validated['tarif_sopir_id'] ? $validated['tarif_sopir_id'] : null,
            //     'tarif_tkbm_borongan' => null,
            //     'tarif_sopir_borongan' => $validated['tarif_sopir_borongan'] ? $validated['tarif_sopir_borongan'] : null,
            // ];
            // Tkbm::insert($data);

            $data = [];
            if ($validated['model_kerja_id'] == 1) { //tkbm
                $get_tkbm_agg =  M_karyawan::withTrashed()->whereIn('id', $validated['tkbm_id'])->select('nama')->pluck('nama')->toArray();
                $tkbm_agg = implode('~', $get_tkbm_agg);
                $tarif_tkbm = M_tarif::where('id', $validated['tarif_tkbm_id'])->first();
                $jumlah_uang_tkbm =  $validated['netto'] * $tarif_tkbm->tarif_perkg / count($validated['tkbm_id']);


                $tarif_sopir = M_tarif::where('id', $validated['tarif_sopir_id'])->first();
                $jumlah_uang_sopir =  $validated['netto'] * $tarif_sopir->tarif_perkg;
                foreach ($validated['tkbm_id'] as $d) {
                    $data[] = [
                        'id' => (string) Str::uuid(),
                        'karyawan_id' => $d,
                        'penjualan_id' => $penjualan->id,
                        'type_karyawan_id' => 2, //TKBM
                        'model_kerja_id' => $validated['model_kerja_id'],
                        'tarif_id' => $validated['tarif_tkbm_id'] ? $validated['tarif_tkbm_id'] : null,
                        'tarif_tkbm_borongan' =>  null,
                        'tarif_sopir_borongan' => null,
                        'is_gaji_dibayarkan' => false,
                        'tkbm_agg' => $tkbm_agg,
                        'jumlah_tkbm' => count($validated['tkbm_id']),
                        'jumlah_uang' => ceil($jumlah_uang_tkbm) 
                    ];
                }

                $data[] = [
                    'id' => (string) Str::uuid(),
                    'karyawan_id' => $validated['sopir_id'],
                    'penjualan_id' => $penjualan->id,
                    'type_karyawan_id' => 1, //SOPIR
                    'model_kerja_id' => $validated['model_kerja_id'],
                    'tarif_id' => $validated['tarif_sopir_id'],
                    'tarif_tkbm_borongan' => null,
                    'tarif_sopir_borongan' => null,
                    'is_gaji_dibayarkan' => false,
                    'tkbm_agg' => $tkbm_agg,
                    'jumlah_tkbm' => count($validated['tkbm_id']),
                    'jumlah_uang' => ceil($jumlah_uang_sopir)
                ];
                
                Tkbm::insert($data);
            } elseif ($validated['model_kerja_id'] == 2) {

                $karyawanIds = collect($request->data_tkbm_dinamis_borongan_json)
                    ->pluck('karyawan_id')
                    ->all();

                $get_tkbm_agg =  M_karyawan::withTrashed()->whereIn('id', $karyawanIds)->select('nama')->pluck('nama')->toArray();
                $tkbm_agg = implode('~', $get_tkbm_agg);




                foreach ($validated['data_tkbm_dinamis_borongan_json'] as $d) {
                    $data[] = [
                        'id' => (string) Str::uuid(),
                        'karyawan_id' => $d->karyawan_id,
                        'penjualan_id' => $penjualan->id,
                        'type_karyawan_id' => 2, //TKBM
                        'model_kerja_id' => $validated['model_kerja_id'],
                        'tarif_id' => null,
                        'tarif_tkbm_borongan' => $d->tarif_borongan,
                        'tarif_sopir_borongan' => null,
                        'is_gaji_dibayarkan' => false,
                        'tkbm_agg' => $tkbm_agg,
                        'jumlah_tkbm' => count($validated['data_tkbm_dinamis_borongan_json']),
                        'jumlah_uang' => $d->tarif_borongan
                    ];
                }

                $data[] = [
                    'id' => (string) Str::uuid(),
                    'karyawan_id' => $validated['sopir_id'],
                    'penjualan_id' => $penjualan->id,
                    'type_karyawan_id' => 1, //SOPIR
                    'model_kerja_id' => $validated['model_kerja_id'],
                    'tarif_id' => null,
                    'tarif_tkbm_borongan' => null,
                    'tarif_sopir_borongan' => $validated['tarif_sopir_borongan'],
                    'is_gaji_dibayarkan' => false,
                    'tkbm_agg' => $tkbm_agg,
                    'jumlah_tkbm' => count($validated['data_tkbm_dinamis_borongan_json']),
                    'jumlah_uang' => $validated['tarif_sopir_borongan']
                ];
                Tkbm::insert($data);
            }




            DB::commit();
            return redirect('/penjualan/tbs/' . $menu . '/view')->with('success', 'Transaksi berhasil diubah!');
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
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        Tkbm::where('penjualan_id', $penjualan->id)->forceDelete();

        return redirect('/penjualan/tbs/' . $menu . '/view')->with('success', 'Transaksi berhasil dihapus!');
    }
}
