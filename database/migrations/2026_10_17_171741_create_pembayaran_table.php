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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_periksa')->unique()->constrained('periksa')->cascadeOnDelete();
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'lunas'])
                ->default('belum_bayar');
            $table->dateTime('tgl_upload')->nullable();
            $table->dateTime('tgl_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
