<script setup>
import { ArrowRight } from 'lucide-vue-next'
import AppImage from '@/components/ui/AppImage.vue'
import { news } from '@/data/mock'
import { formatDate } from '@/utils/format'

// TODO: API — no news endpoint yet. Replace `news` with GET /news when the backend adds it.
const day = (d) => formatDate(d).split(',')[0]
</script>

<template>
  <section id="news" class="scroll-mt-32" aria-labelledby="news-title">
    <div class="mb-4 flex items-end justify-between gap-3">
      <div>
        <h2 id="news-title" class="text-lg font-semibold tracking-tight sm:text-xl">News &amp; events</h2>
        <p class="text-sm text-muted">Patch notes, in-game events, and esports updates.</p>
      </div>
    </div>
    <div class="-mx-4 flex snap-x gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:none] sm:mx-0 sm:grid sm:grid-cols-2 sm:overflow-visible sm:px-0 lg:grid-cols-4">
      <RouterLink
        v-for="n in news"
        :key="n.id"
        :to="{ name: 'game', params: { slug: n.game_slug } }"
        class="group w-64 shrink-0 snap-start overflow-hidden rounded-card border border-line bg-surface transition duration-200 ease-out-soft hover:-translate-y-0.5 hover:border-specular sm:w-auto"
      >
        <div class="aspect-video overflow-hidden"><AppImage :src="n.image_url" :alt="n.title" class="transition duration-300 group-hover:scale-105" /></div>
        <div class="space-y-2 p-4">
          <span class="pill bg-secondary/20 text-primary-hover">{{ n.tag }}</span>
          <h3 class="line-clamp-2 font-sans text-sm leading-snug font-semibold">{{ n.title }}</h3>
          <p class="flex items-center justify-between text-xs text-muted">
            {{ day(n.date) }}<ArrowRight class="size-3.5 transition group-hover:translate-x-0.5 group-hover:text-primary" aria-hidden="true" />
          </p>
        </div>
      </RouterLink>
    </div>
  </section>
</template>
