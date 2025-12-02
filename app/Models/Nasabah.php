<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'no_telepon',
        'email',
        'jenis_kelamin',
        'tanggal_lahir',
        'pekerjaan',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke Simpanan
    public function simpanans()
    {
        return $this->hasMany(Simpanan::class);
    }

    // Relasi ke Pinjaman
    public function pinjamans()
    {
        return $this->hasMany(Pinjaman::class);
    }

    // Mendapatkan total saldo simpanan
    public function getTotalSimpananAttribute()
    {
        return $this->simpanans()->where('status', 'Aktif')->sum('saldo');
    }

    // Mendapatkan total pinjaman aktif
    public function getTotalPinjamanAktifAttribute()
    {
        return $this->pinjamans()
            ->whereIn('status', ['Disetujui', 'Menunggak'])
            ->sum('sisa_pinjaman');
    }
}
