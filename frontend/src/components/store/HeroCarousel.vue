<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import AppImage from '@/components/ui/AppImage.vue'

// DESIGN.md §3 HeroCarousel: 5s autoplay, pauses on hover/focus/touch, off for reduced motion.
const props = defineProps({ banners: { type: Array, required: true } })
const index = ref(0)
const paused = ref(false)
const multiple = computed(() => props.banners.length > 1)
let timer

const go = (i) => (index.value = (i + props.banners.length) % props.banners.length)

onMounted(() => {
  if (!multiple.value || matchMedia('(prefers-reduced-motion: reduce)').matches) return
  timer = setInterval(() => !paused.value && go(index.value + 1), 5000)
})
onBeforeUnmount(() => clearInterval(timer))
</script>

<template>
  <section
    class="group relative aspect-[16/9] overflow-hidden rounded-card border border-line sm:aspect-[16/5]"
    aria-roledescription="carousel"
    aria-label="Promotions"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    @focusin="paused = true"
    @focusout="paused = false"
    @touchstart.passive="paused = true"
  >
    <div class="flex h-full transition-transform duration-[400ms] ease-out-soft" :style="{ transform: `translateX(-${index * 100}%)` }">
      <div
        v-for="(b, i) in banners"
        :key="b.id"
        class="relative h-full w-full shrink-0"
        role="group"
        aria-roledescription="slide"
        :aria-label="`${i + 1} of ${banners.length}`"
        :aria-hidden="i !== index"
        :inert="i !== index"
      >
        <AppImage :src="b.image_url" :alt="b.title" :eager="i === 0" />
        <div class="absolute inset-0 bg-gradient-to-t from-bg/95 via-bg/50 to-transparent sm:bg-gradient-to-r sm:from-bg/90 sm:via-bg/40" />
        <div class="absolute inset-x-0 bottom-0 p-4 sm:top-0 sm:flex sm:max-w-lg sm:flex-col sm:justify-center sm:p-10">
          <h2 class="text-[22px] leading-tight font-bold tracking-tight sm:text-4xl">{{ b.title }}</h2>
          <p class="mt-1 text-sm text-text/80 sm:mt-2 sm:text-base">{{ b.subtitle }}</p>
          <RouterLink :to="{ name: 'game', params: { slug: b.game_slug } }" class="btn-primary mt-3 hidden w-fit sm:mt-5 sm:inline-flex">Top up now</RouterLink>
        </div>
        <RouterLink :to="{ name: 'game', params: { slug: b.game_slug } }" class="absolute inset-0 sm:hidden" :aria-label="`Top up ${b.game?.name ?? b.title}`" />
      </div>
    </div>

    <template v-if="multiple">
      <button type="button" class="btn-icon absolute top-1/2 left-3 hidden -translate-y-1/2 rounded-full bg-glass text-text opacity-0 backdrop-blur group-hover:opacity-100 focus-visible:opacity-100 sm:inline-flex" aria-label="Previous slide" @click="go(index - 1)">
        <ChevronLeft class="size-5" />
      </button>
      <button type="button" class="btn-icon absolute top-1/2 right-3 hidden -translate-y-1/2 rounded-full bg-glass text-text opacity-0 backdrop-blur group-hover:opacity-100 focus-visible:opacity-100 sm:inline-flex" aria-label="Next slide" @click="go(index + 1)">
        <ChevronRight class="size-5" />
      </button>
      <div class="absolute right-4 bottom-3 flex gap-1.5 sm:right-auto sm:left-10 sm:bottom-5">
        <button
          v-for="(b, i) in banners"
          :key="b.id"
          type="button"
          class="grid h-6 cursor-pointer place-items-center"
          :aria-label="`Go to slide ${i + 1}`"
          :aria-current="i === index"
          @click="go(i)"
        >
          <span class="block h-1.5 rounded-full transition-all duration-300" :class="i === index ? 'w-6 bg-primary' : 'w-1.5 bg-text/40'" />
        </button>
      </div>
    </template>
  </section>
</template>
