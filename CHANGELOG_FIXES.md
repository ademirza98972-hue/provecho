### Fix #6 — Simpan Data Usaha Menghapus Alamat & Place ID: Hidden Input Selalu Kosong

| | |
|---|---|
| **Tanggal** | 2026-10-04 |
| **File** | `resources/views/dashboard/cards/show.blade.php` |
| **Masalah** | Di detail card aktif, menekan "Simpan Perubahan" tanpa memilih ulang usaha dari pencarian Google/link Maps (mis. hanya mengedit nama) mengosongkan `owner_address` dan `place_id` di database. |
| **Akar** | Komponen Alpine `placesSearch()` hanya menerima nama & link awal; `address` dan `placeId` selalu diinisialisasi `''`. Hidden input `owner_address`/`place_id` (x-model) ikut terkirim kosong, dan `CardController::update()` menyimpan apa adanya (string kosong → null). |
| **Fix** | `placesSearch(initName, initUrl, initAddress, initPlaceId)` — hidden input diisi dari data card yang tersimpan. Alamat kini juga tampil di bawah field nama. |
| **Verifikasi** | Render `/dashboard/cards/PV2529C6` sebagai admin: sebelum fix hidden `owner_address` = `""`; sesudah fix = `"Jl. A. Yani No.KM. 36.8, Komet…"` dan `place_id` = `"ChIJpyTe_Q…"`. Submit form kini mengirim nilai lama bila tidak ada pilihan baru. |
| **Pelajaran** | Form edit dengan hidden input yang di-bind x-model wajib diinisialisasi dari nilai tersimpan, bukan string kosong — kalau tidak, setiap simpan diam-diam menimpa data. |
| **Log Keyword** | `owner_address`, `place_id`, `placesSearch`, `x-model`, alamat hilang |
| **Deploy** | Tidak ada migrasi. Card aktif yang alamatnya sudah terlanjur kosong perlu dipilih ulang dari pencarian Google sekali. |

### Fix #5 — Card Setelah Reset Tetap Buka Maps Lama: Client-Side Redirect Ter-cache Browser

| | |
|---|---|
| **Tanggal** | 2026-10-01 |
| **File** | `app/Http/Controllers/CardRedirectController.php` |
| **Masalah** | Card PV2529C6 sudah direset dan diaktifkan ulang dengan Google Maps baru, tapi saat scan tetap membuka link Maps yang lama. |
| **Akar** | Fix #4 mengganti 301 ke halaman HTML perantara (`go.blade.php`) yang melakukan redirect via `<meta http-equiv="refresh">` + `window.location.replace()`. Halaman ini berstatus **HTTP 200** — browser/WebView di HP meng-cache body HTML-nya (yang berisi URL lama) lebih agresif daripada response 3xx. `Cache-Control: no-store` saja pada response 200 tidak cukup: beberapa mobile browser, terutama WebView yang dipanggil dari NFC tap, mengabaikan header ini untuk navigasi utama. Hasilnya, scan berikutnya langsung menampilkan halaman HTML tersimpan tanpa bertanya ke server. |
| **Fix** | Ganti client-side redirect (view `go.blade.php` HTTP 200) ke proper **HTTP 302 server-side redirect** langsung ke `google_url`. Header cache diperkuat: `no-store, no-cache, must-revalidate, private` + `Pragma: no-cache`. Browser tidak menyimpan 302 + no-store, jadi setiap scan pasti bertanya ke server dan mendapat URL terbaru. |
| **Verifikasi** | `curl -sI /c/PV2529C6` → `302 Found`, `Location: <url baru>`, `Cache-Control: no-store, no-cache, must-revalidate, private`, `Pragma: no-cache`. Setelah reset + aktivasi ulang, scan langsung membuka URL baru tanpa perlu clear cache browser. |
| **Pelajaran** | HTTP 200 dengan client-side redirect (`meta refresh` / `window.location.replace`) bukan pengganti yang setara dengan HTTP 302 untuk redirect. Secara caching: 200 adalah response "berhasil" yang browser boleh cache body-nya; 302 adalah instruksi "cari di sana" yang browser tahu tidak boleh disimpan (apalagi dengan `no-store`). Untuk URL yang bisa berubah kapan saja (card bisa direset), server-side 302 adalah satu-satunya pilihan yang benar. |
| **Log Keyword** | `302`, `no-store`, `no-cache`, `must-revalidate`, `Pragma`, `go.blade.php` |
| **Deploy** | Tidak ada migrasi. Langsung efektif setelah deploy. User yang browser-nya sudah meng-cache halaman lama mungkin perlu satu kali clear cache atau buka di incognito — setelah itu cache tidak lagi jadi masalah. |

