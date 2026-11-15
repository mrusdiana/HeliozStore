# Kontrak Desain: Game Top-Up Store

Status: **DRAF, menunggu persetujuan.** Coding UI dimulai setelah kontrak ini disetujui.
Acuan fitur: `PRD.md`. Jika kontrak ini dan PRD berbeda, PRD yang berlaku dan kontrak ini diperbarui.

Aturan perubahan: setiap perubahan tampilan, alur, atau bentuk API dicatat di sini **sebelum** kodenya diubah.

Antarmuka berbahasa **Inggris**. Dokumen ini ditulis dalam bahasa Indonesia; teks yang tampil di layar ditulis dalam bahasa Inggris.

---

## 1. Prinsip

1. **Gaming, tapi tetap toko.** Nuansa futuristik dipakai untuk suasana; harga, data akun, dan tombol bayar harus tetap paling mudah dibaca.
2. **Tiga langkah jelas:** isi data akun → pilih paket → bayar. Pembeli selalu tahu sedang di langkah mana.
3. **Beli cepat dulu.** "Buy now" adalah tombol utama karena kebanyakan pembeli top-up satu kali; "Add to cart" tersedia untuk pembelian beberapa item.
4. **Data akun tidak boleh salah.** Petunjuk format terlihat sebelum mengetik; galat muncul langsung di bawah field.
5. **Status selalu terlihat.** Unpaid, Paid, Completed, Cancelled memakai teks dan ikon, bukan warna saja.
6. **Gaya:** gelap, futuristik modern, aksen neon dengan glow halus. Tanpa efek glitch atau scanline yang mengganggu keterbacaan. Diseleksi dari skill `ui-ux-pro-max` (gaya Cyberpunk/Retro-Futurism, font "Gaming Bold").

---

## 2. Design token

Semua warna, ukuran, dan jarak di komponen **wajib** memakai token.

### 2.1 Warna (tema gelap)

| Token | Nilai | Dipakai untuk |
|---|---|---|
| `--bg` | `#0B0D17` | Latar halaman |
| `--bg-grid` | `rgba(34, 211, 238, 0.04)` | Pola grid halus di latar hero (dekoratif) |
| `--surface` | `#12152A` | Kartu, header, panel |
| `--surface-2` | `#1A1F3A` | Kartu yang dipilih/hover, input |
| `--border` | `rgba(148, 163, 184, 0.16)` | Garis pemisah, border kartu |
| `--text` | `#E6EAF2` | Teks utama |
| `--muted` | `#94A3B8` | Teks sekunder |
| `--primary` | `#22D3EE` | Tombol utama (Add to cart, Checkout, Pay now), paket terpilih, link aktif |
| `--on-primary` | `#04121A` | Teks di atas primary |
| `--secondary` | `#7C3AED` | Aksen dekoratif, glow, badge genre |
| `--hot` | `#F43F5E` | Badge "Hot" / paket populer |
| `--success` | `#34D399` | Paid, Completed, "In stock" |
| `--warning` | `#FBBF24` | Unpaid, "Only N left" |
| `--danger` | `#F87171` | Galat, Cancelled, "Out of stock", hapus |
| `--glow` | `0 0 0 1px var(--primary), 0 0 16px rgba(34, 211, 238, 0.35)` | Glow paket terpilih dan fokus |

- MVP hanya **tema gelap**.
- Semua pasangan teks dan latar wajib kontras ≥ 4.5:1 (cek `--muted` di atas `--surface-2`).
- Glow hanya untuk elemen interaktif (terpilih, hover, fokus), bukan teks biasa.

### 2.2 Tipografi

Heading **Russo One**, teks **Chakra Petch** (400, 500, 600). Cadangan: `system-ui`.

