# DOKUMENTASI SISTEM AKSES KELUAR-MASUK (ADMS ZKTECO)

Dokumentasi ini dibuat untuk memudahkan developer selanjutnya dalam memahami arsitektur, cara kerja database, logika bisnis penanganan skenario di lapangan, serta cara pengelolaan data karyawan pada sistem akses pintu keluar-masuk ini.

---

## 1. Cara Kerja Sistem (Arsitektur Alur Data)

Sistem ini menggunakan protokol ADMS (Automatic Data Master System) milik ZKTeco. Mesin fingerprint secara aktif mengirimkan data log scan ke server secara real-time melalui HTTP POST.

### Diagram Alur Data:
1. **Scan Sidik Jari/Kartu**: Karyawan melakukan scan di pintu.
2. **Kirim Data ke Server**: Mesin mengirimkan string data log beserta **Serial Number (SN)** mesin ke endpoint `/iclock/cdata`.
3. **Identifikasi Pintu**: Server mencocokkan SN mesin dengan data di tabel `devices` untuk mengetahui apakah mesin tersebut dipasang di pintu masuk (`entry`) atau keluar (`exit`).
4. **Pencocokan Sesi**: Server mencocokkan log scan ini ke tabel `access_sessions` menggunakan ID Karyawan dan membuat atau memperbarui sesi masuk/keluar serta menghitung durasinya.

---

## 2. Struktur Database

Sistem ini didukung oleh tiga tabel utama:

### A. Tabel `devices` (Alat/Mesin Fingerprint)
Tabel ini mencatat mesin-mesin yang terhubung ke server.
*   `no_sn` (String, Unique): Nomor Serial Mesin.
*   `type` (String, default: `'entry'`): Tipe pintu, bernilai `'entry'` (pintu masuk) atau `'exit'` (pintu keluar). Nilai ini dapat diatur melalui halaman Edit Device di web.

### B. Tabel `employees` (Master Pemetaan Karyawan)
Memetakan ID angka dari mesin ke profil lengkap karyawan.
*   `employee_id` (String, Unique): ID Karyawan (PIN/No. User yang terdaftar di mesin).
*   `nik` (String, Nullable): Nomor Induk Karyawan.
*   `name` (String): Nama Lengkap Karyawan.
*   `gender` (String, Nullable): Jenis Kelamin (`Pria` / `Wanita`).
*   `department` (String, Nullable): Nama Divisi/Departemen.

### C. Tabel `access_sessions` (Log Sesi Keluar-Masuk)
Menyimpan data log akses masuk dan keluar yang sudah berpasangan secara otomatis.
*   `employee_id` (String): ID Karyawan.
*   `entry_time` (DateTime, Nullable): Waktu masuk.
*   `exit_time` (DateTime, Nullable): Waktu keluar.
*   `entry_sn` (String, Nullable): SN mesin pintu masuk.
*   `exit_sn` (String, Nullable): SN mesin pintu keluar.
*   `duration_seconds` (Integer, Nullable): Durasi berada di dalam area dalam hitungan detik.
*   `status` (String): Status sesi (`open`, `completed`, `no_exit`, `no_entry`).

---

## 3. Penanganan Skenario Khusus di Lapangan

Untuk menjaga keakuratan data terhadap kesalahan manusia (human error) atau gangguan teknis, controller `iclockController.php` menerapkan aturan logika berikut:

### A. Skenario Normal (Masuk -> Keluar)
*   **Aksi**: Karyawan scan di pintu Masuk (`entry`), lalu kemudian scan di pintu Keluar (`exit`).
*   **Logika Server**:
    *   Saat scan **Masuk**: Server membuat baris sesi baru dengan status `'open'`.
    *   Saat scan **Keluar**: Server mencari sesi terakhir yang statusnya `'open'`, mengisi `exit_time` & `exit_sn`, menghitung durasi selisih waktu, dan mengubah status menjadi `'completed'`.

