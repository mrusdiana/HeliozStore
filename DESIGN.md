# Kontrak Desain: HeliozStore.com (Game Top-Up Store)

Status: **DRAF v2 (Immersive Calm Blue), menunggu persetujuan.** Coding UI dimulai setelah kontrak ini disetujui.
Acuan fitur: `PRD.md`. Jika kontrak ini dan PRD berbeda, PRD yang berlaku dan kontrak ini diperbarui.

Aturan perubahan: setiap perubahan tampilan, alur, atau bentuk API dicatat di sini **sebelum** kodenya diubah.

Antarmuka berbahasa **Inggris**. Dokumen ini ditulis dalam bahasa Indonesia; teks yang tampil di layar ditulis dalam bahasa Inggris.

**Riwayat:** v3.4 (18 Nov 2026) pesan galat data akun disembunyikan sampai dibutuhkan; QRIS jadi grup akordeon biasa, baris logo pembayaran tidak dibungkus, field akun sebaris di mobile. v3.3 (18 Nov 2026) menu hamburger di mobile, katalog game 3 kolom di mobile, kolom `games.banner_path` + `banner_url` untuk header halaman game. v3.2 (18 Nov 2026) Flash Sale biru dengan titik berkedip, banner game diposisikan dari atas dan menyatu tanpa garis. v3.1 (18 Nov 2026) logo chip + H petir, navbar 2 baris, header game selebar layar, Flash Sale tanpa neon dan 2×2 di mobile, logo pembayaran resmi, ukuran mobile diperkecil. v3 (18 Nov 2026) palet royal blue navy, logo sparkle, layout halaman game berbasis StepCard + ringkasan, akordeon metode pembayaran, Flash Sale lebih mencolok, pembelian tamu + halaman Track order. v2.2 (18 Nov 2026) logo matahari, Flash Sale ringkas biru/rose, TrustBadges, langkah metode pembayaran, News & events, footer lengkap, dashboard admin + grafik, ikon item mata uang. v2.1 (18 Nov 2026) bagian "On promo" diganti panel Flash Sale dan ikon paket mengikuti mata uang game. v2 (18 Nov 2026) mengganti gaya neon cyan/ungu (Russo One, Chakra Petch, sudut terpotong) menjadi Immersive Dark dengan biru calm. Alur, layar, dan kontrak data tidak berubah.

---

## 1. Prinsip

1. **Gaming, tapi tetap toko.** Nuansa immersive dipakai untuk suasana; harga, data akun, dan tombol bayar harus tetap paling mudah dibaca.
2. **Tiga langkah jelas:** isi data akun → pilih paket → bayar. Pembeli selalu tahu sedang di langkah mana.
3. **Beli cepat dulu.** "Buy now" adalah tombol utama karena kebanyakan pembeli top-up satu kali; "Add to cart" tersedia untuk pembelian beberapa item.
4. **Data akun tidak boleh salah.** Petunjuk cara menemukan ID tampil di atas form; contoh format ada di placeholder. Pesan galat kecil (11–12px) muncul di bawah field hanya saat ada masalah: format salah setelah field ditinggalkan, atau field masih kosong ketika pembeli sudah lanjut memilih metode pembayaran.
5. **Status selalu terlihat.** Unpaid, Paid, Completed, Cancelled memakai teks dan ikon, bukan warna saja.
6. **Gaya (Immersive Dark, Calm Cerulean Blue):** latar gelap pekat (`#05070C`) dengan cahaya atmosferik biru calm (`#38BDF8`) yang lembut, permukaan slate semi-transparan berlapis, border tipis yang menyala halus, dan tipografi geometris modern. Tanpa efek glitch, scanline, atau neon yang menusuk mata.

---

## 2. Design token

Semua warna, ukuran, dan jarak di komponen **wajib** memakai token.

### 2.1 Warna (Immersive Dark, Royal Blue — v3)

