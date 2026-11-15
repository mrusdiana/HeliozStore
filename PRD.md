# PRD: Game Top-Up Store (E-Commerce Website)

## Context

Task 4 (Menengah) program magang Auspify: platform e-commerce full-stack untuk menelusuri dan mengelola produk.
Produk yang dijual: **paket item digital game** (misalnya diamond, UC, Robux) untuk banyak game, dengan toko berperan sebagai reseller.

**Stack yang disetujui Auspify:** Laravel (PHP), MySQL, Vue.js, Docker.
**Cara kerja:** pemilik project menulis kode sendiri dengan panduan Claude; Claude tidak menyentuh GitHub (commit, push, merge).

**Syarat Auspify untuk Task 4:**
- Alur: (1) desain halaman daftar produk, (2) API manajemen produk, (3) keranjang belanja, (4) kelola pesanan dan akun pengguna, (5) uji alur end-to-end.
- Fitur utama: katalog produk, keranjang belanja, akun pengguna, manajemen pesanan.

**Keputusan (14 Nov 2026):**

| Topik | Keputusan |
|---|---|
| Jenis toko | Satu toko, satu penjual (reseller item game) |
| Game | Banyak game dengan nama asli (misalnya Mobile Legends, PUBG Mobile, Roblox, Free Fire, eFootball) dan gambar yang diunggah admin |
| Produk | Paket item per game, dengan harga dan stok |
| Data akun game | Berbeda per game (misalnya User ID + Zone ID, Player ID, atau Username) |
| Validasi akun | MVP: validasi **format** saja. Cek akun sungguhan masuk v2 |
| Pembayaran | Midtrans Snap (sandbox); status dicek backend lewat API Midtrans, tanpa webhook |
| Status pesanan | Unpaid → Paid → Completed (otomatis 5–10 detik setelah dibayar), atau Cancelled |
| Ongkir dan alamat | Tidak ada (produk digital) |
| Fitur tambahan MVP | Panel admin, cari & filter, stok, upload gambar |
| Beranda | Banner promo selayar yang berganti otomatis tiap 5 detik → bagian "On promo" (paket promo dari game mana pun) → kartu game (gambar + nama) |
| Promo | Diatur manual oleh admin dan statis (tanpa jadwal otomatis). Paket promo memakai harga coret: harga normal dicoret, harga jual ditampilkan |
| Panel admin | Satu aplikasi: akun berperan admin membuka halaman admin di web yang sama (pesanan, game, paket, banner) |
| Cara membeli | **Buy now** (utama, satu item langsung ke pembayaran) dan **Add to cart** (sekunder, beberapa item sekali bayar) |
| Mata uang & bahasa | IDR; antarmuka bahasa Inggris |
| Desain | Gelap, futuristik modern, bernuansa gaming |
| Definisi selesai | Berjalan lokal dengan Docker Compose + README, screenshot, dan tes. Tanpa deploy |
| Tenggat | 1 minggu |

**Catatan aset:** nama dan gambar game adalah milik pengembangnya masing-masing. Footer aplikasi dan README memuat keterangan:
*"Demo project for learning purposes. Not affiliated with any game publisher."*

---

## 1. Problem statement

Pemain game sering membeli item (diamond, UC, Robux) lewat reseller karena harga lebih murah atau metode bayar lebih beragam dibanding toko resmi. Masalahnya:

- Reseller kecil menerima pesanan lewat chat: pembeli harus menunggu balasan untuk harga, stok, dan konfirmasi.
- Data akun game (User ID, Zone ID) sering salah ketik atau tidak lengkap, sehingga top-up gagal atau salah kirim.
- Pembeli tidak tahu status pesanannya: sudah dibayar? sudah dikirim?
- Reseller kesulitan mencatat stok paket dan pesanan dari banyak game sekaligus.

Yang dirugikan adalah **pembeli** (menunggu dan berisiko salah kirim) dan **reseller** (pesanan berantakan dan stok tidak sinkron).

## 2. Target user dan persona

| Peran | Siapa | Akses |
|---|---|---|
| **Pembeli** | Pemain game yang mendaftar akun | Katalog game, paket, keranjang, checkout, riwayat pesanan |
| **Admin** | Pemilik toko | Panel admin: game, paket, stok, pesanan |

Persona berikut adalah ilustrasi, bukan hasil riset pengguna.

