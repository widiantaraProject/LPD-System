<div>
    <div class="content-wrapper">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title">Data Nasabah</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nasabahModal" wire:click="resetInputFields">
                            <i class="mdi mdi-plus"></i> Tambah Nasabah
                        </button>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Daftar Nasabah</h4>
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Cari nasabah..." wire:model.live="search">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Pekerjaan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nasabahs as $index => $nasabah)
                                        <tr>
                                            <td>{{ $nasabahs->firstItem() + $index }}</td>
                                            <td>{{ $nasabah->nama }}</td>
                                            <td>{{ $nasabah->nik }}</td>
                                            <td>{{ $nasabah->email }}</td>
                                            <td>{{ $nasabah->no_telepon }}</td>
                                            <td>{{ $nasabah->pekerjaan }}</td>
                                            <td>
                                                @if($nasabah->status == 'Aktif')
                                                    <label class="badge badge-success">Aktif</label>
                                                @else
                                                    <label class="badge badge-danger">Tidak Aktif</label>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" wire:click="edit({{ $nasabah->id }})">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#nasabahModal" wire:click="edit({{ $nasabah->id }})">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $nasabah->id }})">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data nasabah</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $nasabahs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="nasabahModal" tabindex="-1" aria-labelledby="nasabahModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nasabahModalLabel">
                        {{ $isEdit ? 'Edit Nasabah' : 'Tambah Nasabah' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama" placeholder="Masukkan nama lengkap">
                                @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" wire:model="nik" placeholder="Masukkan NIK">
                                @error('nik') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" wire:model="alamat" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                            @error('alamat') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" wire:model="no_telepon" placeholder="Masukkan no. telepon">
                                @error('no_telepon') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="Masukkan email">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control @error('jenis_kelamin') is-invalid @enderror" wire:model="jenis_kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" wire:model="tanggal_lahir">
                                @error('tanggal_lahir') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" wire:model="pekerjaan" placeholder="Masukkan pekerjaan">
                                @error('pekerjaan') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h5 class="modal-title" id="detailModalLabel">Detail Nasabah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Lengkap</th>
                            <td>: {{ $nama }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>: {{ $nik }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>: {{ $alamat }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>: {{ $no_telepon }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: {{ $email }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>: {{ $jenis_kelamin }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>: {{ $tanggal_lahir }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>: {{ $pekerjaan }}</td>
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
        function confirmDelete(nasabahid) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus nasabah ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', nasabahid);
                }
            });
        }

        // Listen untuk event delete success
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
                    showConfirmButton: false // Tidak perlu tombol OK jika ada timer
                }).then(() => {
                    // Tutup modal setelah SweetAlert ditutup
                    var modalElement = document.getElementById('nasabahModal');
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
                    showConfirmButton: false // Tidak perlu tombol OK jika ada timer
                }).then(() => {
                    // Tutup modal setelah SweetAlert ditutup
                    var modalElement = document.getElementById('nasabahModal');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                });
            });

            Livewire.on('close-modal', () => {
                const modalElement = document.getElementById('nasabahModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>

</div>
