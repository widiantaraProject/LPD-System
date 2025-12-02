<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiSimpanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'simpanan_id',
        'no_transaksi',
        'jenis_transaksi',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'tanggal_transaksi',
        'keterangan',
        'petugas',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    // Relasi ke Simpanan
    public function simpanan()
    {
        return $this->belongsTo(Simpanan::class);
    }

    // Generate nomor transaksi
    public static function generateNoTransaksi()
    {
        $lastTransaksi = self::latest('id')->first();
        $lastNumber = $lastTransaksi ? intval(substr($lastTransaksi->no_transaksi, -6)) : 0;
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        return 'TRX' . date('Ymd') . $newNumber;
    }
}