### Fix #4 — 301 Redirect Bikin Nonaktifkan Tidak Mempan + Card Disabled Jalan Buntu

| | |
|---|---|
| **Tanggal** | 2026-10-01 |
| **File** | `CardRedirectController.php`, `CardController.php`, `show.blade.php`, `web.php`, migration enum |
| **Masalah** | (1) Card aktif pakai 301 permanent redirect — browser menyimpan tujuan selamanya, sehingga nonaktifkan atau ganti link tidak berlaku untuk pengguna yang pernah scan. (2) Card disabled tidak bisa diaktifkan kembali atau direset — jalan buntu tanpa aksi lanjutan. (3) Kolom `action` di card_logs adalah enum tanpa nilai `reactivated`/`reset`. |
| **Akar** | 301 dipilih untuk performa tapi mengorbankan kendali. UI disabled tidak punya tombol aksi. Enum terlalu ketat. |
| **Fix** | (1) Ganti 301→302 + `Cache-Control: no-store`. (2) Tambah `reactivate()` (aktifkan kembali, data utuh) dan `reset()` (hapus data, kembali ke inactive) di CardController + route + UI. (3) Migration perluas enum. |
| **Verifikasi** | `curl -sI` card aktif → `302 Found`, `Cache-Control: no-store, private`. Reactivate: disabled→active, data toko tetap, log "reactivated". Reset: disabled→inactive, data null, form kembali ke "Aktifkan Card", log "reset". |
| **Pelajaran** | 301 untuk resource yang bisa berubah (card bisa dinonaktifkan/ganti link) adalah kesalahan desain. 302 lebih aman — biaya per-request negligible dibanding kehilangan kendali. Enum di log harus diantisipasi untuk action baru. |
| **Log Keyword** | `301`, `302`, `no-store`, `reactivate`, `reset`, `enum`, `Data truncated` |
| **Deploy** | Perlu `php artisan migrate` untuk enum baru |

### Fix #3 — Link Share dari HP Ditolak: Feature ID Ternyata Punya Dua Bentuk