| Token | Nilai | Dipakai untuk |
|---|---|---|
| `--bg` | `#05080F` | Latar utama halaman (deep navy) |
| `--bg-aura` | `radial-gradient(ellipse 70% 50% at 50% -10%, rgba(37, 99, 235, 0.30), transparent 70%), radial-gradient(ellipse 40% 40% at 100% 0%, rgba(59, 130, 246, 0.12), transparent 70%)` | Cahaya atmosferik biru di atas halaman |
| `--surface` | `#0A1020` | Kartu, panel utama, modal |
| `--surface-glass` | `rgba(10, 16, 32, 0.78)` | Header sticky & bar bawah mobile (dengan `backdrop-filter: blur(16px)`) |
| `--surface-2` | `#101931` | Kartu terpilih/hover, input, chip nonaktif |
| `--border` | `rgba(96, 165, 250, 0.12)` | Garis pemisah, border kartu |
| `--border-specular` | `rgba(96, 165, 250, 0.35)` | Border highlight saat hover/fokus |
| `--text` | `#F1F5F9` | Teks utama |
| `--muted` | `#8A9BB8` | Teks sekunder, metadata, harga coret |
| `--primary` | `#3B82F6` | Royal blue — tombol utama (Buy now, Checkout, Pay now), paket terpilih, link aktif, badge nomor langkah |
| `--primary-hover` | `#60A5FA` | Hover tombol utama |
| `--on-primary` | `#04102A` | Teks di atas `--primary` (navy gelap, kontras ±5:1) |
| `--secondary` | `#1D4ED8` | Aksen gradasi, badge genre |
| `--hot` | `#FB7185` | Badge "Promo" / "Hot" |
| `--success` | `#34D399` | Completed, "In stock" |
| `--warning` | `#FBBF24` | Unpaid, "Only N left" |
| `--danger` | `#F87171` | Galat, Cancelled, "Out of stock", hapus |
| `--glow` | `0 0 0 1px rgba(59, 130, 246, 0.6), 0 10px 30px -6px rgba(59, 130, 246, 0.35)` | Glow paket terpilih, kartu hover, fokus input |

- MVP hanya **tema gelap**.
- Semua pasangan teks dan latar wajib kontras ≥ 4.5:1 (`--text` di atas `--surface` ±16:1; `--muted` di atas `--surface-2` ±6:1; `--on-primary` di atas `--primary` ±5:1).
- Aura dan glow bersifat lembut (diffused), hanya untuk latar hero dan elemen interaktif (terpilih, hover, fokus).

### 2.2 Tipografi

Heading **Space Grotesk** (600, 700), teks antarmuka **Plus Jakarta Sans** (400, 500, 600, 700), angka/ID/harga **JetBrains Mono** (500, 600). Cadangan: `system-ui, sans-serif`.

| Token | Ukuran / bobot | Dipakai untuk |
|---|---|---|
| `display` | 36px Space Grotesk 700, tracking -0.02em (mobile 26px) | Judul hero beranda |
| `h1` | 28px Space Grotesk 700, tracking -0.02em | Judul halaman, nama game |
| `h2` | 20px Space Grotesk 600, tracking -0.01em | Judul bagian ("1. Enter account details", "On promo", "All games") |
| `body` | 15px Plus Jakarta Sans 400, line-height 1.5 | Teks umum, deskripsi, input |
| `small` | 13px Plus Jakarta Sans 500 | Metadata, petunjuk format, nama game di PromoCard |
| `price` | 17px JetBrains Mono 600 | Harga di kartu paket |
| `price-lg` | 26px JetBrains Mono 600 | Total keranjang dan checkout |

- Harga, stok, timer, dan ID memakai `font-variant-numeric: tabular-nums`; harga tidak terpotong ke baris baru (`white-space: nowrap`).
- Space Grotesk hanya untuk judul dan angka besar; label formulir dan paragraf memakai Plus Jakarta Sans.

### 2.3 Jarak, bentuk, dan gerak

| Token | Nilai |
|---|---|
| Skala jarak | 4, 8, 12, 16, 24, 32, 48px |
| Radius | 12px tombol/input; 16px kartu/panel; 14px gambar game; 9999px pill (chip genre, badge) |
| Sudut | Membulat mulus dengan border `1px solid var(--border)`; tanpa sudut terpotong (`clip-path`) |
| Rasio gambar game | 1:1 (ikon game) di kartu katalog dan admin. Header halaman game 16:9 memakai banner game itu dengan gradasi bawah menyatu ke `--bg` (`linear-gradient(to top, #05070C 5%, rgba(5,7,12,0.4) 60%, transparent)`); kalau tidak ada banner, ikon 1:1 di atas `--bg-aura` |
| Transisi | 180–220ms `cubic-bezier(0.16, 1, 0.3, 1)` untuk warna, border, glow, dan `transform: translateY(-2px)` saat hover |
| Gerak | Mati saat `prefers-reduced-motion` |
| Tinggi elemen interaktif | Minimal 44px |

