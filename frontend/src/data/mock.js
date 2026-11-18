// MOCK DATA ONLY. Shapes follow DESIGN.md §8 so each block can be swapped for an API call.
// TODO: API — delete this file once every view fetches from the backend.

// Seeded images are served by Laravel (`php artisan storage:link`). If the backend is off, AppImage shows a fallback.
const ASSET = import.meta.env.VITE_ASSET_URL ?? 'http://localhost:8000/storage'

const fields = {
  idZone: [
    { key: 'user_id', label: 'User ID', type: 'numeric', min: 5, max: 12, placeholder: '123456789' },
    { key: 'zone_id', label: 'Zone ID', type: 'numeric', min: 3, max: 5, placeholder: '1234' },
  ],
  idOnly: [{ key: 'user_id', label: 'Player ID', type: 'numeric', min: 6, max: 12, placeholder: '123456789' }],
  uidServer: [
    { key: 'user_id', label: 'UID', type: 'numeric', min: 9, max: 10, placeholder: '800123456' },
    { key: 'zone_id', label: 'Server', type: 'text', min: 2, max: 10, placeholder: 'Asia' },
  ],
  username: [{ key: 'user_id', label: 'Username', type: 'text', min: 3, max: 20, placeholder: 'player123' }],
  riot: [{ key: 'user_id', label: 'Riot ID', type: 'text', min: 3, max: 22, placeholder: 'Name#TAG' }],
}

