<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">Data Pinjaman</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pinjamanModal" wire:click="resetInputFields">
                            <i class="mdi mdi-plus"></i> Tambah Pinjaman
                        </button>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Daftar Pinjaman</h4>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Cari nasabah/no pinjaman..." wire:model.live="search">
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" wire:model.live="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="Diajukan">Diajukan</option>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Ditolak">Ditolak</option>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Menunggak">Menunggak</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No. Pinjaman</th>
                                        <th>Nasabah</th>
                                        <th>Jumlah Pinjaman</th>
                                        <th>Sisa Pinjaman</th>
                                        <th>Jangka Waktu</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pinjamans as $index => $pinjaman)
                                        <tr>
                                            <td>{{ $pinjamans->firstItem() + $index }}</td>
                                            <td>{{ $pinjaman->no_pinjaman }}</td>
                                            <td>{{ $pinjaman->nasabah->nama }}</td>
                                            <td>Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</td>
                                            <td>{{ $pinjaman->jangka_waktu }} Bulan</td>
                                            <td>
                                                @if($pinjaman->status == 'Diajukan')
                                                    <label class="badge badge-warning">Diajukan</label>
                                                @elseif($pinjaman->status == 'Disetujui')
                                                    <label class="badge badge-primary">Disetujui</label>
                                                @elseif($pinjaman->status == 'Ditolak')
                                                    <label class="badge badge-danger">Ditolak</label>
                                                @elseif($pinjaman->status == 'Lunas')
                                                    <label class="badge badge-success">Lunas</label>
                                                @else
                                                    <label class="badge badge-danger">Menunggak</label>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" wire:click="detail({{ $pinjaman->id }})">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                @if($pinjaman->status == 'Diajukan')
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal" wire:click="edit({{ $pinjaman->id }})">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $pinjaman->id }})">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data pinjaman</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $pinjamans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pinjaman -->
    <div class="modal fade" id="pinjamanModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengajuan Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nasabah <span class="text-danger">*</span></label>
                                    <select class="form-control @error('nasabah_id') is-invalid @enderror" wire:model="nasabah_id">
                                        <option value="">Pilih Nasabah</option>
                                        @foreach($nasabahs as $nasabah)
                                            <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} - {{ $nasabah->nik }}</option>
                                        @endforeach
                                    </select>
                                    @error('nasabah_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pinjaman <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jumlah_pinjaman') is-invalid @enderror" wire:model="jumlah_pinjaman" placeholder="Masukkan jumlah">
                                    @error('jumlah_pinjaman') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jangka Waktu (Bulan) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jangka_waktu') is-invalid @enderror" wire:model="jangka_waktu" placeholder="Dalam bulan">
                                    @error('jangka_waktu') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bunga Per Tahun (%)</label>
                                    <input type="number" step="0.01" class="form-control" wire:model="bunga_persen" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pinjaman <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_pinjaman') is-invalid @enderror" wire:model="tanggal_pinjaman">
                                    @error('tanggal_pinjaman') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('keperluan') is-invalid @enderror" wire:model="keperluan" rows="3" placeholder="Tujuan pinjaman"></textarea>
                                    @error('keperluan') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <button type="button" class="btn btn-primary" wire:click="hitungPinjaman">
                                    <i class="mdi mdi-calculator"></i> Hitung Angsuran
                                </button>
                            </div>

                            <div class="col-md-6">
                                @if($showSimulasi)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Simulasi Pinjaman</h5>
                                        <hr>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td><strong>Jumlah Pinjaman</strong></td>
                                                <td class="text-end">Rp {{ $jumlah_pinjaman ? number_format($jumlah_pinjaman, 0, ',', '.') : '0' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bunga ({{ $bunga_persen ?? 0 }}%)</strong></td>
                                                <td class="text-end">Rp {{ $total_bunga ? number_format($total_bunga, 0, ',', '.') : '0' }}</td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Total Pinjaman</strong></td>
                                                <td class="text-end"><strong>Rp {{ $total_pinjaman ? number_format($total_pinjaman, 0, ',', '.') : '0' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jangka Waktu</strong></td>
                                                <td class="text-end">{{ $jangka_waktu ?? 0 }} Bulan</td>
                                            </tr>
                                            <tr class="bg-primary text-white">
                                                <td><strong>Angsuran per Bulan</strong></td>
                                                <td class="text-end"><strong>Rp {{ $angsuran_perbulan ? number_format($angsuran_perbulan, 0, ',', '.') : '0' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-muted small">
                                                    (Pokok: Rp {{ $angsuran_pokok ? number_format($angsuran_pokok, 0, ',', '.') : '0' }} +
                                                    Bunga: Rp {{ $angsuran_bunga ? number_format($angsuran_bunga, 0, ',', '.') : '0' }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Jatuh Tempo</strong></td>
                                                <td class="text-end">{{ $tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}</td>
                                            </tr>
                                        </table>

                                        <h6 class="mt-3">Jadwal Angsuran:</h6>
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Ke</th>
                                                        <th>Jatuh Tempo</th>
                                                        <th class="text-end">Angsuran</th>
                                                        <th class="text-end">Sisa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($simulasiData as $data)
                                                    <tr>
                                                        <td>{{ $data['angsuran_ke'] }}</td>
                                                        <td>{{ $data['tanggal_jatuh_tempo'] }}</td>
                                                        <td class="text-end">Rp {{ number_format($data['total_angsuran'], 0, ',', '.') }}</td>
                                                        <td class="text-end">Rp {{ number_format($data['sisa_pinjaman'], 0, ',', '.') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information"></i> Silakan isi form dan klik "Hitung Angsuran" untuk melihat simulasi
                                </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="store" {{ !$showSimulasi ? 'disabled' : '' }}>
                        Simpan Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Status -->
    <div class="modal fade" id="statusModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">No. Pinjaman</label>
                        <input type="text" class="form-control" value="{{ $no_pinjaman ?? '-' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" wire:model="status">
                            <option value="Diajukan">Diajukan</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" wire:model="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="update">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="150">No. Pinjaman</th>
                                    <td>: {{ $no_pinjaman ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Pinjaman</th>
                                    <td>: Rp {{ $jumlah_pinjaman ? number_format($jumlah_pinjaman, 0, ',', '.') : '0' }}</td>
                                </tr>
                                <tr>
                                    <th>Bunga</th>
                                    <td>: {{ $bunga_persen ?? 0 }}% (Rp {{ $total_bunga ? number_format($total_bunga, 0, ',', '.') : '0' }})</td>
                                </tr>
                                <tr>
                                    <th>Total Pinjaman</th>
                                    <td>: <strong>Rp {{ $total_pinjaman ? number_format($total_pinjaman, 0, ',', '.') : '0' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Jangka Waktu</th>
                                    <td>: {{ $jangka_waktu ?? 0 }} Bulan</td>
                                </tr>
                                <tr>
                                    <th>Angsuran/Bulan</th>
                                    <td>: <strong>Rp {{ $angsuran_perbulan ? number_format($angsuran_perbulan, 0, ',', '.') : '0' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="150">Sisa Pinjaman</th>
                                    <td>: Rp {{ $sisa_pinjaman ? number_format($sisa_pinjaman, 0, ',', '.') : '0' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pinjaman</th>
                                    <td>: {{ $tanggal_pinjaman ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jatuh Tempo</th>
                                    <td>: {{ $tanggal_jatuh_tempo ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>:
                                        @if($status == 'Diajukan')
                                            <label class="badge badge-warning">Diajukan</label>
                                        @elseif($status == 'Disetujui')
                                            <label class="badge badge-primary">Disetujui</label>
                                        @elseif($status == 'Ditolak')
                                            <label class="badge badge-danger">Ditolak</label>
                                        @elseif($status == 'Lunas')
                                            <label class="badge badge-success">Lunas</label>
                                        @else
                                            <label class="badge badge-danger">Menunggak</label>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Keperluan</th>
                                    <td>: {{ $keperluan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>: {{ $keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(pinjamanid) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus pinjaman ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', pinjamanid);
                }
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('saved', message => {
                Swal.fire({
                    title: 'Sukses!',
                    text: message.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    var modalElement = document.getElementById('pinjamanModal');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                });
            });

            Livewire.on('updated', message => {
                Swal.fire({
                    title: 'Sukses!',
                    text: message.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    var modalElement = document.getElementById('statusModal');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                });
            });

            Livewire.on('deleted', (event) => {
                Swal.fire({
                    title: 'Sukses!',
                    text: event.message,
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