| | |
|---|---|
| **Tanggal** | 2026-10-01 |
| **File** | `app/Support/GoogleMapsLink.php`, `tests/Unit/GoogleMapsLinkTest.php`, `app/Http/Controllers/Dashboard/PlacesController.php` |
| **Masalah** | Semua link hasil Share dari aplikasi Google Maps di HP ditolak dengan pesan "Ini sepertinya link hasil pencarian", padahal link-nya benar. Fix #2 hanya pernah diuji dengan URL panjang dari browser. |
| **Akar** | Dua hal. (1) Link pendek `maps.app.goo.gl` tidak melempar ke `/maps/place/...!1s0x..:0x..` seperti diasumsikan, tapi ke `maps.google.com?q=<nama,alamat>&ftid=0x..:0x..` — feature ID-nya ada, tapi sebagai parameter `ftid`, dan regex hanya mengenal bentuk `!1s`. (2) `expand()` berhenti saat menemukan `/maps/place/`, patokan yang tidak relevan dengan tujuan sebenarnya. |
| **Fix** | Pola FID jadi `(?:!1s\|ftid=)0x..(?::\|%3A)0x..`, mengenali kedua bentuk plus pemisah ter-URL-encode. `expand()` berhenti saat **feature ID sudah didapat**, bukan saat bentuk URL tertentu muncul. Ditambah: hanya host shortener (`maps.app.goo.gl`, `goo.gl`) yang ditembak ke jaringan — sebelumnya URL Maps panjang tanpa FID ikut di-fetch sia-sia. Nama toko juga kini diambil dari parameter `q`, yang sekaligus memberi alamat lengkap — jalur link jadi setara jalur API, tidak lagi `address: null`. |
| **Verifikasi** | 8 unit test lolos. Link asli user `maps.app.goo.gl/rCzQaKWanLwNeae99` → `ChIJuXCYMGOB5i0RNpU2zABkvHM`, **identik** dengan `place_id` yang dikembalikan Places API untuk "Gemilang Pusat Bahan Bangunan Banjarbaru" — dicek dengan memanggil API-nya langsung sebagai pembanding. Nama dan alamat lengkap ikut terisi. URL panjang dari browser tetap jalan (tanpa panggilan jaringan sama sekali). |
| **Pelajaran** | Fix #2 "terverifikasi" padahal cuma diuji dengan satu bentuk input yang saya karang sendiri. Bentuk link yang benar-benar dipakai orang (Share dari HP) berbeda, dan baru ketahuan setelah user mencoba. **Untuk fitur yang memproses input dari dunia luar, satu contoh asli dari user lebih berharga daripada sepuluh contoh buatan sendiri** — minta contoh nyata sebelum menyatakan selesai, bukan sesudah. |
| **Log Keyword** | `FID_PATTERN`, `ftid`, `isShortener`, `GoogleMapsLink::expand` |
| **Deploy** | Tidak ada migrasi. Melanjutkan Fix #2. |

### Fix #2 — Konverter Link Maps: Jalur Cadangan Saat Kuota Places Habis

| | |
|---|---|
| **Tanggal** | 2026-10-01 |
| **File** | `app/Support/GoogleMapsLink.php`, `tests/Unit/GoogleMapsLinkTest.php`, `app/Http/Controllers/Dashboard/PlacesController.php`, `app/Http/Controllers/CardRedirectController.php`, `routes/web.php`, `resources/views/card/activate.blade.php`, `resources/views/dashboard/cards/show.blade.php` |
| **Masalah** | Aktivasi 100% bergantung Places API. Kalau kuota habis sistem mati total — dan lebih buruk: kegagalan API ditampilkan sebagai "Toko tidak ditemukan", sehingga pemilik toko mengira usahanya tidak terdaftar di Google lalu menyerah, bukan menghubungi operator. |
| **Akar** | `PlacesController::search()` mengembalikan `['results' => []]` dengan HTTP 200 untuk semua kegagalan, jadi frontend tidak bisa membedakan "tidak ada hasil" dari "API mati". |
| **Fix** | Kegagalan API kini balas HTTP 502 + `report()`, frontend menampilkan pesan berbeda per sebab. Ditambah jalur cadangan tanpa API: tempel link Google Maps → `GoogleMapsLink::resolve()` mengubahnya jadi link tulis-ulasan. Place ID (`ChIJ…`) ternyata hanya base64url dari protobuf berisi feature ID yang sudah tercantum di URL Maps sebagai `!1s0x<hi>:0x<lo>` — jadi tidak perlu tanya Google. Nama toko diambil dari slug `/maps/place/<nama>/`. Link pendek `maps.app.goo.gl` diikuti redirect-nya (maks 3 lompatan), **setiap lompatan divalidasi host-nya** supaya tidak jadi celah SSRF. Tersedia di halaman aktivasi publik dan dashboard. |
| **Verifikasi** | 6 unit test lolos. Konversi dicocokkan dengan data nyata: FID `0x2de681661c28d9f9:0xa8739c05c8f6a405` → `ChIJ-dkoHGaB5i0RBaT2yAWcc6g`, identik dengan `place_id` hasil Places API untuk toko yang sama. Keempat `place_id` tersimpan berstruktur sama (20 byte, `0a1209…11`). Aktivasi lewat jalur link menghasilkan baris DB identik dengan jalur API. Error: link pencarian / bukan Google / `169.254.169.254` (uji SSRF) / teks sembarangan → semua 422 dengan pesan spesifik. |
| **Pelajaran** | `hexdec()` diam-diam mengubah nilai > `PHP_INT_MAX` jadi float dan presisinya hilang — percobaan pertama menghasilkan Place ID yang **mirip tapi salah** (`…RAKD2yAWcc6g` vs `…RBaT2yAWcc6g`), lolos tanpa error apa pun. Hex 64-bit harus diolah sebagai byte (`hex2bin` + `strrev`), bukan sebagai angka. Ini sebab umum kenapa konversi FID→PlaceID dilaporkan "tidak jalan" oleh orang lain. |
| **Log Keyword** | `GoogleMapsLink`, `resolveMaps`, `card.resolve-maps`, `Places API gagal` |
| **Deploy** | Tidak ada migrasi. Jalur cadangan tidak memakai kuota Google sama sekali. Disarankan pasang batas kuota harian di Google Cloud Console. |

