<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    protected $client;
    protected $service;
    protected $documentId;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(base_path(env('GOOGLE_SERVICE_ACCOUNT_JSON_PATH', 'adms-bcn-f1f1cf78d913.json')));
        $this->client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($this->client);
        $this->documentId = env('GOOGLE_SHEET_ID');
    }

    /**
     * Menambahkan baris data baru ke Google Sheet.
     * 
     * @param string $range Nama sheet/sheet tab, contoh: 'Sheet1' atau 'Absensi!A:G'
     * @param array $values Array berisi data baris, contoh: [['101', '2026-06-24 09:00:00', true]]
     */
    public function appendRow($range, array $values)
    {
        $body = new ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        return $this->service->spreadsheets_values->append(
            $this->documentId,
            $range,
            $body,
            $params
        );
    }
}
