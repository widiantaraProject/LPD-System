<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Angsuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pinjaman_id',
        'no_angsuran',
        'angsuran_ke',
        'jumlah_bayar',
        'angsuran_pokok',
        'angsuran_bunga',
        'denda',
        'sisa_pinjaman',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'status',
        'hari_terlambat',
        'keterangan',
        'petugas',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
        'angsuran_pokok' => 'decimal:2',
        'angsuran_bunga' => 'decimal:2',
        'denda' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
    ];

    // Relasi ke Pinjaman
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }

    // Generate nomor angsuran
    public static function generateNoAngsuran()
    {
        $lastAngsuran = self::latest('id')->first();
        $lastNumber = $lastAngsuran ? intval(substr($lastAngsuran->no_angsuran, -6)) : 0;
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        return 'ANG' . date('Ymd') . $newNumber;
    }

    // Hitung denda keterlambatan
    public function hitungDenda()
    {
        if ($this->status != 'Lunas' && Carbon::now()->greaterThan($this->tanggal_jatuh_tempo)) {
            $hariTerlambat = Carbon::now()->diffInDays($this->tanggal_jatuh_tempo);
            $dendaPersen = Pengaturan::where('key', 'denda_keterlambatan')->value('value') ?? 0.5;

            $this->hari_terlambat = $hariTerlambat;
            $this->denda = ($this->angsuran_pokok * $dendaPersen / 100) * $hariTerlambat;
            $this->status = 'Terlambat';
            $this->save();

            return $this->denda;
        }

        return 0;
    }

    // Bayar angsuran
    public function bayar($petugas = null)
    {
        $this->hitungDenda();

        $this->jumlah_bayar = $this->angsuran_pokok + $this->angsuran_bunga + $this->denda;
        $this->tanggal_bayar = now();
        $this->status = 'Lunas';
        $this->petugas = $petugas ?? auth()->user()->name ?? 'System';
        $this->save();

        // Update sisa pinjaman
        $this->pinjaman->updateSisaPinjaman();

        return $this->jumlah_bayar;
    }
}