### 2.4 Breakpoint

| Nama | Lebar | Grid game | Grid paket |
|---|---|---|---|
| Mobile | < 640px | 3 kolom (kartu ringkas, nama 12px) | 2 kolom |
| Tablet | 640–1023px | 4 kolom | 3 kolom |
| Desktop | ≥ 1024px | 6 kolom, konten maks. 1200px | 4 kolom |

Wajib diuji pada 375, 768, 1024, dan 1440px tanpa scroll horizontal.

---

## 3. Komponen

| Komponen | State wajib | Aturan |
|---|---|---|
| **Button** (`primary`, `secondary`, `ghost`, `danger`, `icon`) | default, hover (glow lembut), focus, disabled, loading | `primary`: latar `--primary`, teks `--on-primary`, hover `--primary-hover`. `secondary`: latar `--surface-2` dengan border `--border-specular`. Loading: teks berubah ("Adding…", "Placing order…") dan tombol nonaktif. Ikon wajib `aria-label`. |
| **Field** (text, numeric, email, password, select, textarea, file, toggle) | default, focus (glow biru), error, disabled | Latar `--surface-2`, border `--border`. Label di atas, petunjuk format di bawah ("5–12 digits"), galat menggantikan petunjuk dengan teks `--danger`. |
| **HeroCarousel** | auto-play, paused, single | Selebar konten (maks. 1200px), radius 16px, rasio ±16:5 desktop dan 16:9 mobile. Gambar banner + overlay gelap bergradien dari kiri/bawah + judul (Space Grotesk) + subjudul promo + tombol "Top up now". Berganti tiap 5 detik dengan transisi geser/fade 400ms. Tombol ‹ › dan indikator titik berbentuk pill. Berhenti saat hover/fokus/sentuh; tanpa auto-play bila `prefers-reduced-motion`. Satu banner: tanpa navigasi. |
| **FlashSale** + **PromoCard** | default, hover, out of stock | Semua aksen biru (`--primary`), bukan merah. Panel `--surface` dengan border `--primary`/40 (tanpa glow/neon, tanpa cahaya blur). Header: titik biru berkedip (`animate-ping`, mati saat reduced motion), judul "Flash Sale" + subjudul, timer 3 kotak `HH` `MM` `SS`. Grid PromoCard **2 kolom di mobile** (kartu ringkas: ikon item di atas, nama game, paket, harga), 4 kolom di desktop. Badge diskon `--primary` solid, bar stok tipis. Hover: border `--primary`/60 tanpa bayangan. |
| **GameCard** | default, hover, inactive | Gambar 1:1 dan nama game **saja** (tanpa harga). Hover: `translateY(-2px)`, border `--border-specular`, glow biru halus di bawah kartu. |
| **PackageTile** | default, hover, selected, out of stock | Latar `--surface`, border `--border`. Foto paket, atau ikon mata uang sesuai game (Diamonds: `gem`, UC/Tokens/CP: `coins`, Genesis Crystals: `sparkles`, Robux: `hexagon`, VP: `zap`; lainnya `coins`), jumlah ("86 Diamonds"), harga (JetBrains Mono); bila promo: harga normal dicoret + badge "Promo". Terpilih: latar `--surface-2` dengan tint biru 8%, border `--primary` + `--glow` + ikon centang bulat biru. Habis: `opacity: 0.45` + "Out of stock". Opsional badge "Hot". |
| **AccountForm** | empty, valid, error | Field dibuat dari `account_fields` game. Petunjuk pengisian game (misalnya "Find your ID in Profile") di atas, dalam kotak info biru redup. Dua field (mis. User ID + Zone ID) selalu sebaris kiri-kanan, termasuk di mobile (kolom kiri lebih lebar). |
| **StepHeader** | — | Nomor langkah dalam lingkaran biru translucent (`rgba(56, 189, 248, 0.14)`) berborder `--primary` + judul ("1. Enter account details", "2. Select a package"). |
| **PurchaseSummary** | empty, ready | Sticky di desktop (kanan); bar bawah kaca (`--surface-glass` + blur) di mobile. Isi: game, paket terpilih, akun, jumlah, harga (`price-lg`), tombol utama **"Buy now"** dan tombol sekunder **"Add to cart"**. Bar bawah mobile: harga + "Buy now" + ikon keranjang ("Add to cart"). |
| **QuantityStepper** | default, min, max, disabled | − / + 44px, tidak bisa < 1 atau > stok. |
| **StockBadge** | in stock, low (≤ 5), out | Pill translucent: "In stock" (`--success`) / "Only 3 left" (`--warning`) / "Out of stock" (`--danger`). |
| **CartItem** | default, updating, problem | Gambar game kecil, nama game + paket, data akun (misalnya "ID 123456789 (1234)"), stepper, subtotal, hapus. Problem: pesan merah. |
| **OrderStatusBadge** | 4 status | Pill translucent dengan ikon + teks (lihat §6). |
| **OrderTimeline** | — | Unpaid → Paid → Completed; langkah aktif menyala biru (Paid) atau hijau (Completed). Versi Cancelled menampilkan langkah merah. |
| **SearchBar** | empty, filled | Latar `--surface-2` dengan ikon kaca pembesar, placeholder "Search games". Mencari setelah 300ms berhenti mengetik. |
| **GenreChips** | default, selected | Pill button: "All" + daftar genre; satu dipilih (latar `--primary`, teks `--on-primary`). |
| **AccountFieldsEditor** (admin) | — | Tabel baris field: key, label, type, min, max, placeholder; tambah/hapus baris; minimal satu. |
| **ImageUpload** (admin) | empty, preview, error | Pratinjau sebelum disimpan. |
| **DataTable** (admin) | loading, empty, filled | Di mobile menjadi daftar kartu. |
| **Modal / ConfirmDialog**, **Toast**, **EmptyState**, **Skeleton** | — | Latar `--surface` dengan backdrop blur. Esc menutup, fokus kembali, toast 3 detik, skeleton shimmer biru gelap seukuran konten. |