**Persona 1: Raka, 19 tahun, pemain Mobile Legends dan Free Fire**
- Top-up beberapa kali sebulan dari HP.
- Masalah: harus chat penjual dan menunggu; pernah salah memasukkan Zone ID.
- Butuh: memilih paket, mengisi ID dengan panduan yang jelas, bayar, dan item cepat masuk.

**Persona 2: Dina, 26 tahun, pemilik toko top-up**
- Menjual paket untuk banyak game.
- Masalah: stok dan pesanan tercatat manual.
- Butuh: mengelola game dan paket, melihat pesanan yang sudah dibayar, dan memantau status.

## 3. Goals dan non-goals

**Goals**
1. Pembeli melihat banner dan paket yang sedang promo di beranda, dapat menelusuri, mencari, dan memfilter game, lalu memilih paket.
2. Pembeli mengisi data akun game yang sesuai dengan game yang dipilih, dengan validasi format.
3. Pembeli dapat langsung membeli satu paket ("Buy now"), atau mengumpulkan beberapa paket di keranjang (bisa dari beberapa game atau untuk beberapa akun) lalu checkout sekali bayar.
4. Pembeli membayar lewat Midtrans (sandbox); status pesanan berubah otomatis.
5. Pesanan yang sudah dibayar otomatis menjadi Completed dalam 5–10 detik (simulasi pengiriman item).
6. Stok paket selalu konsisten dengan pesanan.
7. Admin dapat mengelola game (gambar dan field akun), paket (termasuk promo dan foto), stok, banner beranda, dan pesanan dari web yang sama.
8. Aplikasi bisa dijalankan dengan Docker Compose.

**Non-goals**
- Pengecekan akun game sungguhan dan pengiriman item sungguhan (butuh API supplier; masuk v2).
- Pembayaran sungguhan (hanya sandbox).
- Banyak penjual (marketplace).
- Webhook notifikasi Midtrans.
- Voucher/kode redeem, promo, dan program poin.
- Aplikasi mobile native dan deploy.

## 4. User stories

**Pembeli**
- **Akun:** Sebagai pembeli, saya ingin mendaftar, masuk, dan keluar agar keranjang dan pesanan saya tersimpan.
- **Promo:** Sebagai pembeli, saya ingin langsung melihat promo yang sedang berlaku di beranda agar bisa membeli dengan harga lebih murah.
- **Katalog game:** Sebagai pembeli, saya ingin melihat daftar game beserta gambarnya agar bisa memilih game yang saya mainkan.
- **Cari & filter:** Sebagai pembeli, saya ingin mencari game berdasarkan nama dan memfilter berdasarkan genre.
- **Paket:** Sebagai pembeli, saya ingin melihat paket item sebuah game beserta harga dan stoknya.
- **Data akun:** Sebagai pembeli, saya ingin mengisi data akun game (misalnya User ID dan Zone ID) dengan petunjuk yang jelas agar item masuk ke akun yang benar.
- **Beli langsung:** Sebagai pembeli, saya ingin langsung membayar satu paket tanpa melewati keranjang, karena biasanya saya top-up satu kali saat butuh.
- **Keranjang:** Sebagai pembeli, saya ingin mengumpulkan beberapa paket (misalnya untuk teman satu squad atau beberapa game) lalu membayar sekali.
- **Bayar:** Sebagai pembeli, saya ingin membayar lewat Midtrans dan melihat status pembayaran.
- **Riwayat:** Sebagai pembeli, saya ingin melihat pesanan saya beserta statusnya.

**Admin**
- **Game:** Sebagai admin, saya ingin menambah dan mengubah game: nama, genre, gambar, nama mata uang game, dan field akun yang dibutuhkan.
- **Paket:** Sebagai admin, saya ingin mengelola paket per game: nama, foto, jumlah item, harga, harga normal (untuk promo), stok, dan status aktif.
- **Banner:** Sebagai admin, saya ingin mengatur banner promo di beranda: gambar, judul, subjudul, game tujuan, urutan, dan status aktif.
- **Pesanan:** Sebagai admin, saya ingin melihat semua pesanan beserta data akun game pembeli dan statusnya.

## 5. Daftar fitur

