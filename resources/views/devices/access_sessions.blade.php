@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Log Akses Keluar-Masuk (Sesi)</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID Sesi</th>
                    <th>Nama Karyawan</th>
                    <th>ID Karyawan</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Durasi Akses</th>
                    <th>Status</th>
                    <th>Detail Alat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr>
                        <td>{{ $session->id }}</td>
                        <td><strong>{{ $session->employee_name ?? 'Belum Terdaftar' }}</strong></td>
                        <td>{{ $session->employee_id }}</td>
                        <td>
                            @if($session->entry_time)
                                {{ $session->entry_time }}
                            @else
                                <span class="text-muted italic">Tidak tercatat</span>
                            @endif
                        </td>
                        <td>
                            @if($session->exit_time)
                                {{ $session->exit_time }}
                            @else
                                <span class="text-muted italic">Masih di dalam / Tidak tercatat</span>
                            @endif
                        </td>
                        <td>
                            @if($session->duration_seconds !== null)
                                @php
                                    $hours = floor($session->duration_seconds / 3600);
                                    $minutes = floor(($session->duration_seconds / 60) % 60);
                                    $seconds = $session->duration_seconds % 60;
                                    
                                    $durationString = '';
                                    if ($hours > 0) $durationString .= "{$hours} jam ";
                                    if ($minutes > 0) $durationString .= "{$minutes} menit ";
                                    if ($seconds > 0 || empty($durationString)) $durationString .= "{$seconds} detik";
                                @endphp
                                <span class="badge bg-secondary text-white">{{ $durationString }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($session->status === 'open')
                                <span class="badge bg-primary">Berada di Dalam (Open)</span>
                            @elseif($session->status === 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($session->status === 'no_exit')
                                <span class="badge bg-warning text-dark">Lupa Scan Keluar</span>
                            @elseif($session->status === 'no_entry')
                                <span class="badge bg-warning text-dark">Lupa Scan Masuk</span>
                            @else
                                <span class="badge bg-secondary">{{ $session->status }}</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                @if($session->entry_sn)
                                    In: {{ $session->entry_sn }}
                                @endif
                                @if($session->entry_sn && $session->exit_sn)
                                    <br>
                                @endif
                                @if($session->exit_sn)
                                    Out: {{ $session->exit_sn }}
                                @endif
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data aktivitas keluar-masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