| Token | Ukuran / bobot | Dipakai untuk |
|---|---|---|
| `display` | 40px Russo One (mobile 28px) | Judul hero beranda |
| `h1` | 28px Russo One | Judul halaman, nama game |
| `h2` | 20px Russo One | Judul bagian ("1. Enter account details") |
| `body` | 16px Chakra Petch 400, line-height 1.5 | Teks umum, input |
| `small` | 14px Chakra Petch | Metadata, petunjuk format |
| `price` | 18px Chakra Petch 600 | Harga di kartu paket |
| `price-lg` | 28px Chakra Petch 600 | Total keranjang dan checkout |

- Harga dan ID memakai `tabular-nums`; harga tidak terpotong ke baris baru.
- Russo One hanya untuk judul, tidak untuk paragraf atau label formulir.

### 2.3 Jarak, bentuk, dan gerak

| Token | Nilai |
|---|---|
| Skala jarak | 4, 8, 12, 16, 24, 32, 48px |
| Radius | 10px tombol/input; 14px kartu; gambar game 12px |
| Sudut "futuristik" | Kartu game dan tombol utama boleh memakai satu sudut terpotong (`clip-path`) sebagai aksen |
| Rasio gambar game | 3:4 (poster) di kartu katalog; 16:9 di banner halaman game |
| Transisi | 150–200ms untuk warna, border, glow, dan `transform: scale(1.02)` saat hover |
| Gerak | Mati saat `prefers-reduced-motion` |
| Tinggi elemen interaktif | Minimal 44px |

### 2.4 Breakpoint

| Nama | Lebar | Grid game | Grid paket |
|---|---|---|---|
| Mobile | < 640px | 2 kolom | 2 kolom |
| Tablet | 640–1023px | 4 kolom | 3 kolom |
| Desktop | ≥ 1024px | 6 kolom, konten maks. 1200px | 4 kolom |

Wajib diuji pada 375, 768, 1024, dan 1440px tanpa scroll horizontal.

---

## 3. Komponen

| Komponen | State wajib | Aturan |
|---|---|---|
| **Button** (`primary`, `secondary`, `ghost`, `danger`, `icon`) | default, hover (glow), focus, disabled, loading | Loading: teks berubah ("Adding…", "Placing order…") dan tombol nonaktif. Ikon wajib `aria-label`. |
| **Field** (text, numeric, email, password, select, textarea, file, toggle) | default, focus (glow), error, disabled | Label di atas, petunjuk format di bawah ("5–12 digits"), galat menggantikan petunjuk. |
| **HeroCarousel** | auto-play, paused, single | Selebar layar (rasio ±16:5 desktop, 16:9 mobile), gambar banner + overlay gelap bergradien tipis di sisi teks + judul (Russo One) + subjudul promo + tombol "Top up now". Berganti tiap 5 detik dengan transisi geser/fade 400ms. Tombol ‹ › dan indikator titik. Berhenti saat hover/fokus/sentuh; tanpa auto-play bila `prefers-reduced-motion`. Satu banner: tanpa navigasi. |
| **PromoCard** | default, hover, out of stock | Foto paket 1:1, nama game (kecil), nama paket, harga normal dicoret (`--muted`), harga jual (`--primary`), badge "Promo" (`--hot`). Klik → halaman game dengan paket terpilih. |
| **GameCard** | default, hover, inactive | Gambar 3:4 dan nama game **saja** (tanpa harga). Hover: sedikit membesar + glow border. |
| **PackageTile** | default, hover, selected, out of stock | Foto paket (atau ikon mata uang), jumlah ("86 Diamonds"), harga; bila promo: harga normal dicoret + badge "Promo". Terpilih: border `--primary` + glow + ikon centang. Habis: redup + "Out of stock". Opsional badge "Hot". |
| **AccountForm** | empty, valid, error | Field dibuat dari `account_fields` game. Petunjuk pengisian game (misalnya "Find your ID in Profile") di atas. |
| **StepHeader** | — | Nomor langkah dalam lingkaran neon + judul ("1. Enter account details", "2. Select a package"). |
| **PurchaseSummary** | empty, ready | Sticky di desktop (kanan), bar bawah di mobile: game, paket terpilih, akun, jumlah, harga, tombol utama **"Buy now"** dan tombol sekunder **"Add to cart"**. Di bar bawah mobile: harga + "Buy now" + ikon keranjang ("Add to cart"). |
| **QuantityStepper** | default, min, max, disabled | − / + 44px, tidak bisa < 1 atau > stok. |
| **StockBadge** | in stock, low (≤ 5), out | "In stock" / "Only 3 left" / "Out of stock". |
| **CartItem** | default, updating, problem | Gambar game kecil, nama game + paket, data akun (misalnya "ID 123456789 (1234)"), stepper, subtotal, hapus. Problem: pesan merah. |
| **OrderStatusBadge** | 4 status | Lihat §6. |
| **OrderTimeline** | — | Unpaid → Paid → Completed dengan langkah aktif menyala; versi Cancelled menampilkan langkah merah. |
| **SearchBar** | empty, filled | "Search games". Mencari setelah 300ms berhenti mengetik. |
| **GenreChips** | default, selected | "All" + daftar genre; satu dipilih. |
| **AccountFieldsEditor** (admin) | — | Tabel baris field: key, label, type, min, max, placeholder; tambah/hapus baris; minimal satu. |
| **ImageUpload** (admin) | empty, preview, error | Pratinjau sebelum disimpan. |
| **DataTable** (admin) | loading, empty, filled | Di mobile menjadi daftar kartu. |
| **Modal / ConfirmDialog**, **Toast**, **EmptyState**, **Skeleton** | — | Sama seperti Task 3: Esc menutup, fokus kembali, toast 3 detik, skeleton seukuran konten. |

