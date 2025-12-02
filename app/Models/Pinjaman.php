<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjamans'; // TAMBAHKAN INI!

    protected $fillable = [
        'nasabah_id',
        'no_pinjaman',
        'jumlah_pinjaman',
        'bunga_persen',
        'total_bunga',
        'total_pinjaman',
        'jangka_waktu',
        'angsuran_pokok',
        'angsuran_bunga',
        'angsuran_perbulan',
        'sisa_pinjaman',
        'tanggal_pinjaman',
        'tanggal_jatuh_tempo',
        'status',
        'keperluan',
        'keterangan',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_pinjaman' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'approved_at' => 'datetime',
        'jumlah_pinjaman' => 'decimal:2',
        'bunga_persen' => 'decimal:2',
        'total_bunga' => 'decimal:2',
        'total_pinjaman' => 'decimal:2',
        'angsuran_pokok' => 'decimal:2',
        'angsuran_bunga' => 'decimal:2',
        'angsuran_perbulan' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
    ];

    // Relasi ke Nasabah
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // Relasi ke Angsuran
    public function angsurans()
    {
        return $this->hasMany(Angsuran::class);
    }

    // Generate nomor pinjaman
    public static function generateNoPinjaman()
    {
        $lastPinjaman = self::latest('id')->first();
        $lastNumber = $lastPinjaman ? intval(substr($lastPinjaman->no_pinjaman, -6)) : 0;
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        return 'PJM' . date('Ymd') . $newNumber;
    }

    // Hitung angsuran
    public static function hitungAngsuran($jumlahPinjaman, $bungaPersen, $jangkaWaktu)
    {
        $totalBunga = ($jumlahPinjaman * $bungaPersen / 100) * ($jangkaWaktu / 12);
        $totalPinjaman = $jumlahPinjaman + $totalBunga;
        $angsuranPokok = $jumlahPinjaman / $jangkaWaktu;
        $angsuranBunga = $totalBunga / $jangkaWaktu;
        $angsuranPerbulan = $totalPinjaman / $jangkaWaktu;

        return [
            'total_bunga' => round($totalBunga, 2),
            'total_pinjaman' => round($totalPinjaman, 2),
            'angsuran_pokok' => round($angsuranPokok, 2),
            'angsuran_bunga' => round($angsuranBunga, 2),
            'angsuran_perbulan' => round($angsuranPerbulan, 2),
        ];
    }

    // Update sisa pinjaman
    public function updateSisaPinjaman()
    {
        $totalBayar = $this->angsurans()
            ->where('status', 'Lunas')
            ->sum('angsuran_pokok');

        $this->sisa_pinjaman = $this->jumlah_pinjaman - $totalBayar;

        if ($this->sisa_pinjaman <= 0) {
            $this->status = 'Lunas';
        }

        $this->save();
    }
}
