<script setup>
import { ref } from 'vue'
import { Loader2, PackageCheck, XCircle } from 'lucide-vue-next'
import OrderTimeline from './OrderTimeline.vue'
import OrderStatusBadge from '@/components/ui/OrderStatusBadge.vue'
import { formatAccount, formatDate, formatMoney } from '@/utils/format'

// Shared by /orders/:number (signed in) and /track (guest). The parent decides which API to call.
defineProps({ order: { type: Object, required: true } })
const emit = defineEmits(['pay', 'check'])
const checking = ref(false)

async function check() {
  checking.value = true
  emit('check', () => (checking.value = false))
}
</script>

<template>
  <div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <div>
        <p class="text-[13px] text-muted">{{ formatDate(order.created_at) }}</p>
        <h1 class="font-mono text-xl font-semibold sm:text-2xl">{{ order.order_number }}</h1>
      </div>
      <OrderStatusBadge :status="order.status" />
    </div>

    <div class="card p-5"><OrderTimeline :status="order.status" /></div>

    <div aria-live="polite">
      <div v-if="order.status === 'pending_payment'" class="card flex flex-col gap-3 p-5 sm:flex-row sm:items-center">
        <p class="flex-1 text-sm">Waiting for your payment.</p>
        <button type="button" class="btn-secondary" :disabled="checking" @click="check">{{ checking ? 'Checking…' : 'Check payment status' }}</button>
        <button type="button" class="btn-primary" @click="$emit('pay')">Pay now</button>
      </div>
      <p v-else-if="order.status === 'paid'" class="card flex items-center gap-2 p-5 text-sm text-primary-hover">
        <Loader2 class="size-4 animate-spin" aria-hidden="true" />Delivering your items…
      </p>
      <p v-else-if="order.status === 'completed'" class="card flex items-center gap-2 p-5 text-sm text-success">
        <PackageCheck class="size-4" aria-hidden="true" />Items delivered
      </p>
      <p v-else class="card flex items-center gap-2 p-5 text-sm text-danger">
        <XCircle class="size-4" aria-hidden="true" />Payment expired
      </p>
    </div>

    <section class="card" aria-label="Items">
      <ul class="divide-y divide-line">
        <li v-for="i in order.items" :key="i.id" class="flex items-center gap-3 p-4">
          <div class="min-w-0 flex-1">
            <p class="text-[13px] text-muted">{{ i.game_name }}</p>
            <p class="font-semibold">{{ i.product_name }} <span class="text-muted">× {{ i.quantity }}</span></p>
            <p class="truncate font-mono text-[13px]">{{ formatAccount(i.account_data) }}</p>
          </div>
          <span class="price">{{ formatMoney(i.price * i.quantity) }}</span>
        </li>
      </ul>
      <div class="flex items-baseline justify-between border-t border-line p-4">
        <span class="text-muted">Total</span>
        <span class="price text-[26px]">{{ formatMoney(order.total) }}</span>
      </div>
    </section>
  </div>
</template>
