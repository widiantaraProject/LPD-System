<?php

namespace App\Livewire;

use App\Models\Nasabah;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\TransaksiSimpanan;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalNasabah;
    public $nasabahAktif;
    public $totalSimpanan;
    public $totalPinjaman;
    public $pinjamanAktif;
    public $pinjamanLunas;
    public $angsuranBelumBayar;
    public $angsuranTerlambat;

    // Data untuk chart
    public $transaksiHarian = [];
    public $pinjamanPerBulan = [];

    // Data terbaru
    public $nasabahTerbaru = [];
    public $pinjamanTerbaru = [];
    public $angsuranJatuhTempo = [];

    public function mount()
    {
        $this->loadStatistik();
        $this->loadTransaksiHarian();
        $this->loadPinjamanPerBulan();
        $this->loadDataTerbaru();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    private function loadStatistik()
    {
        // Statistik Nasabah
        $this->totalNasabah = Nasabah::count();
        $this->nasabahAktif = Nasabah::where('status', 'Aktif')->count();

        // Statistik Simpanan
        $this->totalSimpanan = Simpanan::where('status', 'Aktif')->sum('saldo');

        // Statistik Pinjaman
        $this->totalPinjaman = Pinjaman::whereIn('status', ['Disetujui', 'Menunggak'])->sum('sisa_pinjaman');
        $this->pinjamanAktif = Pinjaman::whereIn('status', ['Disetujui', 'Menunggak'])->count();
        $this->pinjamanLunas = Pinjaman::where('status', 'Lunas')->count();

        // Statistik Angsuran
        $this->angsuranBelumBayar = Angsuran::where('status', 'Belum Bayar')->count();
        $this->angsuranTerlambat = Angsuran::where('status', 'Terlambat')->count();
    }

    private function loadTransaksiHarian()
    {
        $startDate = Carbon::now()->subDays(6);
        $transaksi = TransaksiSimpanan::select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                DB::raw('SUM(CASE WHEN jenis_transaksi = "Setoran" THEN jumlah ELSE 0 END) as setoran'),
                DB::raw('SUM(CASE WHEN jenis_transaksi = "Penarikan" THEN jumlah ELSE 0 END) as penarikan')
            )
            ->where('tanggal_transaksi', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $this->transaksiHarian = [
            'labels' => $transaksi->pluck('tanggal')->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray(),
            'setoran' => $transaksi->pluck('setoran')->toArray(),
            'penarikan' => $transaksi->pluck('penarikan')->toArray(),
        ];
    }

    private function loadPinjamanPerBulan()
    {
        $startMonth = Carbon::now()->subMonths(5)->startOfMonth();

        // SQLite compatible version
        $pinjaman = Pinjaman::select(
                DB::raw("strftime('%Y-%m', tanggal_pinjaman) as bulan"),
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(jumlah_pinjaman) as total')
            )
            ->where('tanggal_pinjaman', '>=', $startMonth)
            ->whereIn('status', ['Disetujui', 'Lunas', 'Menunggak'])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $this->pinjamanPerBulan = [
            'labels' => $pinjaman->pluck('bulan')->map(fn($bulan) => Carbon::parse($bulan . '-01')->format('M Y'))->toArray(),
            'jumlah' => $pinjaman->pluck('jumlah')->toArray(),
            'total' => $pinjaman->pluck('total')->toArray(),
        ];
    }

    private function loadDataTerbaru()
    {
        // Nasabah terbaru
        $this->nasabahTerbaru = Nasabah::latest()
            ->take(5)
            ->get();

        // Pinjaman terbaru
        $this->pinjamanTerbaru = Pinjaman::with('nasabah')
            ->latest()
            ->take(5)
            ->get();

        // Angsuran jatuh tempo (7 hari ke depan)
        $this->angsuranJatuhTempo = Angsuran::with('pinjaman.nasabah')
            ->where('status', 'Belum Bayar')
            ->whereBetween('tanggal_jatuh_tempo', [
                Carbon::now(),
                Carbon::now()->addDays(7)
            ])
            ->orderBy('tanggal_jatuh_tempo')
            ->take(10)
            ->get();
    }

    public function refresh()
    {
        $this->loadStatistik();
        $this->loadTransaksiHarian();
        $this->loadPinjamanPerBulan();
        $this->loadDataTerbaru();

        $this->dispatch('refreshed', ['message' => 'Data berhasil direfresh!']);
    }
}