| **StepCard** | — | Kartu `--surface` dengan header: badge nomor `01` (kotak 28px `--primary`/15, teks mono `--primary`) + judul (h2) + keterangan kanan kecil `--muted` (mis. "No password needed", "Tax included"). |
| **TrustBadges** | — | Baris 3 badge di bawah nama game: ikon `zap` "Fast delivery", `message-circle` "24/7 chat support", `shield-check` "Secure payment". Pill `--surface-2`, ikon `--primary`. |
| **PaymentMethodPicker** | collapsed, expanded, selected | Empat grup akordeon yang seragam: **QRIS**, **E-wallet**, **Virtual account**, **Convenience store**. Setiap grup = header (nama + metode terpilih + chevron) dan di bawahnya **satu baris** chip logo putih kecil (tidak pernah dibungkus ke baris baru; ukuran chip mengecil di layar sempit). Dibuka → kartu metode berisi logo, nama, dan total. Terpilih: border `--primary` + `--glow` + centang. |
| **NewsCard** | default, hover | Gambar 16:9, tag kategori ("Event", "Patch", "Esports") pill `--secondary`, judul 2 baris, tanggal (`--muted`). Hover: `translateY(-2px)` + border `--border-specular`. |
| **StatCard** (admin) | loading, filled | Label (`--muted`), angka besar (Space Grotesk/JetBrains Mono), perubahan vs kemarin dengan ikon panah + warna (`--success` naik, `--danger` turun) dan teks, bukan warna saja. |
| **BarChart / Sparkline** (admin) | loading, empty, filled | SVG/CSS ringan tanpa library. Bar vertikal untuk pendapatan 7 hari terakhir; bar horizontal untuk persentase penjualan per game. Setiap bar punya label teks nilai; warna `--primary`. |

**Ikon:** lucide (SVG), tanpa emoji. **Logo pembayaran:** logo resmi bank/e-wallet/gerai (QRIS, GoPay, OVO, DANA, ShopeePay, LinkAja, BCA, BNI, BRI, Mandiri, Permata, Indomaret, Alfamart) dari Wikimedia Commons, ditampilkan dalam chip putih kecil (tinggi 20–24px). **Ikon item mata uang:** SVG buatan sendiri per mata uang (berlian, koin UC, hexagon Robux, kristal, token, dll.), tidak memakai aset resmi game.

---

## 4. Navigasi dan layout

### 4.1 Toko

