<?php

namespace App\Livewire;

use App\Models\Simpanan as SimpananModel;
use App\Models\Nasabah;
use App\Models\Pengaturan;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Simpanan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $simpanan_id;
    public $nasabah_id;
    public $no_rekening;
    public $jenis_simpanan;
    public $saldo = 0;
    public $bunga_persen;
    public $status = 'Aktif';
    public $tanggal_buka;
    public $keterangan;

    public $isEdit = false;
    public $search = '';
    public $filterJenis = '';
    public $filterStatus = '';

    protected function rules()
    {
        $rules = [
            'nasabah_id' => 'required|exists:nasabahs,id',
            'jenis_simpanan' => 'required|in:Simpanan Pokok,Simpanan Wajib,Simpanan Sukarela',
            'tanggal_buka' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ];

        // Validasi saldo berdasarkan jenis simpanan
        if (!$this->isEdit) {
            $minSaldo = 0;
            switch($this->jenis_simpanan) {
                case 'Simpanan Pokok':
                    $minSaldo = Pengaturan::get('minimal_simpanan_pokok', 100000);
                    break;
                case 'Simpanan Wajib':
                    $minSaldo = Pengaturan::get('minimal_simpanan_wajib', 50000);
                    break;
                case 'Simpanan Sukarela':
                    $minSaldo = 0; // Tidak ada minimal untuk simpanan sukarela
                    break;
            }
            $rules['saldo'] = "required|numeric|min:$minSaldo";
        } else {
            $rules['saldo'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    protected function messages()
    {
        $minSaldo = 0;
        switch($this->jenis_simpanan) {
            case 'Simpanan Pokok':
                $minSaldo = Pengaturan::get('minimal_simpanan_pokok', 100000);
                break;
            case 'Simpanan Wajib':
                $minSaldo = Pengaturan::get('minimal_simpanan_wajib', 50000);
                break;
        }

        return [
            'saldo.min' => 'Saldo minimal untuk ' . $this->jenis_simpanan . ' adalah Rp ' . number_format($minSaldo, 0, ',', '.'),
            'nasabah_id.required' => 'Nasabah harus dipilih',
            'jenis_simpanan.required' => 'Jenis simpanan harus dipilih',
            'tanggal_buka.required' => 'Tanggal buka harus diisi',
        ];
    }

    public function render()
    {
        $query = SimpananModel::with('nasabah');

        if ($this->search) {
            $query->whereHas('nasabah', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nik', 'like', '%' . $this->search . '%');
            })->orWhere('no_rekening', 'like', '%' . $this->search . '%');
        }

        if ($this->filterJenis) {
            $query->where('jenis_simpanan', $this->filterJenis);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $simpanans = $query->orderBy('created_at', 'desc')->paginate(10);
        $nasabahs = Nasabah::where('status', 'Aktif')->orderBy('nama')->get();

        return view('livewire.simpanan', [
            'simpanans' => $simpanans,
            'nasabahs' => $nasabahs
        ]);
    }

    public function resetInputFields()
    {
        $this->simpanan_id = '';
        $this->nasabah_id = '';
        $this->no_rekening = '';
        $this->jenis_simpanan = '';
        $this->saldo = 0;
        $this->bunga_persen = '';
        $this->status = 'Aktif';
        $this->tanggal_buka = date('Y-m-d');
        $this->keterangan = '';
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function updatedJenisSimpanan($value)
    {
        // Set bunga otomatis berdasarkan jenis simpanan (bunga per bulan)
        switch($value) {
            case 'Simpanan Pokok':
                $this->bunga_persen = Pengaturan::get('bunga_simpanan_pokok', 0);
                break;
            case 'Simpanan Wajib':
                $this->bunga_persen = Pengaturan::get('bunga_simpanan_wajib', 0.17);
                break;
            case 'Simpanan Sukarela':
                $this->bunga_persen = Pengaturan::get('bunga_simpanan_sukarela', 0.25);
                break;
        }
    }

    public function store()
    {
        $this->validate();

        // Generate nomor rekening
        $this->no_rekening = SimpananModel::generateNoRekening();

        SimpananModel::create([
            'nasabah_id' => $this->nasabah_id,
            'no_rekening' => $this->no_rekening,
            'jenis_simpanan' => $this->jenis_simpanan,
            'saldo' => $this->saldo,
            'bunga_persen' => $this->bunga_persen,
            'status' => $this->status,
            'tanggal_buka' => $this->tanggal_buka,
            'keterangan' => $this->keterangan,
        ]);

        $this->resetInputFields();
        $this->dispatch('close-modal');
        $this->dispatch('saved', ['message' => 'Data simpanan berhasil ditambahkan!']);
    }

    public function edit($id)
    {
        $simpanan = SimpananModel::findOrFail($id);
        $this->simpanan_id = $id;
        $this->nasabah_id = $simpanan->nasabah_id;
        $this->no_rekening = $simpanan->no_rekening;
        $this->jenis_simpanan = $simpanan->jenis_simpanan;
        $this->saldo = $simpanan->saldo;
        $this->bunga_persen = $simpanan->bunga_persen;
        $this->status = $simpanan->status;
        $this->tanggal_buka = $simpanan->tanggal_buka->format('Y-m-d');
        $this->keterangan = $simpanan->keterangan;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        $simpanan = SimpananModel::find($this->simpanan_id);
        $simpanan->update([
            'nasabah_id' => $this->nasabah_id,
            'jenis_simpanan' => $this->jenis_simpanan,
            'saldo' => $this->saldo,
            'bunga_persen' => $this->bunga_persen,
            'status' => $this->status,
            'tanggal_buka' => $this->tanggal_buka,
            'keterangan' => $this->keterangan,
        ]);

        $this->resetInputFields();
        $this->dispatch('close-modal');
        $this->dispatch('updated', ['message' => 'Data simpanan berhasil diupdate!']);
    }

    #[On('deleteConfirmed')]
    public function delete($simpananid)
    {
        SimpananModel::find($simpananid)->delete();
        $this->dispatch('delete', ['message' => 'Simpanan berhasil dihapus!']);
    }

    public function closeModal()
    {
        $this->resetInputFields();
    }
}