| Fase | Fitur |
|---|---|
| **MVP** | Autentikasi dengan peran; beranda dengan banner slide otomatis dan bagian promo; katalog game; cari game dan filter genre; halaman game dengan daftar paket; form data akun dinamis per game dengan validasi format; Buy now; keranjang multi-game; checkout; pembayaran Midtrans Snap sandbox; penyelesaian pesanan otomatis; riwayat pesanan; panel admin dalam web yang sama (game + upload gambar + field akun, paket + foto + promo + stok, banner, pesanan); Docker Compose |
| **v2** | Cek nickname akun sebelum bayar dan pengiriman item otomatis lewat API supplier; webhook Midtrans; promo terjadwal (mulai/selesai otomatis) dan flash sale; dashboard penjualan |
| **Nanti** | Voucher; poin loyalitas; ulasan; deploy |

## 6. Functional requirements MVP

Semua API memakai JSON. Endpoint pembeli dan admin mewajibkan autentikasi, kecuali katalog game, halaman game, daftar, dan masuk.
Data milik pengguna lain menghasilkan 404. Endpoint admin menolak non-admin dengan 403. Semua UI responsif.

### FR-1 Autentikasi dan peran
1. Daftar dengan nama, email unik, dan password (minimum: OQ-6). Akun baru selalu berperan **pembeli**.
2. Masuk dan keluar dengan token; password di-hash; percobaan masuk dibatasi.
3. Akun admin tidak bisa dibuat lewat pendaftaran (cara pembuatan: OQ-7).
4. Pesan gagal masuk tidak mengungkap apakah email terdaftar.

### FR-2 Beranda dan katalog game
1. **Banner slide:** banner aktif tampil selebar layar, berurutan sesuai urutan yang diatur admin, berganti otomatis setiap **5 detik**. Setiap banner punya gambar, judul, subjudul (teks promo), dan game tujuan; klik banner membuka halaman game tersebut.
2. Slide berhenti saat disentuh/di-hover atau difokuskan keyboard, punya tombol sebelumnya/berikutnya dan indikator titik, dan tidak berganti otomatis bila pengguna memilih `prefers-reduced-motion`.
3. **Bagian "On promo":** menampilkan paket aktif yang sedang promo dari game mana pun: foto paket, nama game, nama paket, harga normal dicoret, dan harga jual. Klik membuka halaman game dengan paket itu sudah terpilih.
4. **Kartu game:** di bawah bagian promo, menampilkan game aktif hanya dengan gambar dan nama (tanpa harga). Klik membuka halaman game.
5. Game nonaktif tidak tampil dan paketnya tidak bisa dibeli; banner dan promo dari game nonaktif juga tidak tampil.

### FR-3 Cari dan filter
1. Cari game berdasarkan nama (tidak peka huruf besar/kecil) lewat kolom pencarian di header; hasil tampil sebagai kartu game.
2. Filter berdasarkan genre di bagian kartu game (daftar genre: OQ-14).
3. Pencarian dan filter bisa digabung; hasil kosong menampilkan empty state.

### FR-4 Halaman game dan paket
1. Urutan halaman: banner dan nama game → **kartu data akun di paling atas** (dengan petunjuk singkat) → daftar paket aktif, diurutkan dari harga termurah.
2. Setiap paket menampilkan foto, nama (misalnya "86 Diamonds"), harga, harga normal dicoret bila promo, dan status stok; paket habis tidak bisa dipilih.
7. Jika dibuka dari bagian promo, paket tersebut sudah terpilih.
3. **Form data akun dinamis:** field mengikuti pengaturan game (lihat FR-9.3). Contoh: Mobile Legends menampilkan "User ID" dan "Zone ID"; Roblox menampilkan "Username".
4. Validasi format dijalankan di frontend (untuk umpan balik cepat) **dan** di backend (yang menentukan).
5. Dua tombol aktif setelah paket dipilih dan data akun valid: **"Buy now"** (utama) dan **"Add to cart"** (sekunder).
6. "Buy now" membawa pembeli ke halaman review dengan **hanya item itu**; isi keranjang tidak berubah.

### FR-5 Keranjang
1. Satu keranjang per pembeli, tersimpan di server, bisa berisi paket dari beberapa game.
2. Setiap item menyimpan paket, jumlah, dan data akun game.
3. Paket yang sama untuk akun yang sama digabung (jumlah bertambah); paket yang sama untuk akun berbeda menjadi item terpisah.
4. Jumlah minimal 1 dan tidak boleh melebihi stok tersedia saat itu.
5. Subtotal per item dan total dihitung di server dari harga terbaru.
6. Item yang paketnya nonaktif atau stoknya tidak cukup ditandai dan menghalangi checkout.

