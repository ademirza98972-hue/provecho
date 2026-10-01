# PRD — NovaTap
**Versi:** 1.1  
**Tanggal:** 30 September 2026  
**Author:** NovaTap  
**Status:** Draft

---

## 1. Overview

NovaTap adalah sistem fisik + digital yang memudahkan pelanggan toko memberikan ulasan Google hanya dengan mengetuk HP ke plakat (NFC) atau scan QR code. Operator mengelola semua card melalui dashboard admin berbasis web.

Produk fisik berupa akrilik polos 10x10cm dengan stiker vinyl glossy waterproof di depan dan NFC sticker NTAG213 di belakang. Tidak ada elemen teknis yang terlihat di produk fisik — semua terlihat seperti display Google Review biasa.

---

## 2. Latar Belakang & Masalah

Banyak UMKM lokal ingin mendapatkan ulasan Google tapi prosesnya terlalu ribet bagi pelanggan (buka Maps, cari toko, klik review, dll). Produk sejenis sudah ada di pasaran (Rp 44.000-an di Shopee) tapi kelemahannya:

- Link langsung di-hardcode ke QR/NFC — tidak bisa diubah kalau link berubah
- Tidak ada sistem manajemen — penjual harus setup sendiri
- Tidak ada fleksibilitas untuk update atau nonaktifkan

NovaTap hadir dengan pendekatan berbeda: QR dan NFC mengarah ke server milik operator, server yang memutuskan redirect ke mana berdasarkan status card di database. Link bisa diupdate kapanpun tanpa ganti fisik card.

---

## 3. Target Pengguna

**Operator (Admin):** Pemilik bisnis NovaTap — yang memproduksi card, menjual ke toko, dan mengelola aktivasi lewat dashboard.

**End User:** Pelanggan toko yang menggunakan card untuk memberi ulasan. Tidak perlu install apapun, cukup tap atau scan.

**Merchant:** Pemilik toko (warung, kafe, barbershop, dll) yang membeli card dari operator. Tidak perlu akses sistem, cukup taruh card di meja kasir.

---

## 4. Alur Sistem

### 4.1 Alur Produksi Card

```
1. Operator generate batch ID di dashboard (misal 100 card)
   → Sistem buat NT0001 - NT0100 di database, status: inactive

2. Dashboard export PDF siap cetak
   → Layout: 4 card per A4 dengan crop mark/garis potong
   → QR masing-masing card sudah masuk di desain

3. Print PDF ke kertas stiker vinyl glossy waterproof A4
   → Pakai printer Canon G2010 (inkjet)

4. Potong stiker per card ukuran 10x10 → tempel ke akrilik polos

5. Sebelum ditempel ke akrilik, write URL ke NFC sticker satu per satu
   → Pakai aplikasi NFC Tools (Android)
   → NT0001: write "https://novatap.id/c/NT0001"
   → NT0002: write "https://novatap.id/c/NT0002"
   → dst.

6. Tempel NFC sticker di belakang akrilik
7. Pasang double tape di belakang
8. Card siap dijual
```

### 4.2 Alur Aktivasi (saat deal dengan merchant)

```
1. Operator buka dashboard di HP → halaman list card

2. Klik "Scan & Aktifkan Card" → scan QR yang ada di card
   → Sistem baca ID dari QR (misal NT0047)

3. Ketik nama toko di kolom pencarian
   → Google Places API munculkan suggestion (nama + alamat)
   → Pilih toko yang sesuai

4. Sistem otomatis generate link Google Review dari place_id
   → https://search.google.com/local/writereview?placeid=XXXXX

5. Klik "Aktifkan" → database update:
   status: active, google_url: link review, owner_name, activated_at

6. Card langsung bisa dipakai — tidak ada yang perlu diubah di fisik card
```

### 4.3 Alur End User (pelanggan toko)

```
Pelanggan tap NFC atau scan QR
            ↓
  Browser buka novatap.id/c/NT0047
            ↓
    Server cek database NT0047
         ↙           ↘
    Inactive          Active
       ↓                 ↓
  Halaman          Redirect 301 ke
"Belum aktif"   halaman tulis ulasan Google
                (langsung muncul form bintang)
```

> NFC chip tidak pernah diubah setelah di-write pertama kali. Semua perubahan (update link, nonaktifkan) dilakukan di server, bukan di chip fisik.

---

## 5. Spesifikasi Produk Fisik

| Komponen | Detail |
|---|---|
| Material | Akrilik polos 10x10cm, tebal 2mm |
| Desain depan | Stiker vinyl glossy waterproof, print sendiri (Canon G2010) |
| NFC | Sticker NTAG213 — di-write sebelum ditempel |
| Perekat | Double tape di belakang untuk tempel di meja |

### Estimasi Modal per Unit
| Komponen | Harga |
|---|---|
| Akrilik 10x10 | Rp 3.000 |
| NFC sticker NTAG213 | Rp 1.500 |
| Stiker vinyl A4 (1 lembar = 4 card) | ~Rp 1.000 |
| Double tape | Rp 200 |
| **Total** | **~Rp 6.000 – 7.000** |

---

## 6. Fitur Sistem