```
Desktop (2 baris)
┌──────────────────────────────────────────────────────────────────┐
│ [Logo]  [ Search games .................................. ] [Cart 2] │
│ ⌂ Top up  ⌕ Track order  ▤ News          (Orders · Admin) Sign in · Register │
│ (tautan aktif: garis bawah 2px --primary)                          │
├──────────────────────────────────────────────────────────────────┤
│  Konten (maks. 1200px) di atas --bg + --bg-aura                     │
├──────────────────────────────────────────────────────────────────┤
│ Footer: logo + deskripsi singkat + ikon sosial | Sitemap | Support │
│ | Legal ; baris bawah: © 2026 HeliozStore.com + disclaimer demo      │
└──────────────────────────────────────────────────────────────────┘

Mobile
┌──────────────────────────────┐
│ [Logo]          [Cart 2] [≡]  │
│ [ Search games ............ ] │
│ Top up · Track order · News → │  (baris tautan bisa digeser)
├──────────────────────────────┤
│  Konten                       │
└──────────────────────────────┘
```

- Header selalu punya link **"Track order"**. Belum masuk: "Sign in" menggantikan "Orders" dan "Account". Admin melihat link "Admin".
- Header lengket (sticky): latar `--surface-glass`, `backdrop-filter: blur(16px)`, garis bawah `1px solid var(--border)`.

### 4.2 Panel admin

- Panel admin adalah bagian dari web yang sama (satu aplikasi, satu login). Link "Admin" di header hanya muncul untuk akun berperan admin.
- Desktop: sidebar kiri ("Orders", "Games", "Packages", "Banners", "Back to store", "Sign out").
- Mobile: header dengan menu (≡). Halaman pertama: **Dashboard** (`/admin`).
- Link "Admin" di header **tidak dirender** untuk pengunjung publik atau pembeli; hanya akun `role = admin` yang sudah masuk.

---

## 5. Spesifikasi layar

Setiap layar data wajib punya 4 state: **memuat** (skeleton), **kosong**, **galat** (pesan + "Try again"), dan **terisi**.

### 5.1 Toko