### FR-6 Checkout dan pesanan
1. Pesanan dibuat dari salah satu sumber: **keranjang** (semua item di keranjang) atau **Buy now** (satu item: paket, jumlah, data akun). Tidak ada alamat atau ongkir. **Total = jumlah subtotal item.**
2. Server memeriksa ulang stok, harga, dan format data akun saat checkout.
3. Pesanan menyimpan **salinan** nama game, nama paket, harga, jumlah, dan data akun setiap item.
4. Saat pesanan dibuat: stok dikurangi, status `pending_payment` (label "Unpaid"), dan **keranjang dikosongkan hanya jika pesanan berasal dari keranjang**. Semua dalam satu database transaction.
5. Dua pembeli yang membeli stok terakhir bersamaan tidak boleh sama-sama berhasil.

### FR-7 Pembayaran (Midtrans Snap sandbox)
1. Setelah pesanan dibuat, backend meminta token Snap dan pembeli membayar lewat popup Snap.
2. Status diambil backend dari **API status Midtrans**: saat popup Snap selesai, saat pembeli membuka detail pesanan atau menekan "Check payment status", dan oleh perintah terjadwal untuk pesanan `pending_payment`.
3. Berhasil → `paid`. Kedaluwarsa atau gagal → `cancelled` dan stok dikembalikan.
4. Frontend tidak pernah menentukan status; status hanya diubah berdasarkan jawaban API Midtrans yang dipanggil dari backend.
5. Pengecekan berulang tidak memproses pesanan dua kali (misalnya stok tidak dikembalikan dua kali).
6. Server key Midtrans hanya ada di backend `.env`.

### FR-8 Penyelesaian otomatis (simulasi pengiriman item)
1. Setelah pesanan menjadi `paid`, sistem menjadwalkan penyelesaian **5–10 detik** kemudian; status berubah menjadi `completed`.
2. Dijalankan lewat queue job tertunda. Jika worker queue tidak berjalan, pesanan `paid` yang sudah lewat 10 detik diselesaikan saat pesanannya dibuka (cadangan).
3. Halaman detail pesanan memperbarui status sendiri selama pesanan berstatus `paid`, tanpa pembeli me-refresh halaman.
4. Tidak ada item sungguhan yang dikirim; ini simulasi sampai integrasi supplier di v2.

### FR-9 Panel admin: game, paket, dan banner
0. Panel admin adalah bagian dari web yang sama (route `/admin/...`), hanya bisa dibuka akun berperan admin.
1. CRUD game: nama, slug, genre, gambar (upload), nama mata uang game (misalnya "Diamonds"), petunjuk pengisian ID, field akun, status aktif, dan urutan tampil.
2. Upload gambar: format dan ukuran maksimal dibatasi (OQ-5); gambar lama dihapus saat diganti.
3. **Field akun** per game: daftar field berisi kunci (misalnya `user_id`), label ("User ID"), jenis (`numeric` / `text`), panjang minimal dan maksimal, serta placeholder. Minimal satu field.
4. CRUD paket: game, nama, foto (opsional; tanpa foto memakai gambar game), jumlah item, harga jual (bilangan bulat rupiah > 0), harga normal (opsional; jika diisi harus lebih besar dari harga jual, dan paket dianggap **promo**), stok (bilangan bulat ≥ 0), dan status aktif.
6. CRUD banner: gambar (upload), judul, subjudul, game tujuan, urutan, dan status aktif. Banner dari game nonaktif tidak tampil di beranda.
5. Game yang masih punya paket tidak bisa dihapus; paket yang pernah dipesan tidak dihapus permanen tetapi dinonaktifkan.

### FR-10 Panel admin: pesanan
1. Daftar semua pesanan dengan filter status dan pencarian nomor pesanan.
2. Detail pesanan: data pembeli, item beserta **data akun game**, dan riwayat pembayaran.
3. Pesanan `paid` yang belum selesai bisa ditandai Completed secara manual (cadangan bila penyelesaian otomatis gagal).
4. Pembatalan oleh admin: OQ-9.

### FR-11 Docker
1. `docker compose up` menjalankan backend, worker queue, frontend, dan MySQL tanpa memasang PHP/MySQL di komputer.
2. Data MySQL dan gambar yang diunggah tetap ada setelah container dimatikan.
3. Langkah menjalankan ditulis di README.