export const games = [
  { id: 1, name: 'Mobile Legends', slug: 'mobile-legends', genre: 'MOBA', image_url: `${ASSET}/games/mobile-legends.jpg`, banner_url: `${ASSET}/banners/mobile-legends.jpg`, currency_name: 'Diamonds', account_instructions: 'Open your profile. Your User ID and Zone ID are shown under your nickname, e.g. 123456789 (1234).', account_fields: fields.idZone },
  { id: 2, name: 'Free Fire', slug: 'free-fire', genre: 'Battle Royale', image_url: `${ASSET}/games/free-fire.jpg`, banner_url: `${ASSET}/banners/free-fire.jpg`, currency_name: 'Diamonds', account_instructions: 'Tap your avatar in the top-left corner. Your Player ID is below your nickname.', account_fields: fields.idOnly },
  { id: 3, name: 'PUBG Mobile', slug: 'pubg-mobile', genre: 'Battle Royale', image_url: `${ASSET}/games/pubg-mobile.jpg`, banner_url: `${ASSET}/banners/pubg-mobile.jpg`, currency_name: 'UC', account_instructions: 'Open your profile. Your Character ID is shown under your name.', account_fields: fields.idOnly },
  { id: 4, name: 'Genshin Impact', slug: 'genshin-impact', genre: 'RPG', image_url: `${ASSET}/games/genshin-impact.jpg`, banner_url: `${ASSET}/banners/genshin-impact.jpg`, currency_name: 'Genesis Crystals', account_instructions: 'Open Paimon menu. Your UID is in the bottom-right corner.', account_fields: fields.uidServer },
  { id: 5, name: 'Honor of Kings', slug: 'honor-of-kings', genre: 'MOBA', image_url: `${ASSET}/games/honor-of-kings.jpg`, banner_url: `${ASSET}/banners/honor-of-kings.jpg`, currency_name: 'Tokens', account_instructions: 'Open your profile. Copy the Player ID under your avatar.', account_fields: fields.idOnly },
  { id: 6, name: 'Roblox', slug: 'roblox', genre: 'Sandbox', image_url: `${ASSET}/games/roblox.jpg`, banner_url: `${ASSET}/banners/roblox.jpg`, currency_name: 'Robux', account_instructions: 'Enter your Roblox username, not your display name.', account_fields: fields.username },
  { id: 7, name: 'Valorant', slug: 'valorant', genre: 'FPS', image_url: `${ASSET}/games/valorant.jpg`, banner_url: `${ASSET}/banners/valorant.jpg`, currency_name: 'VP', account_instructions: 'Enter your Riot ID with the tag, e.g. Name#TAG.', account_fields: fields.riot },
  { id: 8, name: 'Call of Duty Mobile', slug: 'call-of-duty-mobile', genre: 'FPS', image_url: `${ASSET}/games/call-of-duty-mobile.jpg`, banner_url: `${ASSET}/banners/call-of-duty-mobile.jpg`, currency_name: 'CP', account_instructions: 'Open your profile. Your Player UID is under your name.', account_fields: fields.idOnly },
  { id: 9, name: 'Magic Chess Go Go', slug: 'magic-chess-go-go', genre: 'Strategy', image_url: `${ASSET}/games/magic-chess-go-go.jpg`, banner_url: `${ASSET}/banners/magic-chess-go-go.jpg`, currency_name: 'Diamonds', account_instructions: 'Tap your avatar to see your User ID and Server ID.', account_fields: fields.idZone },
  { id: 10, name: 'Free Fire MAX', slug: 'free-fire-max', genre: 'Battle Royale', image_url: `${ASSET}/games/free-fire-max.jpg`, banner_url: `${ASSET}/banners/free-fire-max.jpg`, currency_name: 'Diamonds', account_instructions: 'Your Player ID is under your nickname on the profile page.', account_fields: fields.idOnly },
  { id: 11, name: 'Blood Strike', slug: 'blood-strike', genre: 'FPS', image_url: `${ASSET}/games/blood-strike.jpg`, banner_url: `${ASSET}/banners/blood-strike.jpg`, currency_name: 'Gold', account_instructions: 'Open Settings, then Account. Copy the numeric ID.', account_fields: fields.idOnly },
  { id: 12, name: 'Delta Force', slug: 'delta-force', genre: 'FPS', image_url: `${ASSET}/games/delta-force.jpg`, banner_url: `${ASSET}/banners/delta-force.jpg`, currency_name: 'Delta Coins', account_instructions: 'Open your profile card in the lobby to find your UID.', account_fields: fields.idOnly },
  { id: 13, name: 'Honkai: Star Rail', slug: 'honkai-star-rail', genre: 'RPG', image_url: `${ASSET}/games/honkai-star-rail.jpg`, banner_url: `${ASSET}/banners/honkai-star-rail.jpg`, currency_name: 'Oneiric Shards', account_instructions: 'Your UID is in the bottom-left of the phone menu.', account_fields: fields.uidServer },
  { id: 14, name: 'Zenless Zone Zero', slug: 'zenless-zone-zero', genre: 'RPG', image_url: `${ASSET}/games/zenless-zone-zero.jpg`, banner_url: `${ASSET}/banners/zenless-zone-zero.jpg`, currency_name: 'Monochrome', account_instructions: 'Your UID is shown on the profile screen.', account_fields: fields.uidServer },
  { id: 15, name: 'Arena of Valor', slug: 'arena-of-valor', genre: 'MOBA', image_url: `${ASSET}/games/arena-of-valor.jpg`, banner_url: `${ASSET}/banners/arena-of-valor.jpg`, currency_name: 'Vouchers', account_instructions: 'Open your profile to copy your Open ID.', account_fields: fields.idOnly },
  { id: 16, name: 'eFootball', slug: 'efootball', genre: 'Sports', image_url: `${ASSET}/games/efootball.jpg`, banner_url: `${ASSET}/banners/efootball.jpg`, currency_name: 'eFootball Coins', account_instructions: 'Go to Extras, then User Information, and copy your Owner ID.', account_fields: fields.username },
]

export const genres = [...new Set(games.map((g) => g.genre))]

const priceTable = [
  [86, 22000], [172, 44000], [257, 66000, 70000], [344, 88000], [429, 110000],
  [514, 132000, 140000], [706, 175000], [878, 220000], [2195, 548000], [3688, 920000],
]

export const packagesBySlug = Object.fromEntries(
  games.map((g, gi) => [
    g.slug,
    priceTable.map(([amount, price, original], i) => ({
      id: gi * 100 + i + 1,
      name: `${amount} ${g.currency_name}`,
      image_url: null,
      item_amount: amount,
      price,
      original_price: original ?? null,
      stock: i === 8 ? 3 : i === 9 ? 0 : 50,
      hot: i === 3,
    })),
  ]),
)

