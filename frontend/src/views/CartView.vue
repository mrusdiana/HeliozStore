<script setup>
import { computed, ref } from 'vue'
import { ShoppingCart } from 'lucide-vue-next'
import CartItem from '@/components/store/CartItem.vue'
import StatusState from '@/components/ui/StatusState.vue'
import { useCartStore } from '@/stores/cart'
import { formatMoney } from '@/utils/format'

const cart = useCartStore()
// TODO: API — GET /cart on mount; use `total` and `can_checkout` from the response instead of computing here.
const loading = ref(false)

const groups = computed(() => {
  const map = {}
  for (const item of cart.items) (map[item.game.slug] ??= { game: item.game, items: [] }).items.push(item)
  return Object.values(map)
})
const total = computed(() => cart.items.reduce((s, i) => s + i.product.price * i.quantity, 0))
const canCheckout = computed(() => cart.items.length && cart.items.every((i) => !i.problem))

function setQty(item, q) {
  // TODO: API — PATCH /cart/items/{id} { quantity } and replace cart.items with the response.
  item.quantity = q
  item.problem = q > item.product.stock ? 'insufficient_stock' : null
}
function remove(item) {
  // TODO: API — DELETE /cart/items/{id}.
  cart.items = cart.items.filter((i) => i.id !== item.id)
}
</script>

<template>
  <div class="container-app pt-6">
    <h1 class="mb-6 text-[28px] font-bold tracking-tight">Cart</h1>

    <div v-if="loading" class="space-y-3"><div v-for="n in 3" :key="n" class="skeleton h-24" /></div>

    <StatusState v-else-if="!cart.items.length" title="Your cart is empty." text="Pick a game and add a package to get started.">
      <RouterLink to="/" class="btn-primary mt-2"><ShoppingCart class="size-4" aria-hidden="true" />Browse games</RouterLink>
    </StatusState>

    <div v-else class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start">
      <div class="space-y-4">
        <section v-for="g in groups" :key="g.game.slug" class="card overflow-hidden">
          <h2 class="border-b border-line px-4 py-3 text-base font-semibold">{{ g.game.name }}</h2>
          <ul class="divide-y divide-line">
            <CartItem v-for="item in g.items" :key="item.id" :item="item" @update:quantity="setQty(item, $event)" @remove="remove(item)" />
          </ul>
        </section>
      </div>

      <aside class="card sticky top-24 space-y-4 p-5" aria-label="Cart total">
        <div class="flex items-baseline justify-between">
          <span class="text-muted">Total</span>
          <span class="price text-[26px]" aria-live="polite">{{ formatMoney(total) }}</span>
        </div>
        <RouterLink v-if="canCheckout" to="/checkout" class="btn-primary w-full">Checkout</RouterLink>
        <template v-else>
          <button type="button" class="btn-primary w-full" disabled>Checkout</button>
          <p class="text-center text-[13px] text-danger">Fix the highlighted items to continue.</p>
        </template>
      </aside>
    </div>
  </div>
</template>
