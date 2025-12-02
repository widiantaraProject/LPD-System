<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengaturan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class dataAwal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed User Admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => bcrypt('111111'),
                'role' => 'petugas',
            ]
        );

        // Seed Pengaturan LPD
        $pengaturans = [
            // Bunga Simpanan (Per Bulan)
            [
                'key' => 'bunga_simpanan_pokok',
                'value' => '0',
                'label' => 'Bunga Simpanan Pokok',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Bunga per bulan untuk simpanan pokok (dalam persen)',
            ],
            [
                'key' => 'bunga_simpanan_wajib',
                'value' => '0.17',
                'label' => 'Bunga Simpanan Wajib',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Bunga per bulan untuk simpanan wajib (dalam persen). Contoh: 0.17% per bulan = ~2% per tahun',
            ],
            [
                'key' => 'bunga_simpanan_sukarela',
                'value' => '0.25',
                'label' => 'Bunga Simpanan Sukarela',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Bunga per bulan untuk simpanan sukarela (dalam persen). Contoh: 0.25% per bulan = ~3% per tahun',
            ],

            // Minimal Simpanan
            [
                'key' => 'minimal_simpanan_pokok',
                'value' => '100000',
                'label' => 'Minimal Simpanan Pokok',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Setoran minimal untuk membuka simpanan pokok',
            ],
            [
                'key' => 'minimal_simpanan_wajib',
                'value' => '50000',
                'label' => 'Minimal Simpanan Wajib',
                'kategori' => 'Simpanan',
                'deskripsi' => 'Setoran minimal untuk membuka simpanan wajib',
            ],

            // Bunga Pinjaman (Per Bulan)
            [
                'key' => 'bunga_pinjaman',
                'value' => '1',
                'label' => 'Bunga Pinjaman',
                'kategori' => 'Pinjaman',
                'deskripsi' => 'Bunga flat per bulan yang diterapkan pada pinjaman (dalam persen). Contoh: 1% per bulan = 12% per tahun',
            ],
            [
                'key' => 'denda_keterlambatan',
                'value' => '0.5',
                'label' => 'Denda Keterlambatan',
                'kategori' => 'Pinjaman',
                'deskripsi' => 'Denda per hari dari jumlah angsuran pokok (dalam persen)',
            ],
        ];

        foreach ($pengaturans as $pengaturan) {
            Pengaturan::updateOrCreate(
                ['key' => $pengaturan['key']],
                $pengaturan
            );
        }

        echo "✓ User admin berhasil dibuat\n";
        echo "✓ 7 pengaturan default berhasil dibuat\n";
    }
}
