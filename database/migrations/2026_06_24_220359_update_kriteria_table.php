<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum tipe_input — tambah 'persentase'
        DB::statement("ALTER TABLE kriteria MODIFY COLUMN tipe_input ENUM('numerik', 'linguistik', 'persentase') NULL");

        Schema::table('kriteria', function (Blueprint $table) {
            // Tambah kolom tipe_nilai setelah tipe_input
            $table->enum('tipe_nilai', ['benefit', 'cost'])
                  ->default('benefit')
                  ->nullable()
                  ->after('tipe_input');

            // Tambah kolom sumber_data setelah tipe_nilai
            $table->enum('sumber_data', ['manual', 'otomatis'])
                  ->default('manual')
                  ->after('tipe_nilai');
        });
    }

    public function down(): void
    {
        Schema::table('kriteria', function (Blueprint $table) {
            $table->dropColumn(['tipe_nilai', 'sumber_data']);
        });

        DB::statement("ALTER TABLE kriteria MODIFY COLUMN tipe_input ENUM('numerik', 'linguistik') NULL");
    }
};