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
            $table->unsignedBigInteger('sopir_id');
            $table->foreign('sopir_id')->references('id')->on('m_karyawans')->onDelete('cascade');
            $table->unsignedBigInteger('do_type_id');
            $table->foreign('do_type_id')->references('id')->on('m_karyawans')->onDelete('cascade');
            $table->integer('timbangan_first')->nullable();
            $table->integer('timbangan_second')->nullable();
            $table->decimal('bruto', 10, 2)->nullable();
            $table->decimal('sortasi', 10, 2)->nullable();
            $table->decimal('netto', 10, 2)->nullable();
            $table->integer('harga')->nullable();
            $table->integer('uang')->nullable();
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
