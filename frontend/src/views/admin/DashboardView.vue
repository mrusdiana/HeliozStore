<script setup>
import { computed } from 'vue'
import { Wallet, ShoppingBag, Clock, PackageCheck, CalendarDays, ChevronRight } from 'lucide-vue-next'
import StatCard from '@/components/admin/StatCard.vue'
import ColumnChart from '@/components/admin/ColumnChart.vue'
import BarList from '@/components/admin/BarList.vue'
import OrderStatusBadge from '@/components/ui/OrderStatusBadge.vue'
import { dashboard as d, orders } from '@/data/mock'
import { ORDER_STATUS, formatDate, formatMoney } from '@/utils/format'

// TODO: API — GET /admin/dashboard (recent orders come in `recent_orders`).
const todayLabel = new Intl.DateTimeFormat('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date())
const shortMoney = (v) => (v >= 1e6 ? `${(v / 1e6).toFixed(1)}jt` : v >= 1e3 ? `${Math.round(v / 1e3)}rb` : String(v))
const revenue = computed(() =>
  d.revenue_7d.map((r) => {
    const date = new Date(r.date)
    return { label: formatDate(r.date).split(',')[0], short: date.toLocaleDateString('en-GB', { weekday: 'short' }), value: r.revenue }
  }),
)
const weekTotal = computed(() => d.revenue_7d.reduce((s, r) => s + r.revenue, 0))
const statusTotal = computed(() => Object.values(d.status_counts).reduce((a, b) => a + b, 0))
const statusRows = computed(() =>
  Object.entries(ORDER_STATUS).map(([key, s]) => ({ key, ...s, count: d.status_counts[key] ?? 0, pct: Math.round(((d.status_counts[key] ?? 0) / statusTotal.value) * 100) })),
)
const toneBar = { warning: 'bg-warning', primary: 'bg-primary', success: 'bg-success', danger: 'bg-danger' }
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
      <div>
        <p class="flex items-center gap-1.5 text-sm text-muted"><CalendarDays class="size-4" aria-hidden="true" />{{ todayLabel }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-[28px]">Dashboard</h1>
      </div>
      <RouterLink to="/admin/orders" class="btn-secondary">View all orders</RouterLink>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
      <StatCard label="Revenue today" :value="formatMoney(d.today.revenue)" :icon="Wallet" :current="d.today.revenue" :previous="d.yesterday.revenue" />
      <StatCard label="Orders today" :value="String(d.today.orders)" :icon="ShoppingBag" :current="d.today.orders" :previous="d.yesterday.orders" />
      <StatCard label="Waiting for payment" :value="String(d.pending_payment)" :icon="Clock" note="Unpaid orders" />
      <StatCard label="To process" :value="String(d.to_process)" :icon="PackageCheck" note="Paid, not completed yet" />
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.6fr_1fr]">
      <section class="card p-4 sm:p-6" aria-labelledby="rev-title">
        <div class="mb-6 flex flex-wrap items-baseline justify-between gap-2">
          <h2 id="rev-title" class="text-lg font-semibold">Revenue, last 7 days</h2>
          <p class="text-sm text-muted">Total <span class="price text-text">{{ formatMoney(weekTotal) }}</span></p>
        </div>
        <ColumnChart :data="revenue" :format="shortMoney" title="Revenue per day for the last 7 days" />
      </section>

      <section class="card p-4 sm:p-6" aria-labelledby="status-title">
        <h2 id="status-title" class="mb-1 text-lg font-semibold">Order status</h2>
        <p class="mb-5 text-sm text-muted">{{ statusTotal }} orders in total</p>
        <div class="mb-5 flex h-3 gap-0.5 overflow-hidden rounded-full" role="presentation">
          <div v-for="s in statusRows" :key="s.key" :class="toneBar[s.tone]" :style="{ width: `${s.pct}%` }" />
        </div>
        <ul class="space-y-3">
          <li v-for="s in statusRows" :key="s.key" class="flex items-center justify-between gap-2 text-sm">
            <OrderStatusBadge :status="s.key" />
            <span class="font-mono"><span class="text-text">{{ s.count }}</span> <span class="text-muted">· {{ s.pct }}%</span></span>
          </li>
        </ul>
      </section>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.6fr]">
      <section class="card p-4 sm:p-6" aria-labelledby="top-title">
        <h2 id="top-title" class="mb-1 text-lg font-semibold">Best-selling games</h2>
        <p class="mb-5 text-sm text-muted">Share of items sold, last 30 days</p>
        <BarList :items="d.top_games" />
      </section>

      <section class="card overflow-hidden" aria-labelledby="recent-title">
        <h2 id="recent-title" class="border-b border-line p-4 text-lg font-semibold sm:px-6">Recent orders</h2>
        <ul class="divide-y divide-line">
          <li v-for="o in orders" :key="o.id">
            <RouterLink :to="`/admin/orders/${o.order_number}`" class="flex items-center gap-3 px-4 py-3 transition hover:bg-surface-2/60 sm:px-6">
              <div class="min-w-0 flex-1">
                <p class="font-mono text-sm font-semibold">{{ o.order_number }}</p>
                <p class="truncate text-xs text-muted">{{ (o.customer?.name ?? 'Guest') }} · {{ formatDate(o.created_at) }}</p>
              </div>
              <OrderStatusBadge :status="o.status" class="hidden sm:inline-flex" />
              <span class="price text-sm">{{ formatMoney(o.total) }}</span>
              <ChevronRight class="size-4 text-muted" aria-hidden="true" />
            </RouterLink>
          </li>
        </ul>
      </section>
    </div>
  </div>
</template>