**Ikon:** lucide (SVG), tanpa emoji.

---

## 4. Navigasi dan layout

### 4.1 Toko

```
Desktop
┌──────────────────────────────────────────────────────────────────┐
│ [Logo]  Games   [ Search games ............ ]   Orders  Account  [Cart 2] │
├──────────────────────────────────────────────────────────────────┤
│  Konten (maks. 1200px)                                              │
├──────────────────────────────────────────────────────────────────┤
│ Footer: © Store · "Demo project for learning purposes. Not affiliated │
│ with any game publisher."                                           │
└──────────────────────────────────────────────────────────────────┘

Mobile
┌──────────────────────────────┐
│ [Logo]          [Cart 2] [≡]  │
│ [ Search games ............ ] │
├──────────────────────────────┤
│  Konten                       │
└──────────────────────────────┘
```

- Belum masuk: "Sign in" menggantikan "Orders" dan "Account". Admin melihat link "Admin".
- Header lengket (sticky) dengan latar `--surface` semi-transparan dan blur.

### 4.2 Panel admin

- Panel admin adalah bagian dari web yang sama (satu aplikasi, satu login). Link "Admin" di header hanya muncul untuk akun berperan admin.
- Desktop: sidebar kiri ("Orders", "Games", "Packages", "Banners", "Back to store", "Sign out").
- Mobile: header dengan menu (≡). Halaman pertama: **Orders**.

---

## 5. Spesifikasi layar

Setiap layar data wajib punya 4 state: **memuat** (skeleton), **kosong**, **galat** (pesan + "Try again"), dan **terisi**.

### 5.1 Toko