### 6.1 Redirect Service (Core — Fase 1)
- `GET /c/{id}` — endpoint publik, tidak butuh login
- Cek status card di database
- Jika active: redirect 301 ke `google_url` (langsung buka form review Google)
- Jika inactive/disabled: tampil halaman sederhana "Kartu belum aktif"
- Response harus cepat (< 300ms)

### 6.2 Dashboard Admin (Fase 1)

**Halaman Utama:**
- Statistik: total card, aktif, belum aktif, dinonaktifkan
- List 5 card terbaru yang diaktifkan

**Manajemen Card:**
- List semua card dengan filter status dan search (ID / nama merchant)
- Detail card: info toko, link review, log aktivitas
- Aktivasi card: scan QR → cari nama toko via Google Places → aktifkan
- Fallback aktivasi: input link Google Review manual
- Update link Google Review card yang sudah aktif
- Nonaktifkan card

**Generate Batch (Fase 2):**
- Input jumlah card → sistem generate ID sequential (NT0001, NT0002, dst)
- Export PDF siap cetak — desain card + QR sudah include
- Layout 4 card per A4 dengan garis potong

### 6.3 Google Places Integration (Fase 1)
- Search nama toko → return list suggestion (nama + alamat)
- Pilih toko → otomatis ambil `place_id`
- Generate link review: `https://search.google.com/local/writereview?placeid={place_id}`
- Fallback: input manual link kalau toko belum ada di Google Maps

---

## 7. Struktur Database

### Tabel `cards`
```sql
id            VARCHAR(20) PRIMARY KEY  -- NT0001
status        ENUM('inactive','active','disabled') DEFAULT 'inactive'
google_url    TEXT NULL
place_id      VARCHAR(255) NULL
owner_name    VARCHAR(255) NULL
owner_address TEXT NULL
activated_at  TIMESTAMP NULL
disabled_at   TIMESTAMP NULL
notes         TEXT NULL
created_at    TIMESTAMP
updated_at    TIMESTAMP
```

### Tabel `card_logs`
```sql
id          BIGINT AUTO_INCREMENT PRIMARY KEY
card_id     VARCHAR(20)
action      ENUM('scan','activated','updated','disabled')
ip_address  VARCHAR(45) NULL
user_agent  TEXT NULL
created_at  TIMESTAMP
```

---

## 8. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13 (PHP 8.3+) |
| Database | MySQL 8 |
| Frontend Dashboard | Blade + Alpine.js |
| QR Scanner (browser) | html5-qrcode library |
| QR Generator | simplesoftwareio/simple-qrcode |
| PDF Generator | barryvdh/laravel-dompdf |
| Google Places | Google Places API (Text Search) |
| Hosting | VPS (Niagahoster / IDCloudHost) |
| Domain | novatap.id |

---

## 9. Struktur Route

```
GET  /c/{id}                        → Redirect publik (NFC/QR)

GET  /login                         → Form login
POST /login                         → Proses login
POST /logout                        → Logout

GET  /dashboard                     → Halaman utama + statistik
GET  /dashboard/cards               → List semua card
GET  /dashboard/cards/{id}          → Detail card
POST /dashboard/cards/{id}/activate → Aktivasi card
PUT  /dashboard/cards/{id}/update   → Update link
POST /dashboard/cards/{id}/disable  → Nonaktifkan card
POST /dashboard/cards/generate      → Generate batch ID
GET  /dashboard/cards/export/pdf    → Export PDF siap cetak
GET  /dashboard/places/search       → Search Google Places
```

---

## 10. Harga & Proyeksi

### Harga Jual
| Tier | Harga |
|---|---|
| Standar | Rp 75.000 |
| Premium (bantu setup Google Business) | Rp 100.000 |

### Proyeksi (50 unit/bulan)
| Item | Nominal |
|---|---|
| Omzet (@ Rp 75.000) | Rp 3.750.000 |
| Modal produksi (50 × Rp 7.000) | Rp 350.000 |
| Infrastruktur (domain + VPS / 12) | ~Rp 50.000 |
| **Profit bersih** | **~Rp 3.350.000** |

---

## 11. Infrastruktur

- **Domain:** novatap.id
- **Hosting:** VPS minimal 1 core, 1GB RAM
- **SSL:** Wajib HTTPS — HP tidak mau buka redirect dari HTTP
- **Environment variables:**
```
APP_URL=https://novatap.id
DB_CONNECTION=mysql
GOOGLE_PLACES_API_KEY=
```

---

## 12. Prioritas Pengembangan

| Fase | Fitur | Status |
|---|---|---|
| **Fase 1** | Redirect service + halaman inactive | Prioritas utama |
| **Fase 1** | Database + migration | Prioritas utama |
| **Fase 1** | Auth admin (login/logout) | Prioritas utama |
| **Fase 1** | Dashboard + list card | Prioritas utama |
| **Fase 1** | Aktivasi card + Google Places search | Prioritas utama |
| **Fase 2** | Generate batch ID | Penting |
| **Fase 2** | Export PDF siap cetak | Penting |
| **Fase 3** | Update / nonaktifkan card | Nice to have |
| **Fase 3** | Analytics jumlah scan per card | Nice to have |

---

## 13. Out of Scope (v1)

- Multi-user / tim operator
- Notifikasi WhatsApp ke merchant
- Model berlangganan bulanan
- White-label untuk reseller
- Support NFC selain NTAG213