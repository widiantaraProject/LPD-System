<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabahs')->onDelete('cascade');
            $table->string('no_pinjaman')->unique();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('bunga_persen', 5, 2);
            $table->decimal('total_bunga', 15, 2);
            $table->decimal('total_pinjaman', 15, 2);
            $table->integer('jangka_waktu'); // dalam bulan
            $table->decimal('angsuran_pokok', 15, 2);
            $table->decimal('angsuran_bunga', 15, 2);
            $table->decimal('angsuran_perbulan', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->date('tanggal_pinjaman');
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak', 'Lunas', 'Menunggak'])->default('Diajukan');
            $table->text('keperluan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};
