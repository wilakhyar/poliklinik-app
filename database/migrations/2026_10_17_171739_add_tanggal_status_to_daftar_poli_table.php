<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('id_jadwal');
            $table->enum('status', ['menunggu', 'dilayani', 'selesai'])
                ->default('menunggu')->after('no_antrian');
        });

        // Isi data lama supaya tidak kosong
        DB::table('daftar_poli')->update(['tanggal' => DB::raw('DATE(created_at)')]);

        // Pendaftaran yang sudah punya hasil periksa = selesai
        DB::table('daftar_poli')
            ->whereIn('id', DB::table('periksa')->select('id_daftar_poli'))
            ->update(['status' => 'selesai']);
    }

    public function down(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'status']);
        });
    }
};
