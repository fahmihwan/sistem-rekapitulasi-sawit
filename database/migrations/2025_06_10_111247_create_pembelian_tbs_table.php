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
        Schema::create('pembelian_tbs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_customer', 50)->nullable();
            $table->unsignedBigInteger('tbs_type_id');
            $table->foreign('tbs_type_id')->references('id')->on('m_type_tbs')->onDelete('cascade');
            // $table->integer('netto')->nullable();
            $table->decimal('netto', 10, 2)->nullable();
            $table->integer('harga')->nullable();
            $table->integer('uang')->nullable();
            $table->integer('timbangan_first')->nullable();
            $table->integer('timbangan_second')->nullable();
            $table->decimal('bruto', 10, 2)->nullable();
            // $table->integer('bruto')->nullable();
            // $table->integer('sortasi')->nullable();
            $table->decimal('sortasi', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_tbs');
    }
};
