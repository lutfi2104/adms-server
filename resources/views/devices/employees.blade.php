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

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Karyawan (di Mesin)</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>{{ $emp->employee_id }}</td>
                                <td><strong>{{ $emp->name }}</strong></td>
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
                                <td colspan="3" class="text-center">Belum ada data karyawan. Tambahkan di form sebelah kanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $employees->links() }}
            </div>
        </div>

        <!-- Add Employee Form -->
        <div class="col-md-4">
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
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Lutfi">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