## 7. Sketsa data model

| Entitas | Field kunci |
|---|---|
| **users** | id, name, email (unik), password (hash), role (`buyer` / `admin`) |
| **games** | id, name, slug (unik), genre, image_path, currency_name, account_instructions, account_fields (JSON), is_active, sort_order |
| **products** (paket) | id, game_id, name, image_path (nullable), item_amount, price, original_price (nullable; terisi = promo), stock, is_active, timestamps |
| **banners** | id, game_id, title, subtitle, image_path, sort_order, is_active, timestamps |
| **cart_items** | id, user_id, product_id, quantity, account_data (JSON), account_key; unik (user_id, product_id, account_key) |
| **orders** | id, order_number (unik), user_id, status, total, paid_at, completed_at, timestamps |
| **order_items** | id, order_id, product_id, game_name, product_name, price, quantity, subtotal, account_data (JSON) |
| **payments** | id, order_id, provider (`midtrans`), snap_token, provider_reference (unik), status, amount, paid_at, raw_response |

Contoh `account_fields` untuk Mobile Legends:

```json
[
  { "key": "user_id", "label": "User ID", "type": "numeric", "min": 5, "max": 12, "placeholder": "e.g. 123456789" },
  { "key": "zone_id", "label": "Zone ID", "type": "numeric", "min": 4, "max": 5, "placeholder": "e.g. 1234" }
]
```

`account_key` = gabungan nilai data akun (misalnya `123456789|1234`), dipakai untuk menggabungkan item yang sama di keranjang.
Semua nominal disimpan sebagai `DECIMAL`, bukan `FLOAT`.

**Status pesanan:**

```
pending_payment (Unpaid) ──► paid (Paid) ──5–10 detik──► completed (Completed)
        │
        └──► cancelled (Cancelled: kedaluwarsa / gagal bayar; stok dikembalikan)
```

## 8. Edge case dan failure state

| Kasus | Perilaku |
|---|---|
| Data akun tidak sesuai format (huruf di User ID, terlalu pendek/panjang, kosong) | Ditolak dengan pesan per field |
| Data akun yang benar formatnya tetapi akunnya tidak ada | Tidak terdeteksi di MVP (keterbatasan yang diketahui; v2) |
| Jumlah di keranjang melebihi stok | Ditolak dengan pesan berisi stok tersedia |
| Stok berkurang atau paket dinonaktifkan setelah masuk keranjang | Item ditandai; checkout ditolak sampai diperbaiki |
| Harga berubah setelah masuk keranjang | Total memakai harga terbaru saat checkout |
| Dua pembeli membeli stok terakhir bersamaan | Hanya satu yang berhasil |
| Checkout dengan keranjang kosong | Ditolak |
| Buy now saat keranjang berisi item lain | Pesanan hanya berisi item Buy now; isi keranjang tetap |
| Tombol checkout ditekan dua kali | Hanya satu pesanan yang dibuat |
| Pembayaran kedaluwarsa atau gagal | `cancelled`, stok dikembalikan |
| Status pembayaran dicek berkali-kali | Pesanan diproses sekali saja |
| Status yang dikirim frontend tidak sesuai jawaban Midtrans | Diabaikan; backend hanya percaya API Midtrans |
| Midtrans tidak bisa dihubungi | Pesanan tetap `pending_payment`; pembeli bisa mencoba bayar lagi |
| Worker queue mati saat pesanan `paid` | Pesanan diselesaikan saat dibuka (cadangan FR-8.2) atau manual oleh admin |
| Admin mengubah field akun sebuah game | Hanya berlaku untuk keranjang/pesanan baru; pesanan lama tetap memakai salinan datanya |
| Pembeli membuka pesanan milik orang lain | 404 |
| Non-admin membuka endpoint admin | 403 |
| Upload file bukan gambar atau terlalu besar | Ditolak |
| Harga normal diisi lebih kecil atau sama dengan harga jual | Ditolak dengan pesan di field harga normal |
| Tidak ada banner aktif atau tidak ada paket promo | Bagian tersebut disembunyikan dari beranda |
| Hanya ada satu banner aktif | Tampil tanpa slide otomatis dan tanpa tombol navigasi |
| Menghapus game yang masih punya paket / paket yang pernah dipesan | Ditolak; disarankan menonaktifkan |
| Sesi habis | Ke halaman masuk; keranjang tetap aman di server |
| Jaringan atau server gagal | Pesan galat dan tombol coba lagi |

