<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> Dashboard
                </h3>
                <nav aria-label="breadcrumb">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">
                            <button class="btn btn-sm btn-gradient-primary" wire:click="refresh">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Nasabah
                            <i class="mdi mdi-account-multiple float-end"></i>
                        </h4>
                        <h2 class="mb-2">{{ number_format($totalNasabah) }}</h2>
                        <h6 class="card-text">Aktif: {{ number_format($nasabahAktif) }}</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Simpanan
                            <i class="mdi mdi-cash-multiple float-end"></i>
                        </h4>
                        <h2 class="mb-2">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h2>
                        <h6 class="card-text">Dari semua rekening</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Pinjaman Aktif
                            <i class="mdi mdi-currency-usd float-end"></i>
                        </h4>
                        <h2 class="mb-2">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</h2>
                        <h6 class="card-text">{{ $pinjamanAktif }} Pinjaman</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Angsuran
                            <i class="mdi mdi-calendar-check float-end"></i>
                        </h4>
                        <h2 class="mb-2">{{ number_format($angsuranBelumBayar) }}</h2>
                        <h6 class="card-text">Belum Bayar | Terlambat: {{ $angsuranTerlambat }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Transaksi Simpanan (7 Hari Terakhir)</h4>
                        <canvas id="transaksiChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Statistik Pinjaman</h4>
                        <canvas id="pinjamanChart"></canvas>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <p class="mb-0">Aktif</p>
                                <p class="mb-0">{{ $pinjamanAktif }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0">Lunas</p>
                                <p class="mb-0">{{ $pinjamanLunas }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables Row -->
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Nasabah Terbaru</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nasabahTerbaru as $nasabah)
                                        <tr>
                                            <td>{{ $nasabah->nama }}</td>
                                            <td>{{ $nasabah->nik }}</td>
                                            <td>
                                                @if($nasabah->status == 'Aktif')
                                                    <label class="badge badge-success">Aktif</label>
                                                @else
                                                    <label class="badge badge-danger">Tidak Aktif</label>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('nasabah') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Angsuran Jatuh Tempo (7 Hari)</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nasabah</th>
                                        <th>Angsuran Ke</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($angsuranJatuhTempo as $angsuran)
                                        <tr>
                                            <td>{{ $angsuran->pinjaman->nasabah->nama }}</td>
                                            <td>{{ $angsuran->angsuran_ke }}</td>
                                            <td>{{ $angsuran->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                                            <td>Rp {{ number_format($angsuran->angsuran_pokok + $angsuran->angsuran_bunga, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('angsuran') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            // Chart Transaksi Simpanan
            const transaksiData = @json($transaksiHarian);
            const ctxTransaksi = document.getElementById('transaksiChart').getContext('2d');
            new Chart(ctxTransaksi, {
                type: 'line',
                data: {
                    labels: transaksiData.labels,
                    datasets: [{
                        label: 'Setoran',
                        data: transaksiData.setoran,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4
                    }, {
                        label: 'Penarikan',
                        data: transaksiData.penarikan,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // Chart Pinjaman
            const ctxPinjaman = document.getElementById('pinjamanChart').getContext('2d');
            new Chart(ctxPinjaman, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Lunas'],
                    datasets: [{
                        data: [@json($pinjamanAktif), @json($pinjamanLunas)],
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(75, 192, 192)'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });

            // Listen untuk event refreshed
            Livewire.on('refreshed', (event) => {
                Swal.fire({
                    title: 'Sukses!',
                    text: event.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });
    </script>
</div>
