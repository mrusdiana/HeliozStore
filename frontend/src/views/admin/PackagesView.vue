<script setup>
import { computed, ref } from 'vue'
import { Pencil, Trash2 } from 'lucide-vue-next'
import PageHeader from '@/components/admin/PageHeader.vue'
import ActiveBadge from '@/components/admin/ActiveBadge.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
import StockBadge from '@/components/ui/StockBadge.vue'
import BarList from '@/components/admin/BarList.vue'
import { adminGames, adminProducts, dashboard } from '@/data/mock'
import { formatMoney } from '@/utils/format'
import { useToastStore } from '@/stores/toast'

// TODO: API — GET /admin/products?game_id=&page=.
const gameId = ref(1)
const list = computed(() => adminProducts.filter((p) => !gameId.value || p.game_id === gameId.value))
const toDelete = ref(null)
const toast = useToastStore()

function confirmDelete() {
  // TODO: API — DELETE /admin/products/{id}; 409 → "Set it to inactive instead".
  toast.show('This package has orders. Set it to inactive instead.', 'danger')
  toDelete.value = null
}
</script>

<template>
  <PageHeader title="Packages" new-to="/admin/packages/new" new-label="New package" />
  <!-- TODO: API — `top_games` from GET /admin/dashboard -->
  <section class="card mb-6 p-4 sm:p-6" aria-labelledby="best-title">
    <h2 id="best-title" class="text-lg font-semibold">Best-selling games</h2>
    <p class="mb-5 text-sm text-muted">Share of items sold, last 30 days</p>
    <BarList :items="dashboard.top_games" class="grid gap-x-8 gap-y-3 space-y-0 md:grid-cols-2" />
  </section>
  <label class="mb-4 block sm:max-w-xs">
    <span class="sr-only">Filter by game</span>
    <select v-model.number="gameId" class="field">
      <option :value="0">All games</option>
      <option v-for="g in adminGames" :key="g.id" :value="g.id">{{ g.name }}</option>
    </select>
  </label>

  <div class="card hidden overflow-x-auto md:block">
    <table class="w-full text-left text-sm">
      <thead class="border-b border-line text-xs tracking-wider text-muted uppercase">
        <tr><th class="p-4">Package</th><th class="p-4">Game</th><th class="p-4 text-right">Price</th><th class="p-4">Stock</th><th class="p-4">Status</th><th class="p-4"><span class="sr-only">Actions</span></th></tr>
      </thead>
      <tbody class="divide-y divide-line">
        <tr v-for="p in list" :key="p.id" class="transition hover:bg-surface-2/60">
          <td class="p-4 font-semibold">{{ p.name }} <span v-if="p.original_price" class="pill ml-1 bg-hot/15 text-hot">Promo</span></td>
          <td class="p-4 text-muted">{{ p.game.name }}</td>
          <td class="p-4 text-right"><span class="price">{{ formatMoney(p.price) }}</span><s v-if="p.original_price" class="price block text-xs text-muted">{{ formatMoney(p.original_price) }}</s></td>
          <td class="p-4"><StockBadge :stock="p.stock" /></td>
          <td class="p-4"><ActiveBadge :active="p.is_active" /></td>
          <td class="p-2 text-right whitespace-nowrap">
            <RouterLink :to="`/admin/packages/${p.id}/edit`" class="btn-icon text-muted hover:bg-surface-2 hover:text-text" :aria-label="`Edit ${p.name}`"><Pencil class="size-4" /></RouterLink>
            <button type="button" class="btn-icon text-muted hover:bg-danger/10 hover:text-danger" :aria-label="`Delete ${p.name}`" @click="toDelete = p"><Trash2 class="size-4" /></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <ul class="space-y-3 md:hidden">
    <li v-for="p in list" :key="p.id" class="card flex items-center gap-3 p-4">
      <div class="min-w-0 flex-1 space-y-1">
        <p class="font-semibold">{{ p.name }}</p>
        <p class="text-[13px] text-muted">{{ p.game.name }} · <span class="price text-text">{{ formatMoney(p.price) }}</span></p>
        <div class="flex gap-1.5"><StockBadge :stock="p.stock" /><ActiveBadge :active="p.is_active" /></div>
      </div>
      <RouterLink :to="`/admin/packages/${p.id}/edit`" class="btn-icon text-muted" :aria-label="`Edit ${p.name}`"><Pencil class="size-4" /></RouterLink>
      <button type="button" class="btn-icon text-muted hover:text-danger" :aria-label="`Delete ${p.name}`" @click="toDelete = p"><Trash2 class="size-4" /></button>
    </li>
  </ul>
  <ConfirmDialog :open="!!toDelete" :title="`Delete ${toDelete?.name}?`" text="This cannot be undone." @confirm="confirmDelete" @cancel="toDelete = null" />
</template>