## 9. Success metrics

Keberhasilan diukur dengan pengujian. Target kinerja berikut adalah usulan.

**Kelengkapan**
- 100% FR MVP lolos uji manual dan feature test.
- Dua alur end-to-end berhasil, sesuai alur Auspify langkah 5:
  - Buy now: daftar → cari game → isi ID + pilih paket → Buy now → bayar sandbox → Completed otomatis.
  - Keranjang: beberapa paket (game/akun berbeda) → keranjang → checkout → bayar sandbox → Completed otomatis.
- `docker compose up` dari kondisi bersih langsung menjalankan aplikasi.
- README, screenshot, dan daftar teknologi lengkap.

**Akurasi dan keamanan**
- 0 kasus stok minus atau terjual melebihi stok pada uji pembelian bersamaan.
- 0 kasus pesanan diproses dua kali.
- 0 kasus pembeli melihat pesanan orang lain atau mengakses panel admin.
- 100% data akun di pesanan lolos validasi format game-nya.
- Pesanan `paid` menjadi `completed` dalam ≤ 10 detik selama worker berjalan.

**Pengalaman (usulan)**
- Tidak ada scroll horizontal pada 375 px dan 1440 px.
- Katalog merespons < 1 detik dengan 50 game dan 500 paket uji.

## 10. Open questions

**Pemblokir: semua sudah dijawab (14 Nov 2026)**
- **OQ-1, Payment gateway:** Midtrans Snap sandbox. Akun sandbox dibuat sendiri oleh pemilik project.
- **OQ-2, Status pembayaran di lokal:** cek status lewat API Midtrans, tanpa webhook.
- **OQ-3, Ongkir:** tidak ada (produk digital).
- **OQ-4, Data pembeli saat checkout:** diganti data akun game per item (FR-4.3).
- **OQ-10, Mata uang dan bahasa:** IDR, antarmuka bahasa Inggris.
- **OQ-11, Tenggat:** 1 minggu.
- **OQ-15, Validasi akun:** format saja di MVP; cek akun sungguhan di v2.
- **OQ-16, Penyelesaian pesanan:** otomatis 5–10 detik setelah dibayar.

**Bisa diputuskan selama implementasi**
- **OQ-5, Upload gambar:** format (JPG/PNG/WebP?) dan ukuran maksimal (misalnya 2 MB)?
- **OQ-6, Password:** panjang minimum (Task 3 memakai 8)?
- **OQ-7, Akun admin:** dibuat lewat seeder, perintah Artisan, atau keduanya?
- **OQ-9, Pembatalan:** bolehkah pembeli membatalkan pesanan Unpaid?
- **OQ-12, Batas waktu bayar:** berapa lama pesanan Unpaid sebelum otomatis dibatalkan?
- **OQ-14, Genre:** daftar genre (misalnya MOBA, Battle Royale, Sports, Sandbox, RPG)?
- **OQ-18, Rasio gambar banner:** ukuran gambar banner yang disarankan untuk admin (misalnya 1920×600)?
- **OQ-17, Daftar game awal dan field akunnya:** game apa saja di data demo, dan format ID pastinya (perlu dicek per game)?

## Verifikasi PRD terhadap syarat Auspify

| Syarat Auspify | Tercakup di |
|---|---|
| Alur 1: desain halaman daftar produk | FR-2, FR-3, FR-4 (desain di DESIGN.md) |
| Alur 2: API manajemen produk | FR-9 |
| Alur 3: keranjang belanja | FR-5 (ditambah Buy now di FR-4.6 untuk pembelian cepat) |
| Alur 4: pesanan dan akun pengguna | FR-1, FR-6, FR-7, FR-8, FR-10 |
| Alur 5: uji alur end-to-end | Bagian 9 |
| Fitur: katalog produk | FR-2 (beranda, promo, kartu game), FR-3, FR-4 |
| Fitur: keranjang belanja | FR-5 |
| Fitur: akun pengguna | FR-1 |
| Fitur: manajemen pesanan | FR-6 sampai FR-8, FR-10 |
| Stack: Laravel, MySQL, Vue.js, Docker | Context, FR-11 |
