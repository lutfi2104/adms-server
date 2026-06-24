<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;

class TestGoogleSheet extends Command
{
    protected $signature = 'google-sheet:test';
    protected $description = 'Test Google Sheet connection by appending a dummy row';

    public function handle()
    {
        $this->info('Mengirim data percobaan ke Google Sheet...');

        try {
            $sheetService = new GoogleSheetService();
            $dummyData = [
                ['TEST-001', now()->toDateTimeString(), '1', '0', '0', '0', '0', 'TEST SYNC']
            ];

            $sheetService->appendRow('sheet1', $dummyData);
            $this->info('Berhasil! Silakan periksa Google Sheet Anda.');
        } catch (\Exception $e) {
            $this->error('Gagal menghubungkan atau mengirim data.');
            $this->error($e->getMessage());
        }
    }
}
