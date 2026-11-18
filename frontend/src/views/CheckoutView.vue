<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ShieldCheck, AlertTriangle } from 'lucide-vue-next'
import AppImage from '@/components/ui/AppImage.vue'
import PaymentMethodPicker from '@/components/store/PaymentMethodPicker.vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { formatAccount, formatMoney } from '@/utils/format'

const route = useRoute()
const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()
const buyNow = computed(() => route.query.mode === 'buy-now')
const placing = ref(false)
// Pre-filled from the game page for Buy now; picked here for cart checkout.
const paymentMethod = ref(cart.buyNowItem?.payment_method ?? null)

const items = computed(() =>
  buyNow.value
    ? cart.buyNowItem ? [{ id: 'buy-now', ...cart.buyNowItem }] : []
    : cart.items,
)
const total = computed(() => items.value.reduce((s, i) => s + i.product.price * i.quantity, 0))

// Nothing to pay for (e.g. refresh cleared the Buy now item) → go back.
onMounted(() => {
  // The cart needs an account; Buy now works for guests.
  if (!buyNow.value && !auth.isSignedIn) return router.replace({ name: 'login', query: { redirect: route.fullPath } })
  if (!items.value.length) router.replace(buyNow.value ? '/' : '/cart')
})

async function placeOrder() {
  placing.value = true
  // TODO: API — POST /orders { source: 'cart', payment_method } or
  // { source: 'buy_now', product_id, quantity, account_data, payment_method, contact_email, contact_whatsapp }.
  // Then open window.snap.pay(snap_token) and, when the popup closes, go to /orders/{order_number}.
  await new Promise((r) => setTimeout(r, 600))
  placing.value = false
  const number = 'TOP-20261111-A9XZ' // from response.order.order_number
  // Guests track by number + email; signed-in buyers see it in My orders.
  if (auth.isSignedIn) router.push({ name: 'order', params: { number } })
  else router.push({ name: 'track', query: { number, email: cart.buyNowItem?.contact?.email } })
}
</script>

<template>
  <div class="container-app max-w-3xl pt-6">
    <h1 class="mb-6 text-[28px] font-bold tracking-tight">Review &amp; pay</h1>

    <p class="mb-4 flex gap-2.5 rounded-btn border border-warning/25 bg-warning/8 p-3 text-sm">
      <AlertTriangle class="mt-0.5 size-4 shrink-0 text-warning" aria-hidden="true" />
      Please double-check your account details. Items are sent to these accounts.
    </p>

    <ul class="card divide-y divide-line">
      <li v-for="item in items" :key="item.id" class="flex items-center gap-3 p-4">
        <div class="size-12 shrink-0 overflow-hidden rounded-img"><AppImage :src="item.game.image_url" :alt="item.game.name" /></div>
        <div class="min-w-0 flex-1">
          <p class="text-[13px] text-muted">{{ item.game.name }}</p>
          <p class="font-semibold">{{ item.product.name }} <span class="text-muted">× {{ item.quantity }}</span></p>
          <p class="truncate font-mono text-[13px] text-primary">{{ formatAccount(item.account_data) }}</p>
        </div>
        <span class="price">{{ formatMoney(item.product.price * item.quantity) }}</span>
      </li>
    </ul>

    <section class="card mt-4 space-y-3 p-5" aria-labelledby="pay-title">
      <h2 id="pay-title" class="text-lg font-semibold">Payment method</h2>
      <PaymentMethodPicker v-model="paymentMethod" :total="total" />
    </section>

    <div class="card mt-4 space-y-4 p-5">
      <div class="flex items-baseline justify-between">
        <span class="text-muted">Total</span>
        <span class="price text-[26px]">{{ formatMoney(total) }}</span>
      </div>
      <button type="button" class="btn-primary w-full" :disabled="placing || !items.length || !paymentMethod" @click="placeOrder">
        {{ placing ? 'Placing order…' : 'Place order & pay' }}
      </button>
      <p class="flex items-center justify-center gap-1.5 text-[13px] text-muted">
        <ShieldCheck class="size-4" aria-hidden="true" />Secure payment by Midtrans
      </p>
    </div>
  </div>
</template>