export const banners = games
  .filter((g) => g.banner_url)
  .map((g, i) => ({
    id: i + 1,
    title: ['Weekly Diamond Deals', 'Booyah Bonus Week', 'UC Mega Sale', 'New Version Crystals', 'Kings Festival'][i],
    subtitle: ['Save up to 10% on popular packs.', 'Extra value on every top-up.', 'Grab UC before the season ends.', 'Top up for the new banner.', 'Limited-time token promo.'][i],
    image_url: g.banner_url,
    game_slug: g.slug,
    game: { name: g.name },
    sort_order: i,
    is_active: true,
  }))

export const promos = [0, 2, 3, 5].map((i) => games[i]).flatMap((g) =>
  packagesBySlug[g.slug]
    .filter((p) => p.original_price)
    .slice(0, 1)
    .map((p) => ({ ...p, game: { name: g.name, slug: g.slug, image_url: g.image_url, currency_name: g.currency_name } })),
)

const ml = games[0]
const mlPacks = packagesBySlug['mobile-legends']

export const cart = {
  items: [
    { id: 1, game: { name: ml.name, slug: ml.slug, image_url: ml.image_url }, product: mlPacks[3], quantity: 1, account_data: { user_id: '123456789', zone_id: '1234' }, subtotal: mlPacks[3].price, problem: null },
    { id: 2, game: { name: ml.name, slug: ml.slug, image_url: ml.image_url }, product: mlPacks[8], quantity: 4, account_data: { user_id: '123456789', zone_id: '1234' }, subtotal: mlPacks[8].price * 4, problem: 'insufficient_stock' },
    { id: 3, game: { name: 'Free Fire', slug: 'free-fire', image_url: games[1].image_url }, product: packagesBySlug['free-fire'][1], quantity: 1, account_data: { user_id: '556677889' }, subtotal: 44000, problem: null },
  ],
  get total() { return this.items.reduce((s, i) => s + i.subtotal, 0) },
  get can_checkout() { return this.items.every((i) => !i.problem) },
}

const makeOrder = (n, status, createdAt, items) => ({
  id: n,
  order_number: `TOP-202611${String(10 + n).padStart(2, '0')}-${['7Q3K', 'A9XZ', 'M2PL', 'C4TR', 'Z8QW'][n % 5]}`,
  status,
  total: items.reduce((s, i) => s + i.price * i.quantity, 0),
  created_at: createdAt,
  paid_at: status === 'pending_payment' || status === 'cancelled' ? null : createdAt,
  completed_at: status === 'completed' ? createdAt : null,
  customer: n === 4 ? null : { id: 2, name: 'Rian', email: 'rian@example.com' },
  contact: n === 4 ? { email: 'guest@example.com', whatsapp: '081234567890' } : { email: 'rian@example.com', whatsapp: null },
  items: items.map((i, k) => ({ id: k + 1, ...i })),
  payments: [{ id: n, provider: 'midtrans', provider_reference: 'qris', status: status === 'pending_payment' ? 'pending' : 'settlement', amount: 0, paid_at: null }],
})

export const orders = [
  makeOrder(1, 'pending_payment', '2026-11-18T10:12:00', [{ game_name: 'Mobile Legends', product_name: '344 Diamonds', price: 88000, quantity: 1, account_data: { user_id: '123456789', zone_id: '1234' } }]),
  makeOrder(2, 'paid', '2026-11-17T19:05:00', [{ game_name: 'Genshin Impact', product_name: '878 Genesis Crystals', price: 220000, quantity: 1, account_data: { user_id: '800123456', zone_id: 'Asia' } }]),
  makeOrder(3, 'completed', '2026-11-15T08:40:00', [
    { game_name: 'Free Fire', product_name: '172 Diamonds', price: 44000, quantity: 2, account_data: { user_id: '556677889' } },
    { game_name: 'PUBG Mobile', product_name: '86 UC', price: 22000, quantity: 1, account_data: { user_id: '5123456789' } },
  ]),
  makeOrder(4, 'cancelled', '2026-11-14T21:30:00', [{ game_name: 'Roblox', product_name: '429 Robux', price: 110000, quantity: 1, account_data: { user_id: 'player123' } }]),
]

