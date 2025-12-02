<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">Pembayaran Angsuran</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <span class="text-muted">Kelola pembayaran cicilan pinjaman</span>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Daftar Pinjaman & Angsuran</h4>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Cari nasabah/no pinjaman..." wire:model.live="search">
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" wire:model.live="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="Disetujui">Aktif</option>
                                    <option value="Menunggak">Menunggak</option>
                                    <option value="Lunas">Lunas</option>
                                </select>
                            </div>
                        </div>

                        <div class="accordion" id="pinjamanAccordion">
                            @forelse($pinjamans as $index => $pinjaman)
                                @php
                                    $totalAngsuran = $pinjaman->angsurans->count();
                                    $angsuranLunas = $pinjaman->angsurans->where('status', 'Lunas')->count();
                                    $angsuranBelumBayar = $pinjaman->angsurans->where('status', 'Belum Bayar')->count();
                                    $angsuranTerlambat = $pinjaman->angsurans->where('status', 'Terlambat')->count();
                                    $progressPercentage = $totalAngsuran > 0 ? ($angsuranLunas / $totalAngsuran) * 100 : 0;
                                @endphp

                                <div class="accordion-item mb-2" style="border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                    <h2 class="accordion-header" id="heading{{ $pinjaman->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $pinjaman->id }}" aria-expanded="false" aria-controls="collapse{{ $pinjaman->id }}" style="background-color: #f8f9fa;">
                                            <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <strong>{{ $pinjaman->no_pinjaman }}</strong> - {{ $pinjaman->nasabah->nama }}
                                                    </h6>
                                                    <div class="d-flex gap-3 text-muted small">
                                                        <span><i class="mdi mdi-cash"></i> Pinjaman: Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</span>
                                                        <span><i class="mdi mdi-calendar"></i> Jangka Waktu: {{ $pinjaman->jangka_waktu }} bulan</span>
                                                        <span><i class="mdi mdi-percent"></i> Bunga: {{ $pinjaman->suku_bunga }}%</span>
                                                    </div>
                                                </div>
                                                <div class="text-end" style="min-width: 200px;">
                                                    @if($pinjaman->status == 'Disetujui')
                                                        <span class="badge badge-primary mb-1">Aktif</span>
                                                    @elseif($pinjaman->status == 'Menunggak')
                                                        <span class="badge badge-warning mb-1">Menunggak</span>
                                                    @else
                                                        <span class="badge badge-success mb-1">Lunas</span>
                                                    @endif
                                                    <div class="small text-muted">
                                                        <span class="text-success">{{ $angsuranLunas }} Lunas</span> /
                                                        <span class="text-warning">{{ $angsuranBelumBayar }} Belum</span>
                                                        @if($angsuranTerlambat > 0)
                                                            / <span class="text-danger">{{ $angsuranTerlambat }} Terlambat</span>
                                                        @endif
                                                    </div>
                                                    <div class="progress mt-1" style="height: 6px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $pinjaman->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $pinjaman->id }}" data-bs-parent="#pinjamanAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead style="background-color: #f8f9fa;">
                                                        <tr>
                                                            <th>Angsuran Ke</th>
                                                            <th>No. Angsuran</th>
                                                            <th>Angsuran Pokok</th>
                                                            <th>Angsuran Bunga</th>
                                                            <th>Denda</th>
                                                            <th>Total</th>
                                                            <th>Jatuh Tempo</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pinjaman->angsurans->sortBy('angsuran_ke') as $angsuran)
                                                            <tr class="{{ $angsuran->status == 'Terlambat' ? 'table-warning' : ($angsuran->status == 'Lunas' ? 'table-light' : '') }}">
                                                                <td>
                                                                    <strong>{{ $angsuran->angsuran_ke }}</strong>
                                                                </td>
                                                                <td>{{ $angsuran->no_angsuran }}</td>
                                                                <td>Rp {{ number_format($angsuran->angsuran_pokok, 0, ',', '.') }}</td>
                                                                <td>Rp {{ number_format($angsuran->angsuran_bunga, 0, ',', '.') }}</td>
                                                                <td>
                                                                    @if($angsuran->denda > 0)
                                                                        <span class="text-danger fw-bold">Rp {{ number_format($angsuran->denda, 0, ',', '.') }}</span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <strong>Rp {{ number_format($angsuran->angsuran_pokok + $angsuran->angsuran_bunga + $angsuran->denda, 0, ',', '.') }}</strong>
                                                                </td>
                                                                <td>
                                                                    {{ $angsuran->tanggal_jatuh_tempo->format('d/m/Y') }}
                                                                    @if($angsuran->hari_terlambat > 0)
                                                                        <br><small class="text-danger">(+{{ $angsuran->hari_terlambat }} hari)</small>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($angsuran->status == 'Belum Bayar')
                                                                        <label class="badge badge-warning">Belum Bayar</label>
                                                                    @elseif($angsuran->status == 'Lunas')
                                                                        <label class="badge badge-success">Lunas</label>
                                                                        @if($angsuran->tanggal_bayar)
                                                                            <br><small class="text-muted">{{ $angsuran->tanggal_bayar->format('d/m/Y') }}</small>
                                                                        @endif
                                                                    @else
                                                                        <label class="badge badge-danger">Terlambat</label>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group" role="group">
                                                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" wire:click="detail({{ $angsuran->id }})" title="Detail">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </button>
                                                                        @if($angsuran->status != 'Lunas')
                                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bayarModal" wire:click="bayar({{ $angsuran->id }})" title="Bayar">
                                                                                <i class="mdi mdi-cash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot style="background-color: #f8f9fa;">
                                                        <tr>
                                                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                            <td><strong>Rp {{ number_format($pinjaman->angsurans->sum('angsuran_pokok'), 0, ',', '.') }}</strong></td>
                                                            <td><strong>Rp {{ number_format($pinjaman->angsurans->sum('angsuran_bunga'), 0, ',', '.') }}</strong></td>
                                                            <td><strong class="text-danger">Rp {{ number_format($pinjaman->angsurans->sum('denda'), 0, ',', '.') }}</strong></td>
                                                            <td colspan="4"><strong>Rp {{ number_format($pinjaman->angsurans->sum('angsuran_pokok') + $pinjaman->angsurans->sum('angsuran_bunga') + $pinjaman->angsurans->sum('denda'), 0, ',', '.') }}</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted">Tidak ada data pinjaman</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-3">
                            {{ $pinjamans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bayar -->
    <div class="modal fade" id="bayarModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Pembayaran Angsuran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedAngsuran)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Informasi Nasabah</h6>
                                    <p class="mb-1"><strong>Nama:</strong> {{ $selectedAngsuran->pinjaman->nasabah->nama }}</p>
                                    <p class="mb-1"><strong>No. Pinjaman:</strong> {{ $selectedAngsuran->pinjaman->no_pinjaman }}</p>
                                    <p class="mb-0"><strong>No. Angsuran:</strong> {{ $no_angsuran }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Detail Angsuran</h6>
                                    <p class="mb-1"><strong>Angsuran Ke:</strong> {{ $angsuran_ke }} / {{ $selectedAngsuran->pinjaman->jangka_waktu }}</p>
                                    <p class="mb-1"><strong>Jatuh Tempo:</strong> {{ $selectedAngsuran->tanggal_jatuh_tempo->format('d/m/Y') }}</p>
                                    @if($hari_terlambat > 0)
                                        <p class="mb-0 text-danger"><strong>Terlambat:</strong> {{ $hari_terlambat }} hari</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Rincian Pembayaran</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Angsuran Pokok</strong></td>
                                    <td class="text-end">Rp {{ number_format($angsuran_pokok, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Angsuran Bunga</strong></td>
                                    <td class="text-end">Rp {{ number_format($angsuran_bunga, 0, ',', '.') }}</td>
                                </tr>
                                @if($denda > 0)
                                <tr class="text-danger">
                                    <td><strong>Denda Keterlambatan</strong></td>
                                    <td class="text-end">Rp {{ number_format($denda, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td><h5 class="mb-0"><strong>Total Bayar</strong></h5></td>
                                    <td class="text-end"><h5 class="mb-0 text-primary"><strong>Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong></h5></td>
                                </tr>
                            </table>

                            <div class="mt-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" wire:model="keterangan" rows="2" placeholder="Catatan pembayaran (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Batal</button>
                    <button type="button" class="btn btn-success" wire:click="prosesBayar">
                        <i class="mdi mdi-cash"></i> Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Angsuran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedAngsuran)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="150">No. Angsuran</th>
                                    <td>: {{ $no_angsuran }}</td>
                                </tr>
                                <tr>
                                    <th>No. Pinjaman</th>
                                    <td>: {{ $selectedAngsuran->pinjaman->no_pinjaman }}</td>
                                </tr>
                                <tr>
                                    <th>Nasabah</th>
                                    <td>: {{ $selectedAngsuran->pinjaman->nasabah->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Angsuran Ke</th>
                                    <td>: {{ $angsuran_ke }} dari {{ $selectedAngsuran->pinjaman->jangka_waktu }}</td>
                                </tr>
                                <tr>
                                    <th>Angsuran Pokok</th>
                                    <td>: Rp {{ number_format($angsuran_pokok, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Angsuran Bunga</th>
                                    <td>: Rp {{ number_format($angsuran_bunga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Denda</th>
                                    <td>:
                                        @if($denda > 0)
                                            <span class="text-danger">Rp {{ number_format($denda, 0, ',', '.') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="150">Jatuh Tempo</th>
                                    <td>: {{ $tanggal_jatuh_tempo }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Bayar</th>
                                    <td>: {{ $tanggal_bayar ? $tanggal_bayar : '-' }}</td>
                                </tr>
                                @if($hari_terlambat > 0)
                                <tr>
                                    <th>Hari Terlambat</th>
                                    <td>: <span class="text-danger">{{ $hari_terlambat }} hari</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Status</th>
                                    <td>:
                                        @if($status == 'Belum Bayar')
                                            <label class="badge badge-warning">Belum Bayar</label>
                                        @elseif($status == 'Lunas')
                                            <label class="badge badge-success">Lunas</label>
                                        @else
                                            <label class="badge badge-danger">Terlambat</label>
                                        @endif
                                    </td>
                                </tr>
                                @if($status == 'Lunas')
                                <tr>
                                    <th>Jumlah Dibayar</th>
                                    <td>: <strong>Rp {{ number_format($jumlah_bayar, 0, ',', '.') }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Sisa Pinjaman</th>
                                    <td>: Rp {{ number_format($sisa_pinjaman, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>: {{ $keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('saved', message => {
                Swal.fire({
                    title: 'Sukses!',
                    text: message.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    var modalElement = document.getElementById('bayarModal');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
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

            Livewire.on('close-modal', () => {
                const modalElement = document.getElementById('bayarModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            });
        });
    </script>
</div>
