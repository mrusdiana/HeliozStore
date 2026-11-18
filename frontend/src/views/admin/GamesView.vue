<script setup>
import { ref } from 'vue'
import { Pencil, Trash2 } from 'lucide-vue-next'
import PageHeader from '@/components/admin/PageHeader.vue'
import ActiveBadge from '@/components/admin/ActiveBadge.vue'
import AppImage from '@/components/ui/AppImage.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
import { adminGames } from '@/data/mock'
import { useToastStore } from '@/stores/toast'

// TODO: API — GET /admin/games.
const games = ref(adminGames)
const toDelete = ref(null)
const toast = useToastStore()

function confirmDelete() {
  // TODO: API — DELETE /admin/games/{id}; on 409 show `message` from the response.
  toast.show('This game still has packages. Delete them or set the game to inactive.', 'danger')
  toDelete.value = null
}
</script>

<template>
  <PageHeader title="Games" new-to="/admin/games/new" new-label="New game" />
  <div class="card divide-y divide-line">
    <div v-for="g in games" :key="g.id" class="flex items-center gap-3 p-3 sm:p-4">
      <div class="size-12 shrink-0 overflow-hidden rounded-[10px]"><AppImage :src="g.image_url" :alt="g.name" /></div>
      <div class="min-w-0 flex-1">
        <p class="truncate font-semibold">{{ g.name }}</p>
        <p class="text-[13px] text-muted">{{ g.genre }} · {{ g.products_count }} packages · #{{ g.sort_order }}</p>
      </div>
      <ActiveBadge :active="g.is_active" class="hidden sm:inline-flex" />
      <RouterLink :to="`/admin/games/${g.id}/edit`" class="btn-icon text-muted hover:bg-surface-2 hover:text-text" :aria-label="`Edit ${g.name}`"><Pencil class="size-4" /></RouterLink>
      <button type="button" class="btn-icon text-muted hover:bg-danger/10 hover:text-danger" :aria-label="`Delete ${g.name}`" @click="toDelete = g"><Trash2 class="size-4" /></button>
    </div>
  </div>
  <ConfirmDialog :open="!!toDelete" :title="`Delete ${toDelete?.name}?`" text="This cannot be undone." @confirm="confirmDelete" @cancel="toDelete = null" />
</template>
