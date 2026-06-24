<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\DB;

class SyncAllAttendance extends Command
{
    protected $signature = 'google-sheet:sync-all';
    protected $description = 'Sync all existing attendance data from MySQL to Google Sheets';

    public function handle()
    {
        $this->info('Mengambil data absensi dari MySQL...');

        // Mengambil semua data absensi dari tabel 'attendances'
        // Jika data sangat banyak, disarankan menggunakan chunk() untuk menghemat memori
        $attendances = DB::table('attendances')->orderBy('timestamp', 'asc')->get();

        if ($attendances->isEmpty()) {
            $this->warn('Tidak ada data absensi di database.');
            return;
        }

        $this->info('Menyiapkan ' . $attendances->count() . ' data untuk dikirim...');

        $rowsToAppend = [];
        foreach ($attendances as $row) {
            $rowsToAppend[] = [
                $row->employee_id,
                $row->timestamp,
                $row->status1,
                $row->status2,
                $row->status3,
                $row->status4,
                $row->status5,
                $row->created_at
            ];
        }

        $this->info('Mengirim data ke Google Sheet (sheet1)...');

        try {
            $sheetService = new GoogleSheetService();
            
            // Mengirim data secara massal (bulk) dalam 1 kali request
            $sheetService->appendRow('sheet1', $rowsToAppend);
            
            $this->info('Sinkronisasi massal berhasil! ' . count($rowsToAppend) . ' data telah disalin.');
        } catch (\Exception $e) {
            $this->error('Sinkronisasi gagal: ' . $e->getMessage());
        }
    }
}
