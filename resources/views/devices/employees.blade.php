@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- List Employees -->
        <div class="col-md-8">
            <h2 class="mb-4">Daftar Karyawan</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Karyawan (Mesin)</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Departemen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>{{ $emp->employee_id }}</td>
                                <td>{{ $emp->nik ?? '-' }}</td>
                                <td><strong>{{ $emp->name }}</strong></td>
                                <td>{{ $emp->gender ?? '-' }}</td>
                                <td>{{ $emp->department ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data karyawan. Tambahkan di form sebelah kanan atau unggah file CSV.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $employees->links() }}
            </div>
        </div>

        <!-- Forms Column -->
        <div class="col-md-4">
            <!-- Import CSV Form -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Import CSV Karyawan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="csv_file" class="form-label">Pilih File CSV</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
                            <small class="text-muted d-block mt-1">
                                Pastikan urutan kolom Excel Anda:<br>
                                <strong>A: ID, B: NIK, C: Nama, D: Jenis Kelamin, E: Departemen</strong><br>
                                Lalu simpan sebagai file <strong>CSV (Comma Delimited)</strong>.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Upload dan Import</button>
                    </form>
                </div>
            </div>

            <!-- Add Employee Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Pemetaan Karyawan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="employee_id" class="form-label">ID Karyawan (PIN di Mesin)</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id') }}" required placeholder="Contoh: 12">
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" placeholder="Contoh: 1001">
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Lutfi">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="gender" class="form-label">Jenis Kelamin</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="Pria" {{ old('gender') === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('gender') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="department" class="form-label">Departemen</label>
                            <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}" placeholder="Contoh: IT">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
