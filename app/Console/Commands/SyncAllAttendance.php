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

        // Karena data sangat banyak (160.000+), kita gunakan chunk()
        // untuk memproses data per 2000 baris agar tidak kehabisan memori (out of memory)
        $totalCount = DB::table('attendances')->count();

        if ($totalCount === 0) {
            $this->warn('Tidak ada data absensi di database.');
            return;
        }

        $this->info('Ditemukan total ' . $totalCount . ' data. Memulai pengiriman per batch (2.000 data per pengiriman)...');

        $batchNumber = 1;
        try {
            $sheetService = new GoogleSheetService();

            DB::table('attendances')
                ->orderBy('timestamp', 'asc')
                ->chunk(2000, function ($attendances) use ($sheetService, &$batchNumber, $totalCount) {
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

                    $this->info('Mengirim Batch #' . $batchNumber . ' (' . count($rowsToAppend) . ' data)...');
                    $sheetService->appendRow('sheet1', $rowsToAppend);
                    $batchNumber++;
                });

            $this->info('Sinkronisasi massal berhasil! Total ' . $totalCount . ' data telah disalin.');
        } catch (\Exception $e) {
            $this->error('Sinkronisasi gagal pada Batch #' . $batchNumber . ': ' . $e->getMessage());
        }
    }
}
