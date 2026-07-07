@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $lable }}</h2>
        {{-- <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Tambah Device</a> --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-bordered data-table" id="devices">
            <thead>
                <tr>
                    <th>Nama Device</th>
                    <th>Serial Number</th>
                    <th>Lokasi</th>
                    <th>Tipe Akses</th>
                    <th>Online Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($log as $d)
                    <tr>
                        <td>{{ $d->nama ?? '-' }}</td>
                        <td>{{ $d->no_sn }}</td>
                        <td>{{ $d->lokasi ?? '-' }}</td>
                        <td>
                            @if($d->type === 'entry')
                                <span class="badge bg-success">Masuk (Entry)</span>
                            @else
                                <span class="badge bg-danger">Keluar (Exit)</span>
                            @endif
                        </td>
                        <td>{{ $d->online }}</td>
                        <td>
                            <a href="{{ route('devices.edit', $d->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
