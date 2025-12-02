<?php

namespace App\Livewire;

use App\Models\Nasabah as NasabahModel;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Nasabah extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $nasabah_id;
    public $nama;
    public $nik;
    public $alamat;
    public $no_telepon;
    public $email;
    public $jenis_kelamin;
    public $tanggal_lahir;
    public $pekerjaan;
    public $status = 'Aktif';

    public $isEdit = false;
    public $search = '';

    protected $rules = [
        'nama' => 'required|string|max:255',
        'nik' => 'required|string|unique:nasabahs,nik',
        'alamat' => 'required|string',
        'no_telepon' => 'required|string|max:15',
        'email' => 'required|email|unique:nasabahs,email',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'tanggal_lahir' => 'required|date',
        'pekerjaan' => 'required|string|max:255',
        'status' => 'required|in:Aktif,Tidak Aktif',
    ];

    public function render()
    {
        $nasabahs = NasabahModel::where('nama', 'like', '%' . $this->search . '%')
            ->orWhere('nik', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.nasabah', [
            'nasabahs' => $nasabahs
        ]);
    }

    public function resetInputFields()
    {
        $this->nasabah_id = '';
        $this->nama = '';
        $this->nik = '';
        $this->alamat = '';
        $this->no_telepon = '';
        $this->email = '';
        $this->jenis_kelamin = '';
        $this->tanggal_lahir = '';
        $this->pekerjaan = '';
        $this->status = 'Aktif';
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        NasabahModel::create([
            'nama' => $this->nama,
            'nik' => $this->nik,
            'alamat' => $this->alamat,
            'no_telepon' => $this->no_telepon,
            'email' => $this->email,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir,
            'pekerjaan' => $this->pekerjaan,
            'status' => $this->status,
        ]);

        $this->resetInputFields();
        $this->dispatch('close-modal');
        $this->dispatch('saved', ['message' => 'Data berhasil ditambahkan!']);
    }

    public function edit($id)
    {
        $nasabah = NasabahModel::findOrFail($id);
        $this->nasabah_id = $id;
        $this->nama = $nasabah->nama;
        $this->nik = $nasabah->nik;
        $this->alamat = $nasabah->alamat;
        $this->no_telepon = $nasabah->no_telepon;
        $this->email = $nasabah->email;
        $this->jenis_kelamin = $nasabah->jenis_kelamin;
        $this->tanggal_lahir = $nasabah->tanggal_lahir->format('Y-m-d');
        $this->pekerjaan = $nasabah->pekerjaan;
        $this->status = $nasabah->status;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|unique:nasabahs,nik,' . $this->nasabah_id,
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'required|email|unique:nasabahs,email,' . $this->nasabah_id,
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'pekerjaan' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $nasabah = NasabahModel::find($this->nasabah_id);
        $nasabah->update([
            'nama' => $this->nama,
            'nik' => $this->nik,
            'alamat' => $this->alamat,
            'no_telepon' => $this->no_telepon,
            'email' => $this->email,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir,
            'pekerjaan' => $this->pekerjaan,
            'status' => $this->status,
        ]);

        $this->resetInputFields();
        $this->dispatch('close-modal');
        $this->dispatch('updated', ['message' => 'Data berhasil di Update!']);
    }

    #[On('deleteConfirmed')]
    public function delete($nasabahid)
    {
        NasabahModel::find($nasabahid)->delete();
        $this->dispatch('delete', ['message' => 'Nasabah berhasil dihapus!']);
    }

    public function closeModal()
    {
        $this->resetInputFields();
    }
}
