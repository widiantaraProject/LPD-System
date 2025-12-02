<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">Data Simpanan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#simpananModal" wire:click="resetInputFields">
                            <i class="mdi mdi-plus"></i> Tambah Simpanan
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
                            <h4 class="card-title mb-0">Daftar Simpanan</h4>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Cari nasabah/no rekening..." wire:model.live="search">
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" wire:model.live="filterJenis">
                                    <option value="">Semua Jenis</option>
                                    <option value="Simpanan Pokok">Simpanan Pokok</option>
                                    <option value="Simpanan Wajib">Simpanan Wajib</option>
                                    <option value="Simpanan Sukarela">Simpanan Sukarela</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" wire:model.live="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No. Rekening</th>
                                        <th>Nama Nasabah</th>
                                        <th>Jenis Simpanan</th>
                                        <th>Saldo</th>
                                        <th>Bunga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($simpanans as $index => $simpanan)
                                        <tr>
                                            <td>{{ $simpanans->firstItem() + $index }}</td>
                                            <td>{{ $simpanan->no_rekening }}</td>
                                            <td>{{ $simpanan->nasabah->nama }}</td>
                                            <td>
                                                @if($simpanan->jenis_simpanan == 'Simpanan Pokok')
                                                    <label class="badge badge-primary">{{ $simpanan->jenis_simpanan }}</label>
                                                @elseif($simpanan->jenis_simpanan == 'Simpanan Wajib')
                                                    <label class="badge badge-info">{{ $simpanan->jenis_simpanan }}</label>
                                                @else
                                                    <label class="badge badge-warning">{{ $simpanan->jenis_simpanan }}</label>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($simpanan->saldo, 0, ',', '.') }}</td>
                                            <td>{{ $simpanan->bunga_persen }}%</td>
                                            <td>
                                                @if($simpanan->status == 'Aktif')
                                                    <label class="badge badge-success">Aktif</label>
                                                @else
                                                    <label class="badge badge-danger">Tidak Aktif</label>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" wire:click="edit({{ $simpanan->id }})">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#simpananModal" wire:click="edit({{ $simpanan->id }})">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $simpanan->id }})">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data simpanan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $simpanans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="simpananModal" tabindex="-1" aria-labelledby="simpananModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="simpananModalLabel">
                        {{ $isEdit ? 'Edit Simpanan' : 'Tambah Simpanan' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nasabah <span class="text-danger">*</span></label>
                                <select class="form-control @error('nasabah_id') is-invalid @enderror" wire:model="nasabah_id" {{ $isEdit ? 'disabled' : '' }}>
                                    <option value="">Pilih Nasabah</option>
                                    @foreach($nasabahs as $nasabah)
                                        <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} - {{ $nasabah->nik }}</option>
                                    @endforeach
                                </select>
                                @error('nasabah_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Simpanan <span class="text-danger">*</span></label>
                                <select class="form-control @error('jenis_simpanan') is-invalid @enderror" wire:model.live="jenis_simpanan" {{ $isEdit ? 'disabled' : '' }}>
                                    <option value="">Pilih Jenis Simpanan</option>
                                    <option value="Simpanan Pokok">Simpanan Pokok</option>
                                    <option value="Simpanan Wajib">Simpanan Wajib</option>
                                    <option value="Simpanan Sukarela">Simpanan Sukarela</option>
                                </select>
                                @error('jenis_simpanan') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if($isEdit)
                        <div class="mb-3">
                            <label class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" value="{{ $no_rekening }}" disabled>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Saldo Awal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('saldo') is-invalid @enderror" wire:model="saldo" placeholder="Masukkan saldo awal" {{ $isEdit ? 'disabled' : '' }}>
                                @error('saldo') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bunga (%)</label>
                                <input type="number" step="0.01" class="form-control" wire:model="bunga_persen" placeholder="Bunga" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Buka <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_buka') is-invalid @enderror" wire:model="tanggal_buka">
                                @error('tanggal_buka') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" wire:model="keterangan" rows="3" placeholder="Keterangan tambahan"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Batal</button>
                    @if($isEdit)
                        <button type="button" class="btn btn-primary" wire:click="update">Update</button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store">Simpan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Simpanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">No. Rekening</th>
                            <td>: {{ $no_rekening }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Simpanan</th>
                            <td>: {{ $jenis_simpanan }}</td>
                        </tr>
                        <tr>
                            <th>Saldo</th>
                            <td>: Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Bunga</th>
                            <td>: {{ $bunga_persen }}%</td>
                        </tr>
                        <tr>
                            <th>Tanggal Buka</th>
                            <td>: {{ $tanggal_buka }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>:
                                @if($status == 'Aktif')
                                    <label class="badge badge-success">Aktif</label>
                                @else
                                    <label class="badge badge-danger">Tidak Aktif</label>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>: {{ $keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(simpananid) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus simpanan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', simpananid);
                }
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('delete', (event) => {
                Swal.fire({
                    title: 'Sukses!',
                    text: event.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            Livewire.on('saved', message => {
                Swal.fire({
                    title: 'Sukses!',
                    text: message.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    var modalElement = document.getElementById('simpananModal');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
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
                    var modalElement = document.getElementById('simpananModal');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                });
            });

            Livewire.on('close-modal', () => {
                const modalElement = document.getElementById('simpananModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>

</div>
