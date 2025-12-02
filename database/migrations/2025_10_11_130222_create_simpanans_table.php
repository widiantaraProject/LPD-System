<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabahs')->onDelete('cascade');
            $table->string('no_rekening')->unique();
            $table->enum('jenis_simpanan', ['Simpanan Pokok', 'Simpanan Wajib', 'Simpanan Sukarela']);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->decimal('bunga_persen', 5, 2)->default(0);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->date('tanggal_buka');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_bunga_terakhir')->nullable()->after('tanggal_buka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('simpanans', function (Blueprint $table) {
            $table->dropColumn('tanggal_bunga_terakhir');
        });
    }
};
