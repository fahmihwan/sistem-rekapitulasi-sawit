<?php

namespace Database\Seeders;

use App\Models\M_delivery_order_type;
use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\M_tarif;
use App\Models\M_type_tbs;
use App\Models\Pembelian_tbs;
use App\Models\Penjualan;
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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
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

        Pembelian_tbs::factory(100)->create();

        M_delivery_order_type::create(['delivery_order_type' => 'PLASMA']);
        M_delivery_order_type::create(['delivery_order_type' => 'LU (Lahan Usaha)']);

        // $penjualans = Penjualan::factory(50)->create();

        $total = 5; // jumlah data yang akan dibuat
        $penjualans = []; // Array untuk menyimpan entri Penjualan
        // Membuat entri Penjualan
        for ($i = 0; $i < $total; $i++) {
            $penjualan = Penjualan::factory()
                ->createdWithinLastWeek($i, $total)
                ->create();
            $penjualans[] = $penjualan;
        }

        $allKaryawanIds = M_karyawan::where('type_karyawan', 'TKBM')->pluck('id')->toArray();
        foreach ($penjualans as $penjualan) {
            $jumlahTkbm = rand(2, 5);
            // Ambil karyawan_id random unik sebanyak $jumlahTkbm
            // Jika jumlah karyawan kurang dari jumlah Tkbm, batasi jumlahnya
            $karyawanIdsForPenjualan = collect($allKaryawanIds)
                ->shuffle()
                ->take(min($jumlahTkbm, count($allKaryawanIds)))
                ->toArray();
            foreach ($karyawanIdsForPenjualan as $karyawanId) {
                Tkbm::factory()->create([
                    'penjualan_id' => $penjualan->id,
                    'karyawan_id' => $karyawanId,
                ]);
            }
        }


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
