<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Simpanan;

class ProsesBungaBulanan extends Command
{
    // Tambahkan parameter --force
    protected $signature = 'simpanan:proses-bunga {--force : Paksa proses meskipun sudah dapat bunga bulan ini}';

    protected $description = 'Proses penambahan bunga bulanan untuk semua simpanan aktif';

    public function handle()
    {
        $this->info('Memulai proses penambahan bunga bulanan...');

        if ($this->option('force')) {
            // Mode force: proses semua simpanan aktif tanpa cek tanggal
            $this->warn('MODE FORCE: Memproses semua simpanan tanpa cek tanggal!');

            $simpanans = Simpanan::where('status', 'Aktif')->get();
            $totalBunga = 0;
            $jumlahSimpanan = 0;

            foreach ($simpanans as $simpanan) {
                $bunga = $simpanan->tambahBunga();
                if ($bunga) {
                    $totalBunga += $bunga;
                    $jumlahSimpanan++;
                }
            }

            $hasil = [
                'jumlah_simpanan' => $jumlahSimpanan,
                'total_bunga' => $totalBunga,
            ];
        } else {
            // Mode normal: cek tanggal bunga terakhir
            $hasil = Simpanan::prosesBungaBulanan();
        }

        $this->info("Proses selesai!");
        $this->info("Jumlah simpanan diproses: {$hasil['jumlah_simpanan']}");
        $this->info("Total bunga ditambahkan: Rp " . number_format($hasil['total_bunga'], 0, ',', '.'));

        return Command::SUCCESS;
    }
}
