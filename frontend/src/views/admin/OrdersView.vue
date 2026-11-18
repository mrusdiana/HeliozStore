<script setup>
import { computed, ref } from 'vue'
import PageHeader from '@/components/admin/PageHeader.vue'
import OrderStatusBadge from '@/components/ui/OrderStatusBadge.vue'
import StatusState from '@/components/ui/StatusState.vue'
import { orders } from '@/data/mock'
import { ORDER_STATUS, formatDate, formatMoney } from '@/utils/format'

// TODO: API — GET /admin/orders?status=&search=&page= (refetch when filters change).
const status = ref('')
const search = ref('')
const list = computed(() =>
  orders.filter((o) => (!status.value || o.status === status.value) && o.order_number.includes(search.value.toUpperCase())),
)
</script>

<template>
  <PageHeader title="Orders" />
  <div class="mb-4 flex flex-col gap-3 sm:flex-row">
    <label class="sm:max-w-xs sm:flex-1">
      <span class="sr-only">Search order number</span>
      <input v-model="search" type="search" class="field" placeholder="Search order number" />
    </label>
    <label class="sm:w-48">
      <span class="sr-only">Filter by status</span>
      <select v-model="status" class="field">
        <option value="">All statuses</option>
        <option v-for="(s, key) in ORDER_STATUS" :key="key" :value="key">{{ s.label }}</option>
      </select>
    </label>
  </div>

  <StatusState v-if="!list.length" title="No orders found" />
  <template v-else>
    <!-- Desktop table -->
    <div class="card hidden overflow-x-auto md:block">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-line text-xs tracking-wider text-muted uppercase">
          <tr><th class="p-4">Order</th><th class="p-4">Date</th><th class="p-4">Buyer</th><th class="p-4 text-right">Total</th><th class="p-4">Status</th></tr>
        </thead>
        <tbody class="divide-y divide-line">
          <tr v-for="o in list" :key="o.id" class="transition hover:bg-surface-2/60">
            <td class="p-4"><RouterLink :to="`/admin/orders/${o.order_number}`" class="font-mono font-semibold text-primary hover:underline">{{ o.order_number }}</RouterLink></td>
            <td class="p-4 text-muted">{{ formatDate(o.created_at) }}</td>
            <td class="p-4">{{ (o.customer?.name ?? 'Guest') }}</td>
            <td class="price p-4 text-right">{{ formatMoney(o.total) }}</td>
            <td class="p-4"><OrderStatusBadge :status="o.status" /></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Mobile cards -->
    <ul class="space-y-3 md:hidden">
      <li v-for="o in list" :key="o.id">
        <RouterLink :to="`/admin/orders/${o.order_number}`" class="card block space-y-2 p-4">
          <div class="flex items-center justify-between gap-2"><span class="font-mono text-sm font-semibold">{{ o.order_number }}</span><OrderStatusBadge :status="o.status" /></div>
          <div class="flex items-center justify-between text-sm"><span class="text-muted">{{ (o.customer?.name ?? 'Guest') }} · {{ formatDate(o.created_at) }}</span><span class="price">{{ formatMoney(o.total) }}</span></div>
        </RouterLink>
      </li>
    </ul>
  </template>
</template>
