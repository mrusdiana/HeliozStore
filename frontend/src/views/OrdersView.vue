<script setup>
import { ref } from 'vue'
import { ChevronRight } from 'lucide-vue-next'
import OrderStatusBadge from '@/components/ui/OrderStatusBadge.vue'
import StatusState from '@/components/ui/StatusState.vue'
import { orders } from '@/data/mock'
import { formatDate, formatMoney } from '@/utils/format'

// TODO: API — GET /orders?page= → { data, page, last_page, total }.
const loading = ref(false)
const summary = (o) => o.items.map((i) => `${i.product_name} × ${i.quantity}`).join(', ')
</script>

<template>
  <div class="container-app max-w-3xl pt-6">
    <h1 class="mb-6 text-[28px] font-bold tracking-tight">My orders</h1>
    <div v-if="loading" class="space-y-3"><div v-for="n in 4" :key="n" class="skeleton h-20" /></div>
    <StatusState v-else-if="!orders.length" title="No orders yet">
      <RouterLink to="/" class="btn-primary mt-2">Browse games</RouterLink>
    </StatusState>
    <ul v-else class="space-y-3">
      <li v-for="o in orders" :key="o.id">
        <RouterLink :to="{ name: 'order', params: { number: o.order_number } }" class="card flex items-center gap-3 p-4 transition hover:border-specular">
          <div class="min-w-0 flex-1 space-y-1">
            <div class="flex flex-wrap items-center gap-2">
              <span class="font-mono text-sm font-semibold">{{ o.order_number }}</span>
              <OrderStatusBadge :status="o.status" />
            </div>
            <p class="truncate text-sm">{{ summary(o) }}</p>
            <p class="text-[13px] text-muted">{{ formatDate(o.created_at) }}</p>
          </div>
          <span class="price">{{ formatMoney(o.total) }}</span>
          <ChevronRight class="size-4 shrink-0 text-muted" aria-hidden="true" />
        </RouterLink>
      </li>
    </ul>
  </div>
</template>