| Route | Layar | Isi dan perilaku |
|---|---|---|
| `/` | **Home** | Urutan: (1) **HeroCarousel** selebar layar berisi banner promo aktif; (2) bagian **"On promo"**: grid/geser horizontal PromoCard (3–5 paket promo dari game mana pun); (3) bagian **"All games"**: GenreChips + grid GameCard (gambar + nama). Pencarian dari header dan genre tersimpan di URL (`?search=&genre=`); saat mencari, banner dan promo disembunyikan dan hanya hasil kartu game yang tampil. Tidak ada banner/promo aktif → bagian itu disembunyikan. Kosong: "No games found" + "Clear filters". |
| `/games/:slug` | **Game page** | Banner 16:9 + nama game + genre. Lalu **Step 1 – kartu data akun selebar konten di paling atas** (AccountForm + petunjuk), di bawahnya **Step 2 – grid PackageTile** (paket promo memakai harga coret). Desktop: PurchaseSummary sticky di kanan grid paket. `?package=<id>` (dari PromoCard) membuat paket itu langsung terpilih. Mobile: langkah bertumpuk, PurchaseSummary jadi bar bawah. "Buy now" dan "Add to cart" aktif setelah data akun valid dan paket dipilih. "Buy now" → `/checkout?mode=buy-now` dengan hanya item itu; "Add to cart" → toast "Added to cart" dan tetap di halaman. Belum masuk: tombol menjadi "Sign in to buy". |
| `/cart` | **Cart** | Daftar CartItem dikelompokkan per game + ringkasan (Total) + "Checkout". Item bermasalah: "Checkout" nonaktif + "Fix the highlighted items to continue". Kosong: "Your cart is empty" + "Browse games". |
| `/checkout` | **Review & pay** | Dua mode: **keranjang** (semua item keranjang) atau **Buy now** (`?mode=buy-now`, satu item yang disimpan sementara di frontend). Ringkasan item beserta data akun ("Please double-check your account details. Items are sent to these accounts.") + Total + "Place order & pay". Keranjang kosong atau item Buy now hilang (misalnya setelah refresh) → kembali ke halaman sebelumnya. |
| `/orders` | **My orders** | Nomor, tanggal, ringkasan item, total, OrderStatusBadge. Kosong: "No orders yet". |
| `/orders/:number` | **Order detail** | OrderTimeline di atas, item + data akun, total. Unpaid: "Pay now" + "Check payment status". Paid: "Delivering your items…" dan halaman memperbarui status otomatis tiap 3 detik sampai Completed. Completed: "Items delivered" + toast. Cancelled: "Payment expired". |
| `/login`, `/register` | **Sign in / Create account** | Kartu di tengah dengan latar neon halus. Galat: "Incorrect email or password." Setelah masuk, kembali ke halaman sebelumnya. |

### 5.2 Alur pembayaran dan penyelesaian

```
Game page ──"Buy now"──► Review & pay (1 item) ─┐
Cart ──"Checkout"──► Review & pay (semua item) ──┴─"Place order & pay"──► order dibuat (Unpaid) ──► popup Midtrans Snap
  popup selesai/ditutup ──► Order detail ──► backend cek status ke Midtrans
     paid      → badge "Paid" + "Delivering your items…" ──(5–10 detik)──► "Completed"
     pending   → tetap "Unpaid" + "Check payment status"
     expired   → "Cancelled" (stok dikembalikan)
```

Frontend tidak pernah mengubah status sendiri; status selalu diambil dari backend.

### 5.3 Panel admin

| Route | Layar | Isi dan perilaku |
|---|---|---|
| `/admin/orders` | **Orders** | DataTable: nomor, tanggal, pembeli, total, status. Filter status + cari nomor. |
| `/admin/orders/:number` | **Order detail** | Pembeli, item + data akun (bisa disalin dengan tombol copy), pembayaran, status. Paid yang belum selesai: tombol "Mark as completed". |
| `/admin/games` | **Games** | DataTable: poster kecil, nama, genre, jumlah paket, Active, urutan, aksi. Tombol "New game". |
| `/admin/games/new`, `/admin/games/:id/edit` | **Game form** | Name, Genre, Image (ImageUpload), Currency name, Account instructions, AccountFieldsEditor, Active, Sort order. |
| `/admin/packages` | **Packages** | Filter per game; DataTable: game, nama paket, jumlah item, harga, stok, Active, aksi. Tombol "New package". |
| `/admin/packages/new`, `/admin/packages/:id/edit` | **Package form** | Game, Name, Image (opsional), Item amount, Price, Original price (opsional; diisi = promo, harus lebih besar dari Price), Stock, Active. Hapus paket yang pernah dipesan ditolak: "Set it to inactive instead". |
| `/admin/banners` | **Banners** | Daftar banner: pratinjau gambar, judul, game tujuan, urutan, Active, aksi. Tombol "New banner". |
| `/admin/banners/new`, `/admin/banners/:id/edit` | **Banner form** | Image (pratinjau lebar), Title, Subtitle (teks promo), Game (tujuan klik), Sort order, Active. |

