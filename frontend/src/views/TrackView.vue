<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Search } from 'lucide-vue-next'
import OrderPanel from '@/components/store/OrderPanel.vue'
import { orders } from '@/data/mock'

const route = useRoute()
const router = useRouter()
const form = reactive({ number: route.query.number ?? '', email: route.query.email ?? '' })
const order = ref(null)
const error = ref('')
const searching = ref(false)

async function search() {
  error.value = ''
  searching.value = true
  router.replace({ query: { number: form.number.trim(), email: form.email.trim() } })
  // TODO: API — GET /orders/track?number=&email= (wrapped in `data`); 404 → show the error below. Poll every 3s while 'paid'.
  await new Promise((r) => setTimeout(r, 400))
  order.value = orders.find((o) => o.order_number === form.number.trim().toUpperCase()) ?? null
  if (!order.value) error.value = "We couldn't find an order with that number and email."
  searching.value = false
}

async function check(done) {
  await search() // TODO: API — the track endpoint already re-checks Midtrans.
  done()
}
function pay() {
  // TODO: API — POST /orders/track/pay { number, email } → window.snap.pay(snap_token).
}

// Coming back from checkout as a guest fills the form; search right away.
onMounted(() => form.number && form.email && search())
</script>

<template>
  <div class="container-app max-w-3xl space-y-6 pt-6">
    <div>
      <h1 class="text-[28px] font-bold tracking-tight">Track order</h1>
      <p class="text-sm text-muted">Enter the order number from your receipt and the email you used at checkout.</p>
    </div>

    <form class="card grid gap-4 p-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end sm:p-6" @submit.prevent="search">
      <div>
        <label for="track-number" class="label">Order number</label>
        <input id="track-number" v-model="form.number" class="field font-mono uppercase" placeholder="TOP-20261118-7Q3K" required autocomplete="off" />
      </div>
      <div>
        <label for="track-email" class="label">Email</label>
        <input id="track-email" v-model="form.email" type="email" class="field" placeholder="you@example.com" required autocomplete="email" />
      </div>
      <button type="submit" class="btn-primary" :disabled="searching">
        <Search class="size-4" aria-hidden="true" />{{ searching ? 'Searching…' : 'Track' }}
      </button>
      <p v-if="error" class="error-text sm:col-span-3" role="alert">{{ error }}</p>
    </form>

    <OrderPanel v-if="order" :order="order" @check="check" @pay="pay" />
  </div>
</template>