### Fix #1 — Aktivasi Mandiri: Kartu Belum Aktif Jadi Bisa Diaktifkan Toko Sendiri

| | |
|---|---|
| **Tanggal** | 2026-10-01 |
| **File** | `app/Models/Card.php`, `app/Http/Controllers/CardRedirectController.php`, `app/Http/Controllers/Dashboard/CardController.php`, `routes/web.php`, `resources/views/card/activate.blade.php`, `resources/views/card/inactive.blade.php` |
| **Masalah** | Scan kartu belum aktif berujung halaman buntu. Operator harus aktivasi manual satu per satu. |
| **Akar** | ID kartu dibuat berurutan (`PV0001`, `PV0002`, …). Membuka aktivasi publik dengan ID seperti itu berarti siapa pun bisa menebak seluruh ID lain dan membajak semua kartu yang belum aktif sekaligus. |
| **Fix** | ID diganti acak: `PV` + 6 karakter dari alfabet 32 huruf tanpa `0/O/1/I` (1.073.741.824 kombinasi, `random_int`). ID jadi rahasia yang hanya diketahui pemegang kartu fisik, sehingga aktivasi publik aman tanpa token terpisah. Scan kartu `inactive` kini membuka form aktivasi mandiri; `disabled` tetap buntu (sengaja dimatikan operator). Endpoint Places publik diikat ke kartu valid + belum aktif, `throttle:20,1`, supaya kuota Google tidak bisa dikuras. `google_url` dibatasi skema `http,https` agar tidak bisa diisi `javascript:`. |
| **Verifikasi** | Alur HP 375px: cari toko → pilih → aktifkan → 301 ke writereview URL. DB: status `active`, `place_id` terisi, log `scan -> activated -> scan`. Penjagaan: Places 200 untuk kartu belum aktif, 404 untuk aktif/nonaktif/tidak ada. Scan kartu tidak dikenal → 404. 14 kartu lama yang masih berurutan diacak ulang, 0 orphan log. |
| **Pelajaran** | ID berurutan aman selama aktivasi tertutup di balik login. Begitu aktivasi dibuka ke publik, ID berubah peran jadi kredensial — dan kredensial tidak boleh bisa dihitung. |
| **Opsi yang tidak diambil** | Token rahasia kedua per kartu (dicetak di balik lapisan gosok, hangus sekali pakai). Ditolak user 2026-10-01 karena menambah beban produksi kartu. **Perlindungannya tidak setara dengan ID acak:** ID acak hanya menutup serangan tebak-ID dari jauh, sedangkan token juga menutup serangan dari orang yang sempat melihat/memotret kartu sebelum sampai ke toko — sebab ID acak tercetak terbuka, token tidak. Risiko sisa yang diterima: satu kartu bisa dibajak oleh orang yang memegangnya secara fisik; terlihat di dashboard dan bisa diperbaiki operator. Angkat lagi opsi token kalau nanti kartu dikirim lewat rantai distribusi panjang atau volume sudah besar. |
| **Log Keyword** | `card.activate`, `card.places`, `Card::newId`, `findActivatable` |
| **Deploy** | Tidak ada migrasi. Kartu lama yang sudah aktif tetap memakai ID berurutan — biarkan, ID-nya mungkin sudah tercetak. |