Pembeli yang membuka `/admin/*` diarahkan ke `/` dengan toast "You don't have access to that page."

---

## 6. Format dan teks

| Hal | Aturan | Contoh |
|---|---|---|
| Uang | IDR, `Intl.NumberFormat('id-ID')`, tanpa desimal | `Rp 25.000` |
| Tanggal | Tanggal, bulan singkat (Inggris), tahun, jam | `14 Nov 2026, 19:05` |
| Nomor pesanan | Prefiks + tanggal + kode acak | `TOP-20261114-7Q3K` |
| Data akun | Label + nilai; Zone/Server dalam kurung | `ID 123456789 (1234)` |
| Bahasa | Inggris, kalimat pendek | "Your cart is empty." |

**Status pesanan:**

| Nilai (API) | Label (UI) | Warna | Ikon |
|---|---|---|---|
| `pending_payment` | Unpaid | `--warning` | clock |
| `paid` | Paid | `--success` | credit-card |
| `completed` | Completed | `--success` | check-circle |
| `cancelled` | Cancelled | `--danger` | x-circle |

---

## 7. Aksesibilitas (wajib lolos)

- [ ] Kontras teks ≥ 4.5:1 di tema gelap.
- [ ] Semua fungsi bisa dipakai dengan keyboard; fokus selalu terlihat (glow + outline).
- [ ] Pilihan paket bisa dipilih dengan keyboard (grup radio: panah kiri/kanan, Space).
- [ ] Gambar game punya `alt` berisi nama game.
- [ ] Tombol ikon punya `aria-label`; perubahan jumlah keranjang dan status pesanan diumumkan (`aria-live`).
- [ ] Setiap input punya label; galat terhubung lewat `aria-describedby`.
- [ ] Target sentuh ≥ 44×44px; tidak ada scroll horizontal di 375px.
- [ ] `prefers-reduced-motion` mematikan animasi hover dan transisi.

---

## 8. Kontrak data (frontend ↔ backend)

Semua endpoint di bawah `/api`, JSON. Autentikasi: `Authorization: Bearer <token>`.
Nominal: bilangan bulat rupiah. Tanggal-waktu: ISO 8601. Gambar: URL lengkap (`image_url`).

### 8.1 Publik

| Method dan path | Respons |
|---|---|
| `POST /register`, `POST /login` | `{ user: { id, name, email, role }, token }` |
| `GET /genres` | `[ "MOBA", "Battle Royale", ... ]` |
| `GET /home` | `{ banners: [ { id, title, subtitle, image_url, game_slug } ], promos: [ { id, name, image_url, price, original_price, stock, game: { name, slug } } ], games: [ { id, name, slug, image_url } ] }` |
| `GET /games?search&genre` | `[ { id, name, slug, genre, image_url } ]` |
| `GET /games/{slug}` | `{ id, name, slug, genre, image_url, currency_name, account_instructions, account_fields: [...], packages: [ { id, name, image_url, item_amount, price, original_price, stock } ] }` — game nonaktif → 404 |

### 8.2 Pembeli (wajib masuk)

