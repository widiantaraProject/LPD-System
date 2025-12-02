<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('cascade');
            $table->string('no_angsuran')->unique();
            $table->integer('angsuran_ke');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->decimal('angsuran_pokok', 15, 2);
            $table->decimal('angsuran_bunga', 15, 2);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['Belum Bayar', 'Lunas', 'Terlambat'])->default('Belum Bayar');
            $table->integer('hari_terlambat')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('petugas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsurans');
    }
};
