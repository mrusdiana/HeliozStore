<script setup>
import AppImage from '@/components/ui/AppImage.vue'

defineProps({ game: { type: Object, required: true } })
</script>

<template>
  <RouterLink
    :to="{ name: 'game', params: { slug: game.slug } }"
    class="group block rounded-btn border border-line bg-surface p-1.5 transition sm:rounded-card sm:p-2 duration-200 ease-out-soft hover:-translate-y-0.5 hover:border-specular hover:shadow-glow"
  >
    <div class="aspect-square overflow-hidden rounded-[10px] sm:rounded-img">
      <AppImage :src="game.image_url" :alt="game.name" class="transition duration-300 group-hover:scale-105" />
    </div>
    <p class="mt-1.5 truncate px-0.5 pb-0.5 text-xs font-semibold sm:mt-2 sm:px-1 sm:pb-1 sm:text-sm">{{ game.name }}</p>
  </RouterLink>
</template>