// Admin lists include inactive rows and package counts.
export const adminGames = games.map((g, i) => ({ ...g, products_count: 10, is_active: g.slug !== 'valorant', sort_order: i }))
export const adminProducts = games.flatMap((g) =>
  packagesBySlug[g.slug].map((p) => ({ ...p, game_id: g.id, game: { name: g.name }, is_active: p.stock !== 0 })),
)

// News & events (DESIGN.md §5.1 Home). No backend table yet — sample articles for layout only.
export const news = [
  { id: 1, tag: 'Event', title: 'Weekend top-up event: bonus Diamonds on every Mobile Legends pack', date: '2026-11-18', image_url: `${ASSET}/banners/mobile-legends.jpg`, game_slug: 'mobile-legends' },
  { id: 2, tag: 'Patch', title: 'Genshin Impact new version: what to prepare before the banner drops', date: '2026-11-17', image_url: `${ASSET}/banners/genshin-impact.jpg`, game_slug: 'genshin-impact' },
  { id: 3, tag: 'Esports', title: 'PUBG Mobile season finals: schedule and how to watch', date: '2026-11-16', image_url: `${ASSET}/banners/pubg-mobile.jpg`, game_slug: 'pubg-mobile' },
  { id: 4, tag: 'Guide', title: 'Free Fire: 5 tips to spend Diamonds wisely this month', date: '2026-11-15', image_url: `${ASSET}/banners/free-fire.jpg`, game_slug: 'free-fire' },
]

// Payment methods (DESIGN.md §3 PaymentMethodPicker). Values match the backend `payment_method` field.
// `logo` = file in public/payments/ (official logos from Wikimedia Commons).
// `strip` = logos shown in the group header row; defaults to the items' own logos.
export const paymentMethods = [
  { group: 'QRIS', strip: ['qris', 'gopay', 'ovo', 'dana', 'shopeepay', 'linkaja', 'bca', 'bni', 'mandiri'], items: [{ value: 'qris', label: 'QRIS', logo: 'qris' }] },
  { group: 'E-wallet', items: [{ value: 'gopay', label: 'GoPay', logo: 'gopay' }, { value: 'shopeepay', label: 'ShopeePay', logo: 'shopeepay' }] },
  { group: 'Virtual account', items: [
    { value: 'bca_va', label: 'BCA', logo: 'bca' }, { value: 'bni_va', label: 'BNI', logo: 'bni' }, { value: 'bri_va', label: 'BRI', logo: 'bri' },
    { value: 'mandiri_va', label: 'Mandiri', logo: 'mandiri' }, { value: 'permata_va', label: 'Permata', logo: 'permata' },
  ] },
  { group: 'Convenience store', items: [{ value: 'indomaret', label: 'Indomaret', logo: 'indomaret' }, { value: 'alfamart', label: 'Alfamart', logo: 'alfamart' }] },
]
export const paymentLabel = (value) => paymentMethods.flatMap((g) => g.items).find((m) => m.value === value)?.label.split(' · ')[0]

// Admin dashboard (GET /admin/dashboard).
export const dashboard = {
  today: { revenue: 1_284_000, orders: 37 },
  yesterday: { revenue: 1_102_000, orders: 41 },
  pending_payment: 6,
  to_process: 2,
  revenue_7d: [
    { date: '2026-11-12', revenue: 820000 }, { date: '2026-11-13', revenue: 1040000 }, { date: '2026-11-14', revenue: 760000 },
    { date: '2026-11-15', revenue: 1390000 }, { date: '2026-11-16', revenue: 1510000 }, { date: '2026-11-17', revenue: 1102000 },
    { date: '2026-11-18', revenue: 1284000 },
  ],
  status_counts: { pending_payment: 6, paid: 2, completed: 182, cancelled: 14 },
  top_games: [
    { name: 'Mobile Legends', items_sold: 412, percent: 38.2 },
    { name: 'Free Fire', items_sold: 251, percent: 23.3 },
    { name: 'PUBG Mobile', items_sold: 168, percent: 15.6 },
    { name: 'Genshin Impact', items_sold: 109, percent: 10.1 },
    { name: 'Roblox', items_sold: 82, percent: 7.6 },
    { name: 'Honor of Kings', items_sold: 56, percent: 5.2 },
  ],
}
