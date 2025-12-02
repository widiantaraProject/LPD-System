<?php

namespace App\Livewire;

use App\Models\Pengaturan as PengaturanModel;
use Livewire\Component;

class Pengaturan extends Component
{
    public $pengaturans = [];
    public $kategoriAktif = 'Simpanan';

    public function mount()
    {
        $this->loadPengaturan();
    }

    public function render()
    {
        $pengaturansByKategori = collect($this->pengaturans)->groupBy('kategori');

        return view('livewire.pengaturan', [
            'pengaturansByKategori' => $pengaturansByKategori
        ]);
    }

    public function loadPengaturan()
    {
        $this->pengaturans = PengaturanModel::orderBy('kategori')
            ->orderBy('label')
            ->get()
            ->toArray();
    }

    public function simpan()
    {
        try {
            foreach ($this->pengaturans as $pengaturan) {
                PengaturanModel::where('id', $pengaturan['id'])
                    ->update(['value' => $pengaturan['value']]);
            }

            $this->dispatch('saved', ['message' => 'Pengaturan berhasil disimpan!']);
        } catch (\Exception $e) {
            $this->dispatch('error', ['message' => 'Gagal menyimpan pengaturan: ' . $e->getMessage()]);
        }
    }

    public function resetToDefault()
    {
        // Default nilai bunga per bulan
        $defaults = [
            'bunga_simpanan_pokok' => '0',
            'bunga_simpanan_wajib' => '0.17',  // ~2% per tahun = 0.17% per bulan
            'bunga_simpanan_sukarela' => '0.25', // ~3% per tahun = 0.25% per bulan
            'bunga_pinjaman' => '1', // ~12% per tahun = 1% per bulan
            'denda_keterlambatan' => '0.5',
            'minimal_simpanan_pokok' => '100000',
            'minimal_simpanan_wajib' => '50000',
        ];

        foreach ($defaults as $key => $value) {
            PengaturanModel::where('key', $key)->update(['value' => $value]);
        }

        $this->loadPengaturan();
        $this->dispatch('saved', ['message' => 'Pengaturan berhasil direset ke default!']);
    }
}
