<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">Transaksi Simpanan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transaksiModal" wire:click="resetInputFields">
                            <i class="mdi mdi-plus"></i> Tambah Transaksi
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
                            <h4 class="card-title mb-0">Daftar Transaksi</h4>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Cari nasabah/no transaksi..." wire:model.live="search">
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" wire:model.live="filterJenis">
                                    <option value="">Semua Jenis</option>
                                    <option value="Setoran">Setoran</option>
                                    <option value="Penarikan">Penarikan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" wire:model.live="filterTanggal">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No. Transaksi</th>
                                        <th>Nasabah</th>
                                        <th>No. Rekening</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksis as $index => $transaksi)
                                        <tr>
                                            <td>{{ $transaksis->firstItem() + $index }}</td>
                                            <td>{{ $transaksi->no_transaksi }}</td>
                                            <td>{{ $transaksi->simpanan->nasabah->nama }}</td>
                                            <td>{{ $transaksi->simpanan->no_rekening }}</td>
                                            <td>
                                                @if($transaksi->jenis_transaksi == 'Setoran')
                                                    <label class="badge badge-success">{{ $transaksi->jenis_transaksi }}</label>
                                                @else
                                                    <label class="badge badge-danger">{{ $transaksi->jenis_transaksi }}</label>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                                            <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" wire:click="view({{ $transaksi->id }})">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data transaksi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $transaksis->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Transaksi -->
    <div class="modal fade" id="transaksiModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Rekening Simpanan <span class="text-danger">*</span></label>
                            <select class="form-control @error('simpanan_id') is-invalid @enderror" wire:model.live="simpanan_id">
                                <option value="">Pilih Rekening</option>
                                @foreach($simpanans as $simpanan)
                                    <option value="{{ $simpanan->id }}">
                                        {{ $simpanan->no_rekening }} - {{ $simpanan->nasabah->nama }} ({{ $simpanan->jenis_simpanan }})
                                    </option>
                                @endforeach
                            </select>
                            @error('simpanan_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        @if($selectedSimpanan)
                        <div class="alert alert-info">
                            <strong>Info Rekening:</strong><br>
                            Nama: {{ $selectedSimpanan->nasabah->nama }}<br>
                            Jenis: {{ $selectedSimpanan->jenis_simpanan }}<br>
                            Saldo Terkini: Rp {{ number_format($saldoTerkini, 0, ',', '.') }}
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                                <select class="form-control @error('jenis_transaksi') is-invalid @enderror" wire:model="jenis_transaksi">
                                    <option value="">Pilih Jenis</option>
                                    <option value="Setoran">Setoran</option>
                                    <option value="Penarikan">Penarikan</option>
                                </select>
                                @error('jenis_transaksi') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_transaksi') is-invalid @enderror" wire:model="tanggal_transaksi">
                                @error('tanggal_transaksi') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror" wire:model="jumlah" placeholder="Masukkan jumlah" min="1" step="1">
                            @error('jumlah') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" wire:model="keterangan" rows="3" placeholder="Keterangan transaksi"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="store">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedSimpanan)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="150">No. Transaksi</th>
                                    <td>: {{ $no_transaksi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nasabah</th>
                                    <td>: {{ $selectedSimpanan->nasabah->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Rekening</th>
                                    <td>: {{ $selectedSimpanan->no_rekening ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Transaksi</th>
                                    <td>:
                                        @if($jenis_transaksi == 'Setoran')
                                            <label class="badge badge-success">{{ $jenis_transaksi }}</label>
                                        @elseif($jenis_transaksi == 'Penarikan')
                                            <label class="badge badge-danger">{{ $jenis_transaksi }}</label>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jumlah</th>
                                    <td>: Rp {{ is_numeric($jumlah) ? number_format($jumlah, 0, ',', '.') : '0' }}</td>
                                </tr>
                                <tr>
                                    <th>Saldo Sebelum</th>
                                    <td>: Rp {{ is_numeric($saldo_sebelum) ? number_format($saldo_sebelum, 0, ',', '.') : '0' }}</td>
                                </tr>
                                <tr>
                                    <th>Saldo Sesudah</th>
                                    <td>: Rp {{ is_numeric($saldo_sesudah) ? number_format($saldo_sesudah, 0, ',', '.') : '0' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="150">Tanggal Transaksi</th>
                                    <td>: {{ $tanggal_transaksi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Petugas</th>
                                    <td>: {{ $petugas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>: {{ $keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="mdi mdi-information"></i> Silakan pilih transaksi terlebih dahulu
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
            Livewire.on('saved', (event) => {
                Swal.fire({
                    title: 'Sukses!',
                    text: event[0].message || 'Transaksi berhasil disimpan!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    var modalElement = document.getElementById('transaksiModal');
                    if (modalElement) {
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }
                });
            });

            Livewire.on('error', (event) => {
                Swal.fire({
                    title: 'Error!',
                    text: event[0].message || 'Terjadi kesalahan!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });

            Livewire.on('close-modal', () => {
                const modalElement = document.getElementById('transaksiModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
            });
        });
    </script>
</div>
