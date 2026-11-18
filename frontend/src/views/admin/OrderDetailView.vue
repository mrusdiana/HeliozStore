<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { Copy, Check } from 'lucide-vue-next'
import PageHeader from '@/components/admin/PageHeader.vue'
import OrderStatusBadge from '@/components/ui/OrderStatusBadge.vue'
import StatusState from '@/components/ui/StatusState.vue'
import { orders } from '@/data/mock'
import { formatAccount, formatDate, formatMoney } from '@/utils/format'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const toast = useToastStore()
// TODO: API — GET /admin/orders/{number}.
const order = computed(() => orders.find((o) => o.order_number === route.params.number))
const copied = ref(null)

async function copy(item) {
  await navigator.clipboard?.writeText(Object.values(item.account_data).join(' '))
  copied.value = item.id
  setTimeout(() => (copied.value = null), 1500)
}
function complete() {
  // TODO: API — POST /admin/orders/{number}/complete, then reload.
  toast.show('Order marked as completed')
}
</script>

<template>
  <PageHeader :title="order?.order_number ?? 'Order'" back-to="/admin/orders" />
  <StatusState v-if="!order" title="We couldn't find that." />
  <div v-else class="grid gap-4 lg:grid-cols-[1fr_320px] lg:items-start">
    <section class="card" aria-label="Items">
      <ul class="divide-y divide-line">
        <li v-for="i in order.items" :key="i.id" class="flex flex-wrap items-center gap-3 p-4">
          <div class="min-w-0 flex-1">
            <p class="text-[13px] text-muted">{{ i.game_name }}</p>
            <p class="font-semibold">{{ i.product_name }} × {{ i.quantity }}</p>
            <p class="font-mono text-sm">{{ formatAccount(i.account_data) }}</p>
          </div>
          <button type="button" class="btn-secondary min-h-10 px-3 text-xs" :aria-label="`Copy account for ${i.product_name}`" @click="copy(i)">
            <Check v-if="copied === i.id" class="size-4 text-success" aria-hidden="true" /><Copy v-else class="size-4" aria-hidden="true" />
            {{ copied === i.id ? 'Copied' : 'Copy' }}
          </button>
          <span class="price w-full text-right sm:w-auto">{{ formatMoney(i.price * i.quantity) }}</span>
        </li>
      </ul>
      <div class="flex items-baseline justify-between border-t border-line p-4">
        <span class="text-muted">Total</span><span class="price text-[22px]">{{ formatMoney(order.total) }}</span>
      </div>
    </section>

    <aside class="card space-y-4 p-5">
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-2"><dt class="text-muted">Status</dt><dd><OrderStatusBadge :status="order.status" /></dd></div>
        <div class="flex justify-between gap-2"><dt class="text-muted">Buyer</dt><dd class="text-right">{{ order.customer?.name ?? 'Guest' }}<br /><span class="text-muted">{{ order.contact.email }}</span><template v-if="order.contact.whatsapp"><br /><span class="font-mono text-muted">{{ order.contact.whatsapp }}</span></template></dd></div>
        <div class="flex justify-between gap-2"><dt class="text-muted">Created</dt><dd>{{ formatDate(order.created_at) }}</dd></div>
        <div class="flex justify-between gap-2"><dt class="text-muted">Paid</dt><dd>{{ formatDate(order.paid_at) || '—' }}</dd></div>
        <div class="flex justify-between gap-2"><dt class="text-muted">Payment</dt><dd class="font-mono uppercase">{{ order.payments.at(-1)?.status ?? "—" }}</dd></div>
      </dl>
      <button v-if="order.status === 'paid'" type="button" class="btn-primary w-full" @click="complete">Mark as completed</button>
    </aside>
  </div>
</template>
