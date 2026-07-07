@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Pemetaan Karyawan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="employee_id" class="form-label">ID Karyawan (PIN di Mesin)</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control" value="{{ $employee->employee_id }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control" value="{{ $employee->nik }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $employee->name }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="gender" class="form-label">Jenis Kelamin</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Pria" {{ $employee->gender === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ $employee->gender === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department" class="form-label">Departemen</label>
                            <input type="text" name="department" id="department" class="form-control" value="{{ $employee->department }}">
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning">Update</button>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