| Method dan path | Respons |
|---|---|
| `POST /logout` (204), `GET /me` | `{ id, name, email, role }` |
| `GET /cart` | `{ items: [ { id, game: { name, slug, image_url }, product: { id, name, price, stock }, quantity, account_data, subtotal, problem } ], total, can_checkout }` |
| `POST /cart/items` `{ product_id, quantity, account_data: { user_id, zone_id } }` | Keranjang terbaru |
| `PATCH /cart/items/{id}` `{ quantity }` | Keranjang terbaru |
| `DELETE /cart/items/{id}` | Keranjang terbaru |
| `POST /orders` `{ source: "cart" }` | 201 `{ order, snap_token }` dari semua isi keranjang; keranjang dikosongkan |
| `POST /orders` `{ source: "buy_now", product_id, quantity, account_data }` | 201 `{ order, snap_token }` dari satu item; keranjang tidak berubah |
| `GET /orders?page` | `{ data: [ order ringkas ], page, last_page, total }` |
| `GET /orders/{number}` | Order lengkap (status dicek ke Midtrans bila Unpaid; diselesaikan bila Paid > 10 detik) |
| `POST /orders/{number}/pay` | `{ snap_token }` untuk membayar ulang |
| `POST /orders/{number}/check-payment` | Order terbaru setelah cek ke Midtrans |

`account_data` divalidasi terhadap `account_fields` game. `problem`: `null`, `"insufficient_stock"`, atau `"unavailable"`.

### 8.3 Admin (wajib masuk + `role = admin`)

| Method dan path | Keterangan |
|---|---|
| `GET /admin/games`, `POST /admin/games` (multipart) | Termasuk game nonaktif; buat game + gambar |
| `POST /admin/games/{id}` (multipart, `_method=PUT`), `DELETE /admin/games/{id}` | Ubah (PHP tidak membaca file dari `PUT` multipart) / hapus (409 jika masih punya paket) |
| `GET /admin/products?game_id&page`, `POST /admin/products` | Daftar dan buat paket |
| `POST /admin/products/{id}` (multipart, `_method=PUT`), `DELETE /admin/products/{id}` | Ubah / hapus (409 jika pernah dipesan) |
| `GET /admin/orders?status&search&page`, `GET /admin/orders/{number}` | Pesanan + data akun |
| `POST /admin/orders/{number}/complete` | Tandai Completed manual (hanya dari Paid) |
| `GET /admin/banners`, `POST /admin/banners` (multipart) | Daftar dan buat banner |
| `POST /admin/banners/{id}` (multipart, `_method=PUT`), `DELETE /admin/banners/{id}` | Ubah / hapus banner |

Paket admin (`/admin/products`) dikirim sebagai multipart karena bisa membawa foto; ubah memakai `POST` + `_method=PUT`.

### 8.4 Galat

| Kode | Arti | Perilaku UI |
|---|---|---|
| 401 | Belum masuk / sesi habis | Ke `/login`, lalu kembali ke halaman semula |
| 403 | Bukan admin | Ke `/` + toast "You don't have access to that page." |
| 404 | Tidak ada / milik orang lain | "We couldn't find that." |
| 409 | Konflik (hapus data yang dipakai) | Pesan dari `message` |
| 422 | Validasi: `{ message, errors: { "account_data.user_id": "..." } }` | Galat di bawah field |
| 429 | Terlalu banyak percobaan masuk | Pesan dari `message` |
| 502 | Midtrans tidak bisa dihubungi | "Payment service is unavailable. Please try again." |
| 5xx / jaringan | Server atau jaringan gagal | "Something went wrong. Please try again." |

---

## 9. Pertanyaan terbuka desain

- **D-1, Nama toko dan logo:** nama toko apa? Logo ikon + teks, atau punya logo sendiri?
- **D-2, Hero beranda:** terjawab: carousel banner promo selebar layar, 5 detik per slide, diatur admin.
- **D-3, Badge:** terjawab sebagian: paket promo memakai badge "Promo". Apakah badge "Hot" terpisah masih diperlukan?
