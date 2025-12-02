<?php

namespace App\Livewire;

use App\Models\Pinjaman as PinjamanModel;
use App\Models\Angsuran;
use App\Models\Nasabah;
use App\Models\Pengaturan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Pinjaman extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $pinjaman_id;
    public $nasabah_id;
    public $no_pinjaman;
    public $jumlah_pinjaman = 0;
    public $bunga_persen;
    public $total_bunga = 0;
    public $total_pinjaman = 0;
    public $jangka_waktu = 0;
    public $angsuran_pokok = 0;
    public $angsuran_bunga = 0;
    public $angsuran_perbulan = 0;
    public $sisa_pinjaman = 0;
    public $tanggal_pinjaman;
    public $tanggal_jatuh_tempo;
    public $status = 'Diajukan';
    public $keperluan;
    public $keterangan;

    public $isEdit = false;
    public $isDetail = false;
    public $search = '';
    public $filterStatus = '';

    // Simulasi perhitungan
    public $showSimulasi = false;
    public $simulasiData = [];

    protected $rules = [
        'nasabah_id' => 'required|exists:nasabahs,id',
        'jumlah_pinjaman' => 'required|numeric|min:100000',
        'jangka_waktu' => 'required|integer|min:1|max:60',
        'tanggal_pinjaman' => 'required|date',
        'keperluan' => 'required|string',
    ];

    public function mount()
    {
        $this->bunga_persen = Pengaturan::get('bunga_pinjaman', 12);
        $this->tanggal_pinjaman = date('Y-m-d');
    }

    public function render()
    {
        $query = PinjamanModel::with('nasabah');

        if ($this->search) {
            $query->whereHas('nasabah', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nik', 'like', '%' . $this->search . '%');
            })->orWhere('no_pinjaman', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $pinjamans = $query->orderBy('created_at', 'desc')->paginate(10);
        $nasabahs = Nasabah::where('status', 'Aktif')->orderBy('nama')->get();

        return view('livewire.pinjaman', [
            'pinjamans' => $pinjamans,
            'nasabahs' => $nasabahs
        ]);
    }

    public function resetInputFields()
    {
        $this->pinjaman_id = '';
        $this->nasabah_id = '';
        $this->no_pinjaman = '';
        $this->jumlah_pinjaman = 0;
        $this->bunga_persen = Pengaturan::get('bunga_pinjaman', 12);
        $this->total_bunga = 0;
        $this->total_pinjaman = 0;
        $this->jangka_waktu = 0;
        $this->angsuran_pokok = 0;
        $this->angsuran_bunga = 0;
        $this->angsuran_perbulan = 0;
        $this->sisa_pinjaman = 0;
        $this->tanggal_pinjaman = date('Y-m-d');
        $this->tanggal_jatuh_tempo = '';
        $this->status = 'Diajukan';
        $this->keperluan = '';
        $this->keterangan = '';
        $this->isEdit = false;
        $this->isDetail = false;
        $this->showSimulasi = false;
        $this->simulasiData = [];
        $this->resetValidation();
    }

    public function hitungPinjaman()
    {
        // Cast ke numeric untuk memastikan tipe data benar
        $jumlahPinjaman = (float) $this->jumlah_pinjaman;
        $jangkaWaktu = (int) $this->jangka_waktu;

        if ($jumlahPinjaman > 0 && $jangkaWaktu > 0) {
            $hasil = PinjamanModel::hitungAngsuran(
                $jumlahPinjaman,
                $this->bunga_persen,
                $jangkaWaktu
            );

            $this->total_bunga = $hasil['total_bunga'];
            $this->total_pinjaman = $hasil['total_pinjaman'];
            $this->angsuran_pokok = $hasil['angsuran_pokok'];
            $this->angsuran_bunga = $hasil['angsuran_bunga'];
            $this->angsuran_perbulan = $hasil['angsuran_perbulan'];

            // Hitung tanggal jatuh tempo - pastikan jangka_waktu adalah integer
            $this->tanggal_jatuh_tempo = Carbon::parse($this->tanggal_pinjaman)
                ->addMonths($jangkaWaktu)
                ->format('Y-m-d');

            $this->showSimulasi = true;
            $this->generateSimulasi();
        }
    }

    public function generateSimulasi()
    {
        $this->simulasiData = [];
        $tanggalMulai = Carbon::parse($this->tanggal_pinjaman);
        $sisaPinjaman = (float) $this->jumlah_pinjaman;
        $jangkaWaktu = (int) $this->jangka_waktu;

        for ($i = 1; $i <= $jangkaWaktu; $i++) {
            $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths($i);
            $sisaPinjaman -= $this->angsuran_pokok;

            $this->simulasiData[] = [
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('d/m/Y'),
                'angsuran_pokok' => $this->angsuran_pokok,
                'angsuran_bunga' => $this->angsuran_bunga,
                'total_angsuran' => $this->angsuran_perbulan,
                'sisa_pinjaman' => max(0, $sisaPinjaman),
            ];
        }
    }

    public function store()
    {
        $this->validate();

        if (!$this->total_pinjaman) {
            $this->hitungPinjaman();
        }

        DB::beginTransaction();
        try {
            // Generate nomor pinjaman
            $this->no_pinjaman = PinjamanModel::generateNoPinjaman();

            // Simpan pinjaman
            $pinjaman = PinjamanModel::create([
                'nasabah_id' => $this->nasabah_id,
                'no_pinjaman' => $this->no_pinjaman,
                'jumlah_pinjaman' => $this->jumlah_pinjaman,
                'bunga_persen' => $this->bunga_persen,
                'total_bunga' => $this->total_bunga,
                'total_pinjaman' => $this->total_pinjaman,
                'jangka_waktu' => $this->jangka_waktu,
                'angsuran_pokok' => $this->angsuran_pokok,
                'angsuran_bunga' => $this->angsuran_bunga,
                'angsuran_perbulan' => $this->angsuran_perbulan,
                'sisa_pinjaman' => $this->jumlah_pinjaman,
                'tanggal_pinjaman' => $this->tanggal_pinjaman,
                'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo,
                'status' => $this->status,
                'keperluan' => $this->keperluan,
                'keterangan' => $this->keterangan,
            ]);

            // Generate jadwal angsuran
            $this->generateJadwalAngsuran($pinjaman);

            DB::commit();

            $this->resetInputFields();
            $this->dispatch('close-modal');
            $this->dispatch('saved', ['message' => 'Pengajuan pinjaman berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('error', ['message' => 'Gagal menyimpan pinjaman: ' . $e->getMessage()]);
        }
    }

    private function generateJadwalAngsuran($pinjaman)
    {
        $tanggalMulai = Carbon::parse($pinjaman->tanggal_pinjaman);
        $sisaPinjaman = $pinjaman->jumlah_pinjaman;

        for ($i = 1; $i <= $pinjaman->jangka_waktu; $i++) {
            $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths($i);
            $sisaPinjaman -= $pinjaman->angsuran_pokok;

            Angsuran::create([
                'pinjaman_id' => $pinjaman->id,
                'no_angsuran' => Angsuran::generateNoAngsuran(),
                'angsuran_ke' => $i,
                'jumlah_bayar' => 0,
                'angsuran_pokok' => $pinjaman->angsuran_pokok,
                'angsuran_bunga' => $pinjaman->angsuran_bunga,
                'denda' => 0,
                'sisa_pinjaman' => max(0, $sisaPinjaman),
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'status' => 'Belum Bayar',
            ]);
        }
    }

    public function edit($id)
    {
        $pinjaman = PinjamanModel::findOrFail($id);
        $this->pinjaman_id = $id;
        $this->nasabah_id = $pinjaman->nasabah_id;
        $this->no_pinjaman = $pinjaman->no_pinjaman;
        $this->jumlah_pinjaman = $pinjaman->jumlah_pinjaman;
        $this->bunga_persen = $pinjaman->bunga_persen;
        $this->total_bunga = $pinjaman->total_bunga;
        $this->total_pinjaman = $pinjaman->total_pinjaman;
        $this->jangka_waktu = $pinjaman->jangka_waktu;
        $this->angsuran_pokok = $pinjaman->angsuran_pokok;
        $this->angsuran_bunga = $pinjaman->angsuran_bunga;
        $this->angsuran_perbulan = $pinjaman->angsuran_perbulan;
        $this->sisa_pinjaman = $pinjaman->sisa_pinjaman;
        $this->tanggal_pinjaman = $pinjaman->tanggal_pinjaman->format('Y-m-d');
        $this->tanggal_jatuh_tempo = $pinjaman->tanggal_jatuh_tempo->format('Y-m-d');
        $this->status = $pinjaman->status;
        $this->keperluan = $pinjaman->keperluan;
        $this->keterangan = $pinjaman->keterangan;
        $this->isEdit = true;
    }

    public function detail($id)
    {
        $this->edit($id);
        $this->isDetail = true;
    }

    public function update()
    {
        $this->validate([
            'status' => 'required|in:Diajukan,Disetujui,Ditolak,Lunas,Menunggak',
            'keterangan' => 'nullable|string',
        ]);

        $pinjaman = PinjamanModel::find($this->pinjaman_id);

        // Jika disetujui, update approved_by dan approved_at
        if ($this->status == 'Disetujui' && $pinjaman->status != 'Disetujui') {
            $pinjaman->approved_by = auth()->user()->name ?? 'System';
            $pinjaman->approved_at = now();
        }

        $pinjaman->update([
            'status' => $this->status,
            'keterangan' => $this->keterangan,
        ]);

        $this->resetInputFields();
        $this->dispatch('close-modal');
        $this->dispatch('updated', ['message' => 'Status pinjaman berhasil diupdate!']);
    }

    public function delete($pinjamanid)
    {
        try {
            $pinjaman = PinjamanModel::find($pinjamanid);

            if (!$pinjaman) {
                $this->dispatch('error', ['message' => 'Pinjaman tidak ditemukan!']);
                return;
            }

            // Cek apakah ada angsuran yang sudah dibayar
            $angsuranLunas = $pinjaman->angsurans()->where('status', 'Lunas')->count();

            if ($angsuranLunas > 0) {
                $this->dispatch('error', ['message' => 'Tidak dapat menghapus pinjaman yang sudah ada pembayaran!']);
                return;
            }

            // Hapus semua angsuran terkait terlebih dahulu
            $pinjaman->angsurans()->delete();

            // Hapus pinjaman
            $pinjaman->delete();

            $this->dispatch('deleted', ['message' => 'Pinjaman berhasil dihapus!']);
        } catch (\Exception $e) {
            $this->dispatch('error', ['message' => 'Gagal menghapus pinjaman: ' . $e->getMessage()]);
        }
    }

    public function closeModal()
    {
        $this->resetInputFields();
    }
}
