<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ShoppingCart, ChevronLeft, Minus, Plus, Zap, MessageCircle, ShieldCheck } from 'lucide-vue-next'
import AppImage from '@/components/ui/AppImage.vue'
import ItemIcon from '@/components/ui/ItemIcon.vue'
import StatusState from '@/components/ui/StatusState.vue'
import StepCard from '@/components/store/StepCard.vue'
import AccountForm from '@/components/store/AccountForm.vue'
import PackageGrid from '@/components/store/PackageGrid.vue'
import PaymentMethodPicker from '@/components/store/PaymentMethodPicker.vue'
import { games, packagesBySlug, paymentLabel } from '@/data/mock'
import { formatAccount, formatMoney } from '@/utils/format'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const cart = useCartStore()
const toast = useToastStore()

// TODO: API — GET /games/{slug} (404 → not found state). No banner_url → the header uses the blurred icon.
const loading = ref(false)
const game = computed(() => games.find((g) => g.slug === route.params.slug))
const packages = computed(() => packagesBySlug[route.params.slug] ?? [])

const account = reactive({})
const selectedId = ref(Number(route.query.package) || null)
const quantity = ref(1)
const paymentMethod = ref(null)
const contact = reactive({ email: auth.user?.email ?? '', whatsapp: '' })
const adding = ref(false)
const serverErrors = ref({}) // fill from 422 `errors`

watch(() => route.params.slug, () => {
  Object.keys(account).forEach((k) => delete account[k])
  selectedId.value = Number(route.query.package) || null
  quantity.value = 1
})
watch(selectedId, () => (quantity.value = 1))
watch(() => auth.user, (u) => u && !contact.email && (contact.email = u.email))

const selected = computed(() => packages.value.find((p) => p.id === selectedId.value))
const maxQty = computed(() => Math.min(selected.value?.stock ?? 1, 100))
const accountValid = computed(() =>
  game.value?.account_fields.every((f) => {
    const v = (account[f.key] ?? '').trim()
    return v.length >= f.min && v.length <= f.max && (f.type !== 'numeric' || /^\d+$/.test(v))
  }),
)
const emailValid = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contact.email.trim()))
const whatsappValid = computed(() => !contact.whatsapp || /^\+?\d{9,15}$/.test(contact.whatsapp.trim()))
const ready = computed(() => accountValid.value && !!selected.value)
const canBuy = computed(() => ready.value && !!paymentMethod.value && emailValid.value && whatsappValid.value)
const total = computed(() => (selected.value?.price ?? 0) * quantity.value)
const missing = computed(() =>
  !accountValid.value ? 'Fill in your account details.'
    : !selected.value ? 'Pick a package.'
      : !paymentMethod.value ? 'Pick a payment method.'
        : !emailValid.value ? 'Enter your email so we can send the receipt.'
          : !whatsappValid.value ? 'Check your WhatsApp number.' : '',
)

function setQty(v) {
  const n = Number.parseInt(v, 10)
  quantity.value = Number.isNaN(n) ? 1 : Math.min(Math.max(n, 1), maxQty.value)
}

function buyNow() {
  cart.buyNowItem = {
    game: game.value,
    product: selected.value,
    quantity: quantity.value,
    account_data: { ...account },
    payment_method: paymentMethod.value,
    contact: { email: contact.email.trim(), whatsapp: contact.whatsapp.trim() },
  }
  router.push({ name: 'checkout', query: { mode: 'buy-now' } })
}

async function addToCart() {
  if (!auth.isSignedIn) return router.push({ name: 'login', query: { redirect: route.fullPath } })
  adding.value = true
  // TODO: API — POST /cart/items { product_id, quantity, account_data }, then cart.items = response.items.
  await new Promise((r) => setTimeout(r, 400))
  adding.value = false
  toast.show('Added to cart')
}

const perks = [
  { icon: Zap, label: 'Fast delivery' },
  { icon: MessageCircle, label: '24/7 chat support' },
  { icon: ShieldCheck, label: 'Secure payment' },
]
</script>

