<?php

namespace Database\Seeders;

use App\Models\M_delivery_order_type;
use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\M_tarif;
use App\Models\M_type_tbs;
use App\Models\Pembelian_tbs;
use App\Models\Penjualan;
use App\Models\Periode;
use App\Models\Tkbm;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'nama' => 'Alifian cahyo',
            'username' => 'alif46',
            'password' => bcrypt('qweqwe123')
        ]);

        M_karyawan::create([
            'nama' => 'Ragil',
            'type_karyawan' => 'SOPIR'
        ]);
        M_karyawan::create([
            'nama' => 'Dimas',
            'type_karyawan' => 'SOPIR'
        ]);
        M_karyawan::create([
            'nama' => 'FAHMI',
            'type_karyawan' => 'TKBM'
        ]);


        M_karyawan::factory(5)->create();

        M_pabrik::factory(3)->create();


        M_type_tbs::create(['type_tbs' => 'TBS RUMAH']);
        M_type_tbs::create(['type_tbs' => 'TBS LAHAN']);
        M_type_tbs::create(['type_tbs' => 'TBS RAM']);


        M_tarif::create(['tarif_perkg' => 20, 'type_karyawan' => 'SOPIR']);
        M_tarif::create(['tarif_perkg' => 30, 'type_karyawan' => 'TKBM']);



        M_delivery_order_type::create(['delivery_order_type' => 'PLASMA']);
        M_delivery_order_type::create(['delivery_order_type' => 'LU (Lahan Usaha)']);


        // $periode = null;
        // for ($i = 0; $i < 20; $i++) {
        //     $periode = Periode::create([
        //         'id' => Str::uuid(),
        //         'periode' => $i + 1,
        //         'periode_mulai' => '2025-06-01',
        //         'periode_berakhir' => '2025-06-04',
        //         'stok' => 0,
        //     ])->id;
        // }



        // Pembelian_tbs::create([
        //     'id' => Str::uuid(),
        //     'nama_customer' => 'Pak Budi',
        //     'periode_id' => $periode,
        //     'tanggal_pembelian' => now()->toDateString(),
        //     'tbs_type_id' => 3, // pastikan ID ini ada di tabel m_type_tbs
        //     'netto' => 1000,
        //     'harga' => 3000,
        //     'uang' => 2999220,
        //     'timbangan_first' => 2036,
        //     'timbangan_second' => 1000,
        //     'bruto' => 1036,
        //     'sortasi' => 3.50,
        // ]);


        // Pembelian_tbs::create([
        //     'id' => Str::uuid(),
        //     'periode_id' => $periode,
        //     'tanggal_pembelian' => now()->toDateString(),
        //     'nama_customer' => 'Pak angga',
        //     'tbs_type_id' => 3, // pastikan ID ini ada di tabel m_type_tbs
        //     'netto' => 2000,
        //     'harga' => 3050,
        //     'uang' =>  6098414,
        //     'timbangan_first' => 3072,
        //     'timbangan_second' => 1000,
        //     'bruto' => 2072,
        //     'sortasi' => 3.50,
        // ]);



        // $penjualanUUid = Str::uuid();
        // Penjualan::create([
        //     'id' => $penjualanUUid,
        //     'periode_id' => $periode,
        //     'pabrik_id' => 1,          // pastikan data pabrik dengan ID ini ada
        //     'sopir_id' => 2,           // ID sopir dari m_karyawans
        //     'do_type_id' => 1,         // dari m_delivery_order_types
        //     'tarif_tkbm_id' => 2,      // dari m_tarifs
        //     'tarif_sopir_id' => 1,     // dari m_tarifs

        //     'tanggal_penjualan' => now()->addDay()->toDateString(),
        //     'timbangan_first' => 3590,
        //     'timbangan_second' => 1000,
        //     'bruto' => 2590,
        //     'sortasi' => 3.50,
        //     'netto' => 2500,
        //     'harga' => 3000,
        //     'uang' => 7498050,
        // ]);


        // for ($i = 0; $i < 3; $i++) {
        //     Tkbm::create([
        //         'id' => Str::uuid(),
        //         'karyawan_id' => rand(3, 5),       // 
        //         'penjualan_id' => $penjualanUUid, // pastikan UUID ini valid dari tabel penjualans
        //     ]);
        // }



        // Pembelian_tbs::factory(100)->create();

        // $penjualans = Penjualan::factory(50)->create();

        // $total = 5; // jumlah data yang akan dibuat
        // $penjualans = []; // Array untuk menyimpan entri Penjualan
        // // Membuat entri Penjualan
        // for ($i = 0; $i < $total; $i++) {
        //     $penjualan = Penjualan::factory()
        //         ->createdWithinLastWeek($i, $total)
        //         ->create();
        //     $penjualans[] = $penjualan;
        // }

        // $allKaryawanIds = M_karyawan::where('type_karyawan', 'TKBM')->pluck('id')->toArray();
        // foreach ($penjualans as $penjualan) {
        //     $jumlahTkbm = rand(2, 5);
        //     // Ambil karyawan_id random unik sebanyak $jumlahTkbm
        //     // Jika jumlah karyawan kurang dari jumlah Tkbm, batasi jumlahnya
        //     $karyawanIdsForPenjualan = collect($allKaryawanIds)
        //         ->shuffle()
        //         ->take(min($jumlahTkbm, count($allKaryawanIds)))
        //         ->toArray();
        //     foreach ($karyawanIdsForPenjualan as $karyawanId) {
        //         Tkbm::factory()->create([
        //             'penjualan_id' => $penjualan->id,
        //             'karyawan_id' => $karyawanId,
        //         ]);
        //     }
        // }


        // $jumlahTkbm = rand(2, 5);
        // $existingNamaTkbm = [];
        // // Buat Tkbm satu per satu untuk memastikan 'nama' unik dalam 1 penjualan
        // for ($j = 0; $j < $jumlahTkbm; $j++) {
        //     do {
        //         $tkbm = Tkbm::factory()->make(); // generate instance tapi belum simpan
        //         $nama = $tkbm->nama;
        //     } while (in_array($nama, $existingNamaTkbm));
        //     $existingNamaTkbm[] = $nama;
        //     Tkbm::factory()->create([
        //         'penjualan_id' => $penjualan->id,
        //         'nama' => $nama,
        //     ]);
        // }
    }
}
