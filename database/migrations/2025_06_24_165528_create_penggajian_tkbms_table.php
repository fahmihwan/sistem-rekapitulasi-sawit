<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penggajian_tkbms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penggajian_id')->nullable();
            $table->foreign('penggajian_id')->references('id')->on('penggajians')->onDelete('cascade');

            $table->uuid('tkbm_id');
            $table->foreign('tkbm_id')->references('id')->on('tkbms')->onDelete('cascade');

            $table->unsignedBigInteger('karyawan_id');
            $table->foreign('karyawan_id')->references('id')->on('m_karyawans')->onDelete('cascade');

            $table->uuid('penjualan_id')->nullable();
            $table->foreign('penjualan_id')->references('id')->on('penjualans')->onDelete('cascade');


            $table->unsignedBigInteger('type_karyawan_id');
            $table->foreign('type_karyawan_id')->references('id')->on('m_type_karyawans')->onDelete('cascade');

            // $table->date('tanggal_penjualan')->nullable();

            // $table->integer('netto')->nullable();
            $table->integer('tarif_perkg')->nullable();
            $table->string('tkbm_agg', 300)->nullable();
            $table->integer('total')->nullable();
            $table->integer('jumlah_uang')->nullable();
            $table->boolean('is_gaji_dibayarkan')->default(false);
            $table->boolean('is_gaji_perhari_dibayarkan')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajian_tkbms');
    }
};
