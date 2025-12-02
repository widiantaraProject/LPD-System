<?php

namespace App\Livewire;

use App\Models\TransaksiSimpanan as TransaksiSimpananModel;
use App\Models\Simpanan;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TransaksiSimpanan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $transaksi_id;
    public $simpanan_id;
    public $no_transaksi;
    public $jenis_transaksi;
    public $jumlah = 0;
    public $saldo_sebelum = 0;
    public $saldo_sesudah = 0;
    public $tanggal_transaksi;
    public $keterangan;
    public $petugas;

    public $search = '';
    public $filterJenis = '';
    public $filterTanggal = '';

    // Data untuk tampilan
    public $selectedSimpanan;
    public $saldoTerkini;

    protected $rules = [
        'simpanan_id' => 'required|exists:simpanans,id',
        'jenis_transaksi' => 'required|in:Setoran,Penarikan',
        'jumlah' => 'required|numeric|min:1',
        'tanggal_transaksi' => 'required|date',
    ];

    public function render()
    {
        $query = TransaksiSimpananModel::with('simpanan.nasabah');

        if ($this->search) {
            $query->whereHas('simpanan.nasabah', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })->orWhere('no_transaksi', 'like', '%' . $this->search . '%');
        }

        if ($this->filterJenis) {
            $query->where('jenis_transaksi', $this->filterJenis);
        }

        if ($this->filterTanggal) {
            $query->whereDate('tanggal_transaksi', $this->filterTanggal);
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        $simpanans = Simpanan::with('nasabah')
                            ->where('status', 'Aktif')
                            ->get();

        return view('livewire.transaksi-simpanan', [
            'transaksis' => $transaksis,
            'simpanans' => $simpanans
        ]);
    }

    public function resetInputFields()
    {
        $this->transaksi_id = '';
        $this->simpanan_id = '';
        $this->no_transaksi = '';
        $this->jenis_transaksi = '';
        $this->jumlah = 0;
        $this->saldo_sebelum = 0;
        $this->saldo_sesudah = 0;
        $this->tanggal_transaksi = date('Y-m-d');
        $this->keterangan = '';
        $this->petugas = '';
        $this->selectedSimpanan = null;
        $this->saldoTerkini = 0;
        $this->resetValidation();
    }

    public function updatedSimpananId($value)
    {
        if ($value) {
            $this->selectedSimpanan = Simpanan::with('nasabah')->find($value);
            $this->saldoTerkini = $this->selectedSimpanan->saldo;
        }
    }

    public function store()
    {
        // Validasi input
        $validated = $this->validate();

        // Cek saldo untuk penarikan
        if ($this->jenis_transaksi == 'Penarikan') {
            $simpanan = Simpanan::find($this->simpanan_id);
            if ($simpanan->saldo < $this->jumlah) {
                $this->dispatch('error', ['message' => 'Saldo tidak mencukupi! Saldo tersedia: Rp ' . number_format($simpanan->saldo, 0, ',', '.')]);
                return;
            }
        }

        DB::beginTransaction();
        try {
            $simpanan = Simpanan::find($this->simpanan_id);

            // Validasi simpanan ditemukan
            if (!$simpanan) {
                throw new \Exception('Rekening simpanan tidak ditemukan');
            }

            $this->saldo_sebelum = $simpanan->saldo;

            // Update saldo simpanan
            if ($this->jenis_transaksi == 'Setoran') {
                $simpanan->tambahSaldo($this->jumlah);
                $this->saldo_sesudah = $simpanan->saldo;
            } else {
                if ($simpanan->kurangiSaldo($this->jumlah)) {
                    $this->saldo_sesudah = $simpanan->saldo;
                } else {
                    throw new \Exception('Saldo tidak mencukupi untuk penarikan');
                }
            }

            // Generate nomor transaksi
            $this->no_transaksi = TransaksiSimpananModel::generateNoTransaksi();

            // Simpan transaksi
            TransaksiSimpananModel::create([
                'simpanan_id' => $this->simpanan_id,
                'no_transaksi' => $this->no_transaksi,
                'jenis_transaksi' => $this->jenis_transaksi,
                'jumlah' => $this->jumlah,
                'saldo_sebelum' => $this->saldo_sebelum,
                'saldo_sesudah' => $this->saldo_sesudah,
                'tanggal_transaksi' => $this->tanggal_transaksi,
                'keterangan' => $this->keterangan,
                'petugas' => auth()->user()->name ?? 'System',
            ]);

            DB::commit();

            $this->resetInputFields();
            $this->dispatch('close-modal');
            $this->dispatch('saved', ['message' => 'Transaksi berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error transaksi simpanan: ' . $e->getMessage());
            $this->dispatch('error', ['message' => 'Transaksi gagal: ' . $e->getMessage()]);
        }
    }

    public function view($id)
    {
        $transaksi = TransaksiSimpananModel::with('simpanan.nasabah')->findOrFail($id);
        $this->transaksi_id = $id;
        $this->simpanan_id = $transaksi->simpanan_id;
        $this->no_transaksi = $transaksi->no_transaksi;
        $this->jenis_transaksi = $transaksi->jenis_transaksi;
        $this->jumlah = (float) $transaksi->jumlah;
        $this->saldo_sebelum = (float) $transaksi->saldo_sebelum;
        $this->saldo_sesudah = (float) $transaksi->saldo_sesudah;
        $this->tanggal_transaksi = $transaksi->tanggal_transaksi->format('Y-m-d');
        $this->keterangan = $transaksi->keterangan;
        $this->petugas = $transaksi->petugas;
        $this->selectedSimpanan = $transaksi->simpanan;
    }

    public function closeModal()
    {
        $this->resetInputFields();
    }
}
