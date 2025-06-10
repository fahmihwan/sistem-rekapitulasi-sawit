<?php

namespace Database\Seeders;

use App\Models\M_karyawan;
use App\Models\M_pabrik;
use App\Models\M_type_tbs;
use App\Models\Pembelian_tbs;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        M_karyawan::factory(3)->create();
        M_pabrik::factory(3)->create();


        M_type_tbs::create(['type_tbs' => 'TBS RUMAH']);
        M_type_tbs::create(['type_tbs' => 'TBS LAHAN']);
        M_type_tbs::create(['type_tbs' => 'TBS RAM']);

        Pembelian_tbs::factory(100)->create();
    }
}