| Route | Layar | Isi dan perilaku |
|---|---|---|
| `/` | **Home** | Urutan: (1) **HeroCarousel** selebar konten berisi banner promo aktif, di atas `--bg-aura`; (2) panel **FlashSale**: 4 paket promo dari game mana pun (lihat §3); (3) bagian **"All games"**: GenreChips + grid GameCard; (4) bagian **"News & events"**: 3–4 NewsCard berita/event game terbaru (sementara data contoh, belum ada API). Pencarian dari header dan genre tersimpan di URL (`?search=&genre=`); saat mencari, banner dan promo disembunyikan dan hanya hasil kartu game yang tampil. Tidak ada banner/promo aktif → bagian itu disembunyikan. Kosong: "No games found" + "Clear filters". |
| `/games/:slug` | **Game page** | **Header selebar layar** (edge to edge, bukan kartu): latar banner game (kalau tidak ada banner: ikon game diperbesar + blur) dengan overlay gradien navy; gambar diposisikan dari atas (`object-position: center 20%`) supaya bagian penting tidak terpotong, dan **tepi bawah tanpa garis**: seluruh latar header diberi `mask-image: linear-gradient(to bottom, black 45%, transparent)` sehingga memudar ke latar halaman (termasuk aura), tanpa kartu yang menimpa banner; isi header tetap di dalam kontainer 1200px: ikon 1:1, chip "GENRE · CURRENCY", nama game (h1 36px), subjudul "Instant delivery · arrives in 1–3 seconds", dan di kanan chip "Service status • Online 24/7". Di bawah header, grid 2 kolom (desktop): kiri StepCard berurutan **01 Account details** (AccountForm + petunjuk), **02 Select package** (grid PackageTile + ikon item), **03 Quantity** (QuantityStepper lebar penuh: input + tombol + / −), **04 Payment method** (PaymentMethodPicker), **05 Contact details** (email wajib untuk tamu, terisi otomatis bila masuk; WhatsApp opsional); kanan **Order summary** sticky (ikon + game + paket + akun, harga item, biaya admin Rp 0, total `price-lg`, tombol "Buy now", dan "Add to cart" hanya untuk akun yang masuk). Mobile: langkah bertumpuk, ringkasan jadi bar bawah. `?package=<id>` memilih paket. "Buy now" aktif setelah akun valid + paket + metode + email valid. |
| `/cart` | **Cart** | Daftar CartItem dikelompokkan per game + ringkasan (Total) + "Checkout". Item bermasalah: "Checkout" nonaktif + "Fix the highlighted items to continue". Kosong: "Your cart is empty" + "Browse games". |
| `/checkout` | **Review & pay** | Mode Buy now terbuka untuk tamu; mode keranjang wajib masuk. Dua mode: **keranjang** (semua item keranjang) atau **Buy now** (`?mode=buy-now`, satu item yang disimpan sementara di frontend). Ringkasan item beserta data akun ("Please double-check your account details. Items are sent to these accounts.") + Total + "Place order & pay". Keranjang kosong atau item Buy now hilang (misalnya setelah refresh) → kembali ke halaman sebelumnya. |
| `/track` | **Track order** | Publik. Form nomor transaksi + email → tampilan sama dengan Order detail (timeline, item, total, Pay now bila Unpaid). Setelah bayar sebagai tamu, pembeli diarahkan ke `/track?number=…&email=…`. Galat: "We couldn't find an order with that number and email." |
| `/orders` | **My orders** | Nomor, tanggal, ringkasan item, total, OrderStatusBadge. Kosong: "No orders yet". |
| `/orders/:number` | **Order detail** | OrderTimeline di atas, item + data akun, total. Unpaid: "Pay now" + "Check payment status". Paid: "Delivering your items…" dan halaman memperbarui status otomatis tiap 3 detik sampai Completed. Completed: "Items delivered" + toast. Cancelled: "Payment expired". |
| `/login`, `/register` | **Sign in / Create account** | Kartu `--surface` di tengah dengan latar `--bg-aura` halus. Galat: "Incorrect email or password." Setelah masuk, kembali ke halaman sebelumnya. |

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
| `/admin` | **Dashboard** | Tanggal hari ini; 4 StatCard (pendapatan hari ini, pesanan hari ini, menunggu pembayaran, perlu diproses); BarChart pendapatan 7 hari; persentase status pesanan; 5 pesanan terbaru. |
| `/admin/orders` | **Orders** | DataTable: nomor, tanggal, pembeli, total, status. Filter status + cari nomor. |
| `/admin/orders/:number` | **Order detail** | Pembeli, item + data akun (bisa disalin dengan tombol copy), pembayaran, status. Paid yang belum selesai: tombol "Mark as completed". |
| `/admin/games` | **Games** | DataTable: ikon kecil, nama, genre, jumlah paket, Active, urutan, aksi. Tombol "New game". |
| `/admin/games/new`, `/admin/games/:id/edit` | **Game form** | Name, Genre, Icon 1:1 (ImageUpload), Banner halaman game 16:9 opsional (ImageUpload lebar, field `banner`), Currency name, Account instructions, AccountFieldsEditor, Active, Sort order. |
| `/admin/packages` | **Packages** | BarChart horizontal "Best-selling games" (persentase item terjual per game, 30 hari); filter per game; DataTable: game, nama paket, jumlah item, harga, stok, Active, aksi. Tombol "New package". |
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
| `paid` | Paid | `--primary` | credit-card |
| `completed` | Completed | `--success` | check-circle |
| `cancelled` | Cancelled | `--danger` | x-circle |

---

## 7. Aksesibilitas (wajib lolos)

- [ ] Kontras teks ≥ 4.5:1 di atas `--bg` dan `--surface`.
- [ ] Semua fungsi bisa dipakai dengan keyboard; fokus selalu terlihat (`--glow` + outline biru).
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
Respons `GET /games` dan `GET /games/{slug}` dibungkus `{ data: ... }` (API Resource Laravel). Paket tanpa foto: frontend memakai gambar game.

### 8.1 Publik

| Method dan path | Respons |
|---|---|
| `POST /auth/register` (201), `POST /auth/login` | `{ user: { id, name, email, role }, token }` |
| `GET /genres` | `[ "MOBA", "Battle Royale", ... ]` |
| `GET /home` | `{ banners: [ { id, title, subtitle, image_url, game_slug } ], promos: [ { id, name, image_url, price, original_price, stock, game: { name, slug, currency_name } } ], games: [ { id, name, slug, image_url } ] }` |
| `GET /games?search&genre` | `[ { id, name, slug, genre, image_url } ]` |
| `GET /games/{slug}` | `{ id, name, slug, genre, image_url, banner_url, currency_name, account_instructions, account_fields: [...], packages: [ { id, name, image_url, item_amount, price, original_price, stock } ] }` — game nonaktif → 404 |

