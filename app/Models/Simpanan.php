<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Simpanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nasabah_id',
        'no_rekening',
        'jenis_simpanan',
        'saldo',
        'bunga_persen',
        'status',
        'tanggal_buka',
        'tanggal_bunga_terakhir',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_bunga_terakhir' => 'date',
        'saldo' => 'decimal:2',
        'bunga_persen' => 'decimal:2',
    ];

    // Relasi ke Nasabah
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // Relasi ke Transaksi Simpanan
    public function transaksis()
    {
        return $this->hasMany(TransaksiSimpanan::class);
    }

    // Generate nomor rekening
    public static function generateNoRekening()
    {
        $lastSimpanan = self::latest('id')->first();
        $lastNumber = $lastSimpanan ? intval(substr($lastSimpanan->no_rekening, -6)) : 0;
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        return 'SMP' . date('Ymd') . $newNumber;
    }

    // Tambah saldo (untuk setoran)
    public function tambahSaldo($jumlah)
    {
        $this->saldo += $jumlah;
        $this->save();
        return true;
    }

    // Kurangi saldo (untuk penarikan)
    public function kurangiSaldo($jumlah)
    {
        if ($this->saldo < $jumlah) {
            return false; // Saldo tidak cukup
        }

        $this->saldo -= $jumlah;
        $this->save();
        return true;
    }

    // Update saldo
    public function updateSaldo($jumlah, $tipe = 'tambah')
    {
        if ($tipe == 'tambah') {
            $this->saldo += $jumlah;
        } else {
            $this->saldo -= $jumlah;
        }
        $this->save();
    }

    // Hitung bunga bulanan
    public function hitungBungaBulanan()
    {
        if ($this->status !== 'Aktif' || $this->bunga_persen <= 0) {
            return 0;
        }

        // Hitung bunga: saldo * bunga_persen / 100
        $bunga = ($this->saldo * $this->bunga_persen) / 100;

        return round($bunga, 2);
    }

    // Tambahkan bunga ke saldo
    public function tambahBunga()
    {
        if ($this->status !== 'Aktif') {
            return false;
        }

        $saldoSebelum = $this->saldo;
        $bunga = $this->hitungBungaBulanan();

        if ($bunga > 0) {
            $this->saldo += $bunga;
            $this->tanggal_bunga_terakhir = now();
            $this->save();

            // Log transaksi bunga jika model TransaksiSimpanan ada
            if (class_exists('App\Models\TransaksiSimpanan')) {
                try {
                    // Generate nomor transaksi
                    $lastTransaksi = TransaksiSimpanan::latest('id')->first();
                    $lastNumber = $lastTransaksi ? intval(substr($lastTransaksi->no_transaksi, -6)) : 0;
                    $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                    $noTransaksi = 'TRX' . date('Ymd') . $newNumber;

                    TransaksiSimpanan::create([
                        'simpanan_id' => $this->id,
                        'no_transaksi' => $noTransaksi,
                        'jenis_transaksi' => 'Bunga',
                        'jumlah' => $bunga,
                        'saldo_sebelum' => $saldoSebelum,
                        'saldo_sesudah' => $this->saldo,
                        'tanggal_transaksi' => now(),
                        'keterangan' => 'Bunga bulanan ' . $this->bunga_persen . '% - ' . now()->format('F Y'),
                    ]);
                } catch (\Exception $e) {
                    // Jika gagal buat transaksi, tetap lanjut (bunga sudah ditambah ke saldo)
                    \Log::warning("Gagal mencatat transaksi bunga: " . $e->getMessage());
                }
            }

            return $bunga;
        }

        return false;
    }

    // Static method untuk proses bunga semua simpanan aktif
    public static function prosesBungaBulanan()
    {
        $tanggalSekarang = Carbon::now();
        $bulanSekarang = $tanggalSekarang->month;
        $tahunSekarang = $tanggalSekarang->year;

        // Ambil semua simpanan aktif yang belum dapat bunga bulan ini
        $simpanans = self::where('status', 'Aktif')
            ->where('bunga_persen', '>', 0)
            ->where(function($query) use ($bulanSekarang, $tahunSekarang) {
                $query->whereNull('tanggal_bunga_terakhir')
                    ->orWhere(function($q) use ($bulanSekarang, $tahunSekarang) {
                        $q->whereMonth('tanggal_bunga_terakhir', '!=', $bulanSekarang)
                          ->orWhereYear('tanggal_bunga_terakhir', '!=', $tahunSekarang);
                    });
            })
            ->get();

        $totalBunga = 0;
        $jumlahSimpanan = 0;

        foreach ($simpanans as $simpanan) {
            $bunga = $simpanan->tambahBunga();
            if ($bunga) {
                $totalBunga += $bunga;
                $jumlahSimpanan++;
            }
        }

        return [
            'jumlah_simpanan' => $jumlahSimpanan,
            'total_bunga' => $totalBunga,
        ];
    }
}
