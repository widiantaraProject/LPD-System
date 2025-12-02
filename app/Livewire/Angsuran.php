<?php

namespace App\Livewire;

use App\Models\Angsuran as AngsuranModel;
use App\Models\Pinjaman;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Angsuran extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $angsuran_id;
    public $pinjaman_id;
    public $no_angsuran;
    public $angsuran_ke;
    public $jumlah_bayar;
    public $angsuran_pokok;
    public $angsuran_bunga;
    public $denda;
    public $sisa_pinjaman;
    public $tanggal_jatuh_tempo;
    public $tanggal_bayar;
    public $status;
    public $hari_terlambat;
    public $keterangan;

    public $search = '';
    public $filterStatus = '';

    // Data untuk pembayaran
    public $selectedAngsuran;
    public $totalBayar;
    public $isPayment = false;

    public function render()
    {
        // Query untuk pinjaman dengan eager loading angsurans
        $query = Pinjaman::with(['nasabah', 'angsurans' => function($query) {
            $query->orderBy('angsuran_ke', 'asc');
        }]);

        // Filter berdasarkan pencarian
        if ($this->search) {
            $query->whereHas('nasabah', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })->orWhere('no_pinjaman', 'like', '%' . $this->search . '%');
        }

        // Filter berdasarkan status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        } else {
            // Default hanya tampilkan yang aktif dan menunggak
            $query->whereIn('status', ['Disetujui', 'Menunggak']);
        }

        $pinjamans = $query->orderBy('created_at', 'desc')
                          ->paginate(10);

        return view('livewire.angsuran', [
            'pinjamans' => $pinjamans
        ]);
    }

    public function resetInputFields()
    {
        $this->angsuran_id = '';
        $this->pinjaman_id = '';
        $this->no_angsuran = '';
        $this->angsuran_ke = '';
        $this->jumlah_bayar = 0;
        $this->angsuran_pokok = 0;
        $this->angsuran_bunga = 0;
        $this->denda = 0;
        $this->sisa_pinjaman = 0;
        $this->tanggal_jatuh_tempo = '';
        $this->tanggal_bayar = '';
        $this->status = '';
        $this->hari_terlambat = 0;
        $this->keterangan = '';
        $this->selectedAngsuran = null;
        $this->totalBayar = 0;
        $this->isPayment = false;
        $this->resetValidation();
    }

    public function bayar($id)
    {
        // Reset dulu untuk memastikan tidak ada data lama
        $this->resetInputFields();

        $angsuran = AngsuranModel::with('pinjaman.nasabah')->findOrFail($id);

        if ($angsuran->status == 'Lunas') {
            $this->dispatch('error', ['message' => 'Angsuran ini sudah lunas!']);
            return;
        }

        // Hitung denda jika terlambat
        $angsuran->hitungDenda();

        // Reload data setelah hitung denda
        $angsuran->refresh();

        $this->selectedAngsuran = $angsuran;
        $this->angsuran_id = $id;
        $this->no_angsuran = $angsuran->no_angsuran;
        $this->angsuran_ke = $angsuran->angsuran_ke;

        // Konversi ke float untuk memastikan tipe data benar
        $this->angsuran_pokok = floatval($angsuran->angsuran_pokok);
        $this->angsuran_bunga = floatval($angsuran->angsuran_bunga);
        $this->denda = floatval($angsuran->denda ?? 0);
        $this->hari_terlambat = intval($angsuran->hari_terlambat ?? 0);

        // Hitung total bayar
        $this->totalBayar = $this->angsuran_pokok + $this->angsuran_bunga + $this->denda;
        $this->isPayment = true;
    }

    public function prosesBayar()
    {
        $this->validate([
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $angsuran = AngsuranModel::find($this->angsuran_id);

            if (!$angsuran) {
                throw new \Exception('Angsuran tidak ditemukan');
            }

            // Proses pembayaran
            $angsuran->bayar(auth()->user()->name ?? 'System');

            // Update status pinjaman jika semua angsuran lunas
            $pinjaman = $angsuran->pinjaman;
            $totalAngsuran = $pinjaman->angsurans()->count();
            $angsuranLunas = $pinjaman->angsurans()->where('status', 'Lunas')->count();

            if ($totalAngsuran == $angsuranLunas) {
                $pinjaman->update([
                    'status' => 'Lunas',
                    'sisa_pinjaman' => 0
                ]);
            } else {
                // Cek apakah ada yang terlambat
                $adaTerlambat = $pinjaman->angsurans()
                    ->where('status', 'Terlambat')
                    ->exists();

                if ($adaTerlambat) {
                    $pinjaman->update(['status' => 'Menunggak']);
                } else {
                    $pinjaman->update(['status' => 'Disetujui']);
                }
            }

            if ($this->keterangan) {
                $angsuran->update(['keterangan' => $this->keterangan]);
            }

            DB::commit();

            $this->resetInputFields();
            $this->dispatch('close-modal');
            $this->dispatch('saved', ['message' => 'Pembayaran angsuran berhasil!']);
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('error', ['message' => 'Pembayaran gagal: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $angsuran = AngsuranModel::with('pinjaman.nasabah')->findOrFail($id);

        $this->angsuran_id = $id;
        $this->pinjaman_id = $angsuran->pinjaman_id;
        $this->no_angsuran = $angsuran->no_angsuran;
        $this->angsuran_ke = $angsuran->angsuran_ke;

        // Konversi ke float
        $this->jumlah_bayar = floatval($angsuran->jumlah_bayar ?? 0);
        $this->angsuran_pokok = floatval($angsuran->angsuran_pokok);
        $this->angsuran_bunga = floatval($angsuran->angsuran_bunga);
        $this->denda = floatval($angsuran->denda ?? 0);
        $this->sisa_pinjaman = floatval($angsuran->sisa_pinjaman ?? 0);

        $this->tanggal_jatuh_tempo = $angsuran->tanggal_jatuh_tempo->format('Y-m-d');
        $this->tanggal_bayar = $angsuran->tanggal_bayar ? $angsuran->tanggal_bayar->format('Y-m-d') : '';
        $this->status = $angsuran->status;
        $this->hari_terlambat = intval($angsuran->hari_terlambat ?? 0);
        $this->keterangan = $angsuran->keterangan;
        $this->selectedAngsuran = $angsuran;
    }

    public function closeModal()
    {
        $this->resetInputFields();
    }
}