### 8.2 Pembeli (wajib masuk)

| Method dan path | Respons |
|---|---|
| `POST /auth/logout`, `GET /auth/me` | `{ message }` / `{ user: { id, name, email, role } }` |
| `GET /cart` | `{ items: [ { id, game: { name, slug, image_url }, product: { id, name, price, stock }, quantity, account_data, subtotal, problem } ], total, can_checkout }` |
| `POST /cart/items` `{ product_id, quantity, account_data: { user_id, zone_id } }` | Keranjang terbaru |
| `PATCH /cart/items/{id}` `{ quantity }` | Keranjang terbaru |
| `DELETE /cart/items/{id}` | Keranjang terbaru |
| `POST /orders` `{ source: "cart", payment_method }` | 201 `{ order, snap_token }` dari semua isi keranjang; keranjang dikosongkan |
| `POST /orders` `{ source: "buy_now", product_id, quantity, account_data, payment_method, contact_email, contact_whatsapp }` | **Boleh tanpa login.** 201 `{ order, snap_token }` dari satu item. `contact_email` wajib untuk tamu (akun yang masuk memakai email akunnya bila kosong); `contact_whatsapp` opsional. |
| `GET /orders?page` | `{ data: [ order ringkas ], page, last_page, total }` |
| `GET /orders/{number}` | Order lengkap (status dicek ke Midtrans bila Unpaid; diselesaikan bila Paid > 10 detik) |
| `POST /orders/{number}/pay` | `{ snap_token }` untuk membayar ulang |
| `POST /orders/{number}/check-payment` | Order terbaru setelah cek ke Midtrans |

`account_data` divalidasi terhadap `account_fields` game. `problem`: `null`, `"insufficient_stock"`, atau `"unavailable"`.

`source: "cart"` tanpa login → 401.

**Lacak pesanan (publik, dibatasi 20 permintaan/menit):**

| Method dan path | Respons |
|---|---|
| `GET /orders/track?number&email` | Order lengkap (status dicek ke Midtrans bila Unpaid). Nomor + email tidak cocok → 404 |
| `POST /orders/track/pay` `{ number, email }` | `{ snap_token }` untuk membayar ulang |

Order berisi `contact: { email, whatsapp }`.

`payment_method` opsional: `qris`, `gopay`, `shopeepay`, `bca_va`, `bni_va`, `bri_va`, `mandiri_va`, `permata_va`. Bila diisi, popup Snap langsung membuka metode itu (`enabled_payments`).

### 8.3 Admin (wajib masuk + `role = admin`)

| Method dan path | Keterangan |
|---|---|
| `GET /admin/games`, `POST /admin/games` (multipart) | Termasuk game nonaktif; buat game + gambar |
| `POST /admin/games/{id}` (multipart, `_method=PUT`), `DELETE /admin/games/{id}` | Ubah (PHP tidak membaca file dari `PUT` multipart) / hapus (409 jika masih punya paket) |
| `GET /admin/products?game_id&page`, `POST /admin/products` | Daftar dan buat paket |
| `POST /admin/products/{id}` (multipart, `_method=PUT`), `DELETE /admin/products/{id}` | Ubah / hapus (409 jika pernah dipesan) |
| `GET /admin/dashboard` | `{ today: { revenue, orders }, yesterday: { revenue, orders }, pending_payment, to_process, revenue_7d: [ { date, revenue } ], status_counts: { ... }, recent_orders: [...], top_games: [ { name, items_sold, percent } ] }` |
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

## 9. Keputusan desain (terkunci)

- **D-1, Nama toko dan logo:** HeliozStore.com. Logo: ikon chip prosesor (kotak membulat bergaris tebal dengan 3 pin di tiap sisi) berisi huruf **H** yang dibelah petir, gradien `#38BDF8` → `#2563EB`, tanpa latar; diikuti wordmark "Helioz" (`--text`) + "Store" (`--primary`), Space Grotesk 700. Dipakai juga sebagai favicon SVG.
- **D-2, Hero beranda:** carousel banner promo selebar konten (maks. 1200px), 5 detik per slide, diatur admin.
- **D-3, Badge:** paket promo memakai badge "Promo" (`--hot` translucent). Badge "Hot" opsional, hanya untuk paket populer non-promo agar tidak bertumpuk.
