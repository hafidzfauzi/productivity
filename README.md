# FocusHub — Productivity Dashboard 🚀

FocusHub adalah dashboard produktivitas personal interaktif berbasis web yang dibangun dengan **Laravel + Blade + Alpine.js + Tailwind CSS + PostgreSQL (Supabase)**. Desain antarmuka dirancang dengan gaya *glassmorphism* modern serta mendukung mode gelap/terang (*dark/light mode*) secara mulus.

Proyek ini terinspirasi dari konsep [FocusFlow](https://github.com/rikautamii/focusflow), yang dimodifikasi dan dikembangkan menjadi versi mandiri dengan penambahan fitur-fitur pintar seperti deteksi GPS waktu nyata.

---

## ✨ Fitur Utama

1. **Tata Letak Grid Responsif & Glassmorphism Card**:
   - Tampilan visual premium dengan efek blur backdrop dan bayangan halus.
   - Grid dinamis yang menyesuaikan ukuran layar (Mobile, Tablet, Desktop).
   - Tombol toggle mode gelap/terang (*Dark/Light Mode*) dengan penyimpanan otomatis di local storage.

2. **To Do List (Manajemen Tugas) CRUD**:
   - Menambah, menandai selesai, dan menghapus tugas secara langsung.
   - Indikator sisa tugas di widget dashboard utama.
   - Sinkronisasi instan ke database PostgreSQL (Supabase).

3. **Pomodoro Focus Timer**:
   - 4 Mode: **Focus** (25 mnt), **Short Break** (5 mnt), **Long Break** (15 mnt), dan **Power Nap** (20 mnt).
   - Animasi lingkaran *progress ring* berbasis SVG yang presisi.
   - Penghitung waktu berbasis `Date.now()` agar waktu tetap akurat meskipun tab browser berada di latar belakang (*background tab*).
   - Efek suara bel (*chime*) menggunakan Web Audio API saat waktu habis.
   - Notifikasi Desktop saat timer selesai.

4. **Pemutar Musik Terintegrasi**:
   - Embed Spotify Player dengan 4 pilihan *playlist* preset (Lo-Fi Beats, Synthwave, Chill Lofi, Jazz Cafe).
   - Mendukung pemutaran playlist, album, atau track kustom dengan menempelkan URL Spotify langsung di input pencarian.

5. **Sistem Gamifikasi & Produktivitas**:
   - Mendapatkan **+50 XP** untuk setiap sesi fokus (Pomodoro) yang berhasil diselesaikan.
   - Kenaikan Level (setiap 600 XP).
   - **Tumbuh Kembang Tanaman**: Visual pohon bertumbuh berdasarkan tingkat XP (🌱 → 🌿 → 🌳 → 🌸 → 🌺).
   - Penghitung *streak* harian (*daily focus streak*) dan rekor streak terpanjang.

6. **Kutipan Motivasi (Quotes)**:
   - Terintegrasi dengan **ZenQuotes API** (`/api/quotes`).
   - Caching batch berisi 50 kutipan selama 24 jam untuk mencegah *rate-limit* (HTTP 429).
   - Tombol refresh (*New Quote*) untuk mengambil kutipan acak secara instan dari cache lokal.
   - Tampilan kutipan berestetika premium menggunakan karakter pembuka-penutup *smart quotes* (`“` & `”`).

7. **Kalender Interaktif**:
   - Menampilkan tanggal lengkap hari ini dengan indikator bulan.
   - Navigasi bulan sebelum/berikutnya dengan indikator tanggal hari ini disorot dengan warna aksen khusus.

8. **Cuaca & Jadwal Sholat Sinkron GPS**:
   - **Real-Time GPS Location**: Menggunakan Geolocation API pada browser untuk mendeteksi posisi pengguna secara otomatis.
   - **Penyelarasan Lokasi**: Koordinat GPS yang sama dikirimkan ke modul Cuaca (OpenWeatherMap) dan Jadwal Sholat (Aladhan API Kemenag).
   - **Fallback Cilacap Utara**: Jika akses GPS ditolak atau tidak didukung, lokasi otomatis diatur default ke **Cilacap Utara (-7.7011, 109.0233)**.
   - **Reverse Geocoding Dinamis**: Menggunakan OpenStreetMap Nominatim API dalam mode fallback (tanpa OpenWeatherMap API Key) untuk menerjemahkan koordinat GPS menjadi nama kota/kabupaten lokal secara otomatis (contoh: "Jakarta Selatan" atau "Cilacap").
   - **Jadwal Sholat Real-Time**: Sorotan jadwal sholat berikutnya (*Next Prayer*) dihitung dinamis di backend (menggunakan timezone `Asia/Jakarta`) dan di-update otomatis setiap **30 detik** oleh AlpineJS di frontend tanpa perlu me-reload halaman.

---

## 🛠️ Tech Stack

- **Framework**: [Laravel](https://laravel.com)
- **Frontend**: [AlpineJS](https://alpinejs.dev) & Tailwind CSS v4
- **Database**: PostgreSQL (Supabase)
- **APIs**:
  - OpenWeatherMap API (Weather Data)
  - Aladhan API (Prayer Timings - Method Kemenag RI)
  - ZenQuotes API (Motivational Quotes)
  - Nominatim OpenStreetMap API (Reverse Geocoding Fallback)

---

## 🚀 Instalasi & Cara Menjalankan

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- PostgreSQL database (atau database relasional lainnya)

### Langkah-langkah

1. **Clone Repositori**:
   ```bash
   git clone <url-repo-anda>
   cd productivity
   ```

2. **Install Dependensi PHP & JS**:
   ```bash
   composer install
   npm install
   ```

3. **Duplikat & Konfigurasi File Lingkungan (.env)**:
   ```bash
   cp .env.example .env
   ```
   *Atur konfigurasi database PostgreSQL/Supabase Anda pada baris `DB_*` di dalam file `.env`.*

4. **Konfigurasi API Key Cuaca (Opsional)**:
   Daftar akun gratis di [openweathermap.org](https://openweathermap.org/api) dan tambahkan API Key ke file `.env`:
   ```env
   OPENWEATHERMAP_API_KEY=your_api_key_here
   ```
   *Catatan: Jika dikosongkan, widget cuaca tetap berjalan menggunakan data fallback yang disinkronkan ke lokasi GPS Anda.*

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi Database**:
   ```bash
   php artisan migrate
   ```

7. **Jalankan Development Server**:
   Jalankan server PHP Laravel Artisan:
   ```bash
   php artisan serve
   ```
   Jalankan Vite Bundler (untuk CSS Tailwind):
   ```bash
   npm run dev
   ```

8. Buka browser dan akses halaman dashboard di:
   [http://localhost:8000](http://localhost:8000)

---

## 📁 Struktur File Penting

- **Controller & API**:
  - [TaskController.php](file:///c:/laragon/www/productivity/app/Http/Controllers/TaskController.php) — Manajemen CRUD tugas.
  - [FocusSessionController.php](file:///c:/laragon/www/productivity/app/Http/Controllers/FocusSessionController.php) — Pencatatan sesi fokus pomodoro.
  - [GamificationController.php](file:///c:/laragon/www/productivity/app/Http/Controllers/GamificationController.php) — Perhitungan XP, level, dan streak.
  - [WeatherController.php](file:///c:/laragon/www/productivity/app/Http/Controllers/WeatherController.php) — Proxy cuaca OpenWeatherMap & reverse geocoding Nominatim.
  - [PrayerController.php](file:///c:/laragon/www/productivity/app/Http/Controllers/PrayerController.php) — Proxy jadwal sholat Kemenag RI.
  - [QuoteController.php](file:///c:/laragon/www/productivity/app/Http/Controllers/QuoteController.php) — Caching batch quotes ZenQuotes.
- **Frontend & Views**:
  - [dashboard.blade.php](file:///c:/laragon/www/productivity/resources/views/dashboard.blade.php) — Layout utama dashboard dan logika frontend AlpineJS.
  - [app.css](file:///c:/laragon/www/productivity/resources/css/app.css) — Custom styling design system, glassmorphism, dan dark mode.

---

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT License](LICENSE).
