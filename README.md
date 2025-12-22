# Absensi QR (CodeIgniter 3)

Sistem absensi kantor berbasis **QR Code** dengan **validasi lokasi** dan **device lock** untuk mencegah titip absen.  
Cocok untuk kantor kecil–menengah, gudang, outlet, sekolah, dan tim lapangan.

## Highlight
- ✅ QR Absensi (Masuk / Pulang)
- ✅ Validasi lokasi (radius kantor)
- ✅ 1 User = 1 Device (admin bisa reset)
- ✅ Rekap harian & bulanan
- ✅ Monitor TV (tampilan QR + status live)
- ✅ Admin panel (Users, Settings, Logs)

## Alur Singkat
1. Monitor menampilkan QR (auto refresh)
2. Pegawai scan → isi email → pilih **Masuk / Pulang**
3. Sistem cek token QR, lokasi, device, dan status
4. Data masuk ke logs + rekap tampil di admin

## Tech Stack
- PHP 7+ (CodeIgniter 3)
- MySQL/MariaDB
- Bootstrap 5 + Bootstrap Icons
- SweetAlert (opsional)

## Instalasi Lokal (XAMPP)
1. Clone repo ke `htdocs`
2. Buat database & import SQL
3. Copy config contoh:
   - `application/config/database.example.php` → `database.php`
   - `application/config/config.example.php` → `config.php`
4. Sesuaikan `base_url` dan kredensial DB
5. Akses:
   - Monitor: `/`
   - Absen: `/absen?token=...`
   - Admin: `/admin`

## Keamanan
File sensitif tidak di-commit:
- `application/config/database.php`
- `application/config/config.php`
- secret/token disimpan di setting kantor

## License
Commercial / Private Use (sesuaikan kebutuhan)
