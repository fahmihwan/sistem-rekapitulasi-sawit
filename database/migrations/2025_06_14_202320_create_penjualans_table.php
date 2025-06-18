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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('pabrik_id');
            $table->foreign('pabrik_id')->references('id')->on('m_pabriks')->onDelete('cascade');

            $table->unsignedBigInteger('sopir_id');
            $table->foreign('sopir_id')->references('id')->on('m_karyawans')->onDelete('cascade');

            $table->unsignedBigInteger('do_type_id');
            $table->foreign('do_type_id')->references('id')->on('m_delivery_order_types')->onDelete('cascade');

            $table->unsignedBigInteger('tarif_tkbm_id');
            $table->foreign('tarif_tkbm_id')->references('id')->on('m_tarifs')->onDelete('cascade');

            $table->unsignedBigInteger('tarif_sopir_id');
            $table->foreign('tarif_sopir_id')->references('id')->on('m_tarifs')->onDelete('cascade');

            $table->integer('timbangan_first')->default(0);
            $table->integer('timbangan_second')->default(0);
            $table->integer('bruto')->default(0);
            $table->decimal('sortasi', 10, 2)->default(0);
            $table->integer('netto')->default(0);
            $table->integer('harga')->default(0);
            $table->integer('uang')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
