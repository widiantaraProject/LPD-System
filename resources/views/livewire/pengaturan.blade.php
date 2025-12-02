<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">Pengaturan Sistem</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <button type="button" class="btn btn-warning btn-sm" wire:click="resetToDefault" onclick="return confirm('Reset semua pengaturan ke nilai default?')">
                            <i class="mdi mdi-restore"></i> Reset Default
                        </button>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Konfigurasi LPD</h4>

                        <form wire:submit.prevent="simpan">
                            @foreach($pengaturansByKategori as $kategori => $items)
                            <div class="mb-4">
                                <h5 class="mb-3">
                                    <i class="mdi {{ $kategori == 'Simpanan' ? 'mdi-cash-multiple' : 'mdi-currency-usd' }} me-2"></i>
                                    {{ $kategori }}
                                </h5>
                                <div class="row">
                                    @foreach($items as $index => $item)
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <label class="form-label">
                                                        <strong>{{ $item['label'] }}</strong>
                                                        @if($item['deskripsi'])
                                                            <i class="mdi mdi-information-outline text-muted" data-bs-toggle="tooltip" title="{{ $item['deskripsi'] }}"></i>
                                                        @endif
                                                    </label>
                                                    <div class="input-group">
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            class="form-control"
                                                            wire:model="pengaturans.{{ array_search($item, $pengaturans) }}.value"
                                                        >
                                                        <span class="input-group-text">
                                                            @if(str_contains($item['key'], 'bunga') || str_contains($item['key'], 'denda'))
                                                                % / bulan
                                                            @else
                                                                Rp
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @if($item['deskripsi'])
                                                        <small class="text-muted d-block mt-1">{{ $item['deskripsi'] }}</small>
                                                    @endif

                                                    @if(str_contains($item['key'], 'bunga'))
                                                        @php
                                                            $nilaiPerBulan = floatval($item['value']);
                                                            $nilaiPerTahun = $nilaiPerBulan * 12;
                                                        @endphp
                                                        <small class="text-info d-block mt-1">
                                                            <i class="mdi mdi-information"></i> Setara ~{{ number_format($nilaiPerTahun, 2) }}% per tahun
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <hr>
                            @endforeach

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="mdi mdi-content-save"></i> Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Pengaturan</h5>
                        <div class="alert alert-info">
                            <h6><i class="mdi mdi-information"></i> Panduan Pengaturan:</h6>
                            <ul class="mb-0">
                                <li><strong>Bunga Simpanan:</strong> Dihitung per bulan dari saldo simpanan. Bunga akan ditambahkan otomatis setiap awal bulan.</li>
                                <li><strong>Bunga Pinjaman:</strong> Bunga flat per bulan yang diterapkan pada pinjaman</li>
                                <li><strong>Denda Keterlambatan:</strong> Denda per hari dari jumlah angsuran pokok</li>
                                <li><strong>Minimal Simpanan:</strong> Setoran minimal yang harus dipenuhi nasabah saat membuka rekening</li>
                                <li><strong>Konversi:</strong> Bunga per bulan × 12 = Bunga per tahun (estimasi)</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <h6><i class="mdi mdi-alert"></i> Catatan Penting:</h6>
                            <ul class="mb-0">
                                <li>Bunga akan dihitung dan ditambahkan secara otomatis setiap tanggal 1 jam 00:00</li>
                                <li>Pastikan task scheduler Laravel aktif dengan menjalankan: <code>php artisan schedule:work</code></li>
                                <li>Atau tambahkan cron job: <code>* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1</code></li>
                                <li>Untuk proses manual, jalankan: <code>php artisan simpanan:proses-bunga</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            Livewire.on('saved', message => {
                Swal.fire({
                    title: 'Sukses!',
                    text: message.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            Livewire.on('error', message => {
                Swal.fire({
                    title: 'Error!',
                    text: message.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
</div>