<template>
  <div v-if="loading" class="container-app space-y-6 pt-6">
    <div class="skeleton h-48" />
    <div class="skeleton h-64" />
  </div>

  <div v-else-if="!game" class="container-app pt-10">
    <StatusState title="We couldn't find that." text="This game may be unavailable right now.">
      <RouterLink to="/" class="btn-secondary mt-2">Browse games</RouterLink>
    </StatusState>
  </div>

  <div v-else class="pb-28 lg:pb-0">
    <!-- Full-width header: banner, or the blurred icon when the game has no banner -->
    <header class="relative isolate overflow-hidden">
      <!-- The mask fades the whole background (image + overlays) to transparent, so it blends into the page with no edge -->
      <div class="absolute inset-0 -z-10 [mask-image:linear-gradient(to_bottom,black_45%,transparent)]">
        <AppImage v-if="game.banner_url" :src="game.banner_url" :alt="''" eager class="object-[center_20%]" />
        <AppImage v-else :src="game.image_url" :alt="''" eager class="scale-125 blur-2xl saturate-150" />
        <div class="absolute inset-0 bg-[linear-gradient(90deg,#05080F_5%,rgba(5,8,15,0.8)_45%,rgba(5,8,15,0.35))]" />
      </div>
      <div class="container-app pt-6 pb-10 sm:pt-10 sm:pb-16">
        <RouterLink to="/" class="mb-2 inline-flex min-h-11 items-center gap-1 text-sm font-semibold text-text/70 hover:text-text">
          <ChevronLeft class="size-4" aria-hidden="true" />All games
        </RouterLink>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
          <div class="flex min-w-0 flex-1 items-center gap-4 sm:gap-6">
            <div class="size-16 shrink-0 overflow-hidden rounded-[16px] border border-white/10 shadow-2xl shadow-black/50 sm:size-28 sm:rounded-[20px]">
              <AppImage :src="game.image_url" :alt="game.name" eager />
            </div>
            <div class="min-w-0">
              <span class="inline-block rounded-md border border-white/10 bg-black/30 px-2 py-0.5 font-mono text-[10px] tracking-wider text-text/80 uppercase backdrop-blur sm:text-[11px]">
                {{ game.genre }} · {{ game.currency_name }}
              </span>
              <h1 class="mt-1.5 text-[22px] leading-tight font-bold tracking-tight sm:text-4xl">{{ game.name }}</h1>
              <p class="mt-0.5 text-[13px] text-text/75 sm:text-base">Instant delivery · arrives in 1–3 seconds</p>
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <span class="flex items-center gap-1.5 rounded-full border border-white/10 bg-black/30 px-3 py-1.5 text-xs font-semibold text-success backdrop-blur">
              <span class="size-1.5 rounded-full bg-success" aria-hidden="true" />Online 24/7
            </span>
            <span v-for="p in perks" :key="p.label" class="flex items-center gap-1.5 rounded-full border border-white/10 bg-black/30 px-3 py-1.5 text-xs font-semibold backdrop-blur">
              <component :is="p.icon" class="size-3.5 text-primary-hover" aria-hidden="true" />{{ p.label }}
            </span>
          </div>
        </div>
      </div>
    </header>

    <div class="container-app grid gap-4 sm:mt-2 sm:gap-5 lg:grid-cols-[1fr_360px] lg:items-start">
      <div class="space-y-4 sm:space-y-5">
        <StepCard :step="1" title="Account details" note="No password needed">
          <AccountForm v-model="account" :fields="game.account_fields" :instructions="game.account_instructions" :server-errors="serverErrors" :show-errors="!!paymentMethod" />
        </StepCard>

        <StepCard :step="2" :title="`Select ${game.currency_name}`" note="Tax included">
          <PackageGrid v-model="selectedId" :packages="packages" :currency="game.currency_name" />
        </StepCard>

        <StepCard :step="3" title="Quantity">
          <div class="flex gap-2">
            <label class="flex-1">
              <span class="sr-only">Quantity</span>
              <input
                :value="quantity"
                type="number"
                inputmode="numeric"
                min="1"
                :max="maxQty"
                class="field font-mono"
                :disabled="!selected"
                @change="setQty($event.target.value)"
              />
            </label>
            <button type="button" class="btn-primary btn-icon" :disabled="!selected || quantity >= maxQty" aria-label="Increase quantity" @click="setQty(quantity + 1)">
              <Plus class="size-5" />
            </button>
            <button type="button" class="btn-secondary btn-icon" :disabled="!selected || quantity <= 1" aria-label="Decrease quantity" @click="setQty(quantity - 1)">
              <Minus class="size-5" />
            </button>
          </div>
          <p class="hint">{{ selected ? `Max ${maxQty} per order for this package.` : 'Pick a package first.' }}</p>
        </StepCard>

        <StepCard :step="4" title="Payment method" note="No admin fee">
          <PaymentMethodPicker v-model="paymentMethod" :total="total" />
        </StepCard>

        <StepCard :step="5" title="Contact details" :note="auth.isSignedIn ? 'From your account' : 'For your receipt'">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="contact-email" class="label">Email</label>
              <input
                id="contact-email"
                v-model="contact.email"
                type="email"
                autocomplete="email"
                class="field"
                placeholder="you@example.com"
                :aria-invalid="!!contact.email && !emailValid"
                aria-describedby="contact-email-msg"
              />
              <p id="contact-email-msg" :class="contact.email && !emailValid ? 'error-text' : 'hint'">
                {{ contact.email && !emailValid ? 'Enter a valid email.' : 'Use it with the order number to track your order.' }}
              </p>
            </div>
            <div>
              <label for="contact-wa" class="label">WhatsApp <span class="font-normal text-muted">(optional)</span></label>
              <input
                id="contact-wa"
                v-model="contact.whatsapp"
                type="tel"
                inputmode="tel"
                autocomplete="tel"
                class="field font-mono"
                placeholder="081234567890"
                :aria-invalid="!whatsappValid"
                aria-describedby="contact-wa-msg"
              />
              <p id="contact-wa-msg" :class="whatsappValid ? 'hint' : 'error-text'">
                {{ whatsappValid ? "We'll message you only if there's a problem." : 'Use 9–15 digits, e.g. 081234567890.' }}
              </p>
            </div>
          </div>
          <p v-if="!auth.isSignedIn" class="mt-4 text-[13px] text-muted">
            Buying as a guest.
            <RouterLink :to="{ name: 'login', query: { redirect: route.fullPath } }" class="font-semibold text-primary-hover hover:underline">Sign in</RouterLink>
            to use the cart and see your order history.
          </p>
        </StepCard>
      </div>

      <!-- Desktop: sticky order summary -->
      <aside class="card sticky top-24 hidden space-y-5 p-6 lg:block" aria-label="Order summary">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold tracking-tight">Order summary</h2>
          <span class="rounded-md border border-line bg-surface-2 px-2 py-1 font-mono text-[11px] tracking-wider text-muted uppercase">Instant</span>
        </div>
        <div class="flex items-center gap-3 rounded-btn border border-line bg-bg/60 p-3">
          <div class="size-12 shrink-0 overflow-hidden rounded-[10px]"><AppImage :src="game.image_url" :alt="game.name" /></div>
          <div class="min-w-0">
            <p class="truncate font-semibold">{{ game.name }}</p>
            <p class="flex items-center gap-1.5 truncate text-sm font-semibold text-primary-hover">
              <ItemIcon v-if="selected" :currency="game.currency_name" class="size-4" />{{ selected ? `${selected.name} × ${quantity}` : 'No package yet' }}
            </p>
            <p class="truncate font-mono text-xs text-muted">{{ formatAccount(account) || 'No account yet' }}</p>
          </div>
        </div>
        <dl class="space-y-2 border-t border-line pt-4 text-sm">
          <div class="flex justify-between"><dt class="text-muted">Item price</dt><dd class="price">{{ formatMoney(total) }}</dd></div>
          <div class="flex justify-between"><dt class="text-muted">Admin fee{{ paymentMethod ? ` (${paymentLabel(paymentMethod)})` : '' }}</dt><dd class="price text-success">Rp 0</dd></div>
        </dl>
        <div class="flex items-baseline justify-between border-t border-line pt-4">
          <span class="text-sm text-muted">Total</span>
          <span class="price text-[28px]" aria-live="polite">{{ formatMoney(total) }}</span>
        </div>
        <button type="button" class="btn-primary min-h-13 w-full text-base" :disabled="!canBuy" @click="buyNow">Buy now</button>
        <button v-if="auth.isSignedIn" type="button" class="btn-secondary w-full" :disabled="!ready || adding" @click="addToCart">
          <ShoppingCart class="size-4" aria-hidden="true" />{{ adding ? 'Adding…' : 'Add to cart' }}
        </button>
        <p v-if="missing" class="text-center text-[13px] text-muted">{{ missing }}</p>
      </aside>
    </div>

    <!-- Mobile: glass bottom bar -->
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-glass pb-[env(safe-area-inset-bottom)] backdrop-blur-xl lg:hidden">
      <div class="container-app flex items-center gap-3 py-3">
        <div class="min-w-0 flex-1">
          <p class="truncate text-[13px] text-muted">{{ selected ? `${selected.name} × ${quantity}` : missing }}</p>
          <p class="price text-lg" aria-live="polite">{{ formatMoney(total) }}</p>
        </div>
        <button v-if="auth.isSignedIn" type="button" class="btn-secondary btn-icon" :disabled="!ready || adding" aria-label="Add to cart" @click="addToCart">
          <ShoppingCart class="size-5" />
        </button>
        <button type="button" class="btn-primary px-6" :disabled="!canBuy" @click="buyNow">Buy now</button>
      </div>
    </div>
  </div>
</template>
