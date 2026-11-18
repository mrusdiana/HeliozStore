<script setup>
import { ref } from 'vue'
import { Pencil, Trash2 } from 'lucide-vue-next'
import PageHeader from '@/components/admin/PageHeader.vue'
import ActiveBadge from '@/components/admin/ActiveBadge.vue'
import AppImage from '@/components/ui/AppImage.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
import { banners as mockBanners } from '@/data/mock'

// TODO: API — GET /admin/banners.
const banners = ref(mockBanners)
const toDelete = ref(null)

function confirmDelete() {
  // TODO: API — DELETE /admin/banners/{id}.
  banners.value = banners.value.filter((b) => b.id !== toDelete.value.id)
  toDelete.value = null
}
</script>

<template>
  <PageHeader title="Banners" new-to="/admin/banners/new" new-label="New banner" />
  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <article v-for="b in banners" :key="b.id" class="card overflow-hidden">
      <div class="aspect-[16/5]"><AppImage :src="b.image_url" :alt="b.title" /></div>
      <div class="flex items-start gap-2 p-4">
        <div class="min-w-0 flex-1">
          <p class="truncate font-semibold">{{ b.title }}</p>
          <p class="text-[13px] text-muted">{{ b.game.name }} · #{{ b.sort_order }}</p>
          <ActiveBadge :active="b.is_active" class="mt-2" />
        </div>
        <RouterLink :to="`/admin/banners/${b.id}/edit`" class="btn-icon text-muted hover:bg-surface-2 hover:text-text" :aria-label="`Edit ${b.title}`"><Pencil class="size-4" /></RouterLink>
        <button type="button" class="btn-icon text-muted hover:bg-danger/10 hover:text-danger" :aria-label="`Delete ${b.title}`" @click="toDelete = b"><Trash2 class="size-4" /></button>
      </div>
    </article>
  </div>
  <ConfirmDialog :open="!!toDelete" :title="`Delete “${toDelete?.title}”?`" text="This cannot be undone." @confirm="confirmDelete" @cancel="toDelete = null" />
</template>
