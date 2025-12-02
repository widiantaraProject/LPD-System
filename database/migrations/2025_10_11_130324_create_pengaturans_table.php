<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label');
            $table->string('kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('pengaturans')->insert([
            [
                'key' => 'bunga_simpanan_pokok',
                'value' => '0',
                'label' => 'Bunga Simpanan Pokok (%)',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Persentase bunga untuk simpanan pokok',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bunga_simpanan_wajib',
                'value' => '2',
                'label' => 'Bunga Simpanan Wajib (%)',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Persentase bunga untuk simpanan wajib per tahun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bunga_simpanan_sukarela',
                'value' => '3',
                'label' => 'Bunga Simpanan Sukarela (%)',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Persentase bunga untuk simpanan sukarela per tahun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bunga_pinjaman',
                'value' => '12',
                'label' => 'Bunga Pinjaman (%)',
                'kategori' => 'Pinjaman',
                'deskripsi' => 'Persentase bunga pinjaman per tahun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'denda_keterlambatan',
                'value' => '0.5',
                'label' => 'Denda Keterlambatan (%)',
                'kategori' => 'Pinjaman',
                'deskripsi' => 'Persentase denda per hari keterlambatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimal_simpanan_pokok',
                'value' => '100000',
                'label' => 'Minimal Simpanan Pokok (Rp)',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Minimal setoran simpanan pokok',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimal_simpanan_wajib',
                'value' => '50000',
                'label' => 'Minimal Simpanan Wajib (Rp)',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Minimal setoran simpanan wajib per bulan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