### B. Skenario Lupa Scan Keluar (Masuk -> Masuk Lagi)
*   **Aksi**: Karyawan scan **Masuk**, lalu pergi/pulang tanpa scan di pintu Keluar. Keesokan harinya atau beberapa jam kemudian, ia melakukan scan **Masuk** lagi.
*   **Logika Server**:
    *   Saat mendeteksi scan **Masuk** kedua, server melihat sesi sebelumnya masih berstatus `'open'` (belum ada waktu keluar).
    *   Server secara otomatis menutup sesi lama tersebut dengan status **`no_exit` (Lupa Scan Keluar)** (kolom `exit_time` dan `duration` dibiarkan kosong).
    *   Server kemudian membuka sesi `'open'` baru untuk mencatat waktu masuk yang kedua tersebut.

### C. Skenario Lupa Scan Masuk (Keluar Saja)
*   **Aksi**: Karyawan tidak scan di pintu Masuk (misal menyelinap lewat pintu terbuka), tetapi ia scan di pintu **Keluar**.
*   **Logika Server**:
    *   Saat menerima scan **Keluar**, server mencari sesi terakhir yang statusnya `'open'`.
    *   Karena tidak ditemukan sesi masuk yang aktif (`'open'`), server langsung membuat baris sesi baru dengan status **`no_entry` (Lupa Scan Masuk)**, mengisi `exit_time` dan membiarkan `entry_time` kosong. Sesi langsung ditutup sebagai selesai.

### D. Skenario Double-Scan (Scan Berulang Cepat)
*   **Aksi**: Karyawan menempelkan jarinya berkali-kali secara cepat (kurang dari 1 menit) di mesin yang sama karena ragu apakah sudah terekam.
*   **Logika Server**:
    *   Sistem menerapkan *tolerance window* selama **60 detik**. 
    *   Jika ada scan berulang oleh karyawan yang sama di tipe mesin yang sama dalam kurun waktu 1 menit sejak scan terakhir, log berikutnya akan diabaikan/di-filter agar tidak merusak perhitungan sesi.

### E. Listrik Padam / Koneksi Internet Putus
*   **Aksi**: Jaringan internet mati atau server mati, sementara mesin fingerprint tetap menyala dengan baterai/UPS dan karyawan tetap melakukan scan.
*   **Logika Server**:
    *   Mesin ZKTeco memiliki memori internal yang menyimpan log scan secara offline.
    *   Ketika internet/server aktif kembali, mesin akan mengirimkan log-log yang tertunda tersebut sekaligus.
    *   Server akan memproses log berdasarkan **waktu scan asli di mesin (timestamp)**, bukan waktu saat server menerima data. Pencocokan sesi keluar-masuk akan diproses urut secara kronologis.

---

## 4. Cara Import Master Karyawan dari Excel

Agar tidak perlu menginput data karyawan satu per satu secara manual, gunakan fitur Import CSV di halaman **Karyawan**:

1.  **Format Kolom Excel**:
    Buat spreadsheet di Excel dengan 5 kolom pertama harus disusun persis seperti ini:
    *   **Kolom A**: ID Karyawan (Nomor User/PIN yang didaftarkan di mesin fisik)
    *   **Kolom B**: NIK (Nomor Induk Karyawan)
    *   **Kolom C**: Nama Lengkap
    *   **Kolom D**: Jenis Kelamin (`Pria` atau `Wanita`)
    *   **Kolom E**: Departemen

2.  **Menyimpan File**:
    Di Excel, klik **File > Save As**, pilih format **CSV (Comma Delimited) (*.csv)**.

3.  **Unggah File**:
    *   Buka web server, masuk ke menu **Karyawan**.
    *   Pada card **Import CSV Karyawan** di sebelah kanan, pilih file `.csv` tersebut lalu klik **Upload dan Import**.

4.  **Kelebihan Fitur Import**:
    *   **Deteksi Cerdas**: Otomatis mendeteksi pembatas koma (`,`) maupun titik koma (`;`) tergantung regional setting Excel Anda.
    *   **Pembersihan ID**: Secara otomatis menghapus angka `0` di depan ID angka (misal `04` di Excel menjadi `4`) agar sinkron dengan data yang dikirim mesin fingerprint.
    *   **Update Otomatis**: Jika di kemudian hari ada karyawan baru atau ada perubahan nama/departemen di Excel, Anda cukup mengunggah file Excel terbaru. Sistem akan mendeteksi ID lama dan meng-update datanya secara otomatis tanpa membuat data ganda.
