<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simpanan_id')->constrained('simpanans')->onDelete('cascade');
            $table->string('no_transaksi')->unique();
            $table->enum('jenis_transaksi', ['Setoran', 'Penarikan']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->date('tanggal_transaksi');
            $table->text('keterangan')->nullable();
            $table->string('petugas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_simpanans');
    }
};
