<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import HeroCarousel from '@/components/store/HeroCarousel.vue'
import FlashSale from '@/components/store/FlashSale.vue'
import NewsSection from '@/components/store/NewsSection.vue'
import GameCard from '@/components/store/GameCard.vue'
import GenreChips from '@/components/store/GenreChips.vue'
import StatusState from '@/components/ui/StatusState.vue'
import { banners, promos, games, genres } from '@/data/mock'

const route = useRoute()
const router = useRouter()

// TODO: API — GET /home for banners/promos/games, GET /genres, and GET /games?search=&genre= when filtering.
const loading = ref(false)
const error = ref(false)

const search = computed(() => route.query.search ?? '')
const genre = computed({
  get: () => route.query.genre ?? '',
  set: (g) => router.replace({ query: { ...route.query, genre: g || undefined } }),
})
const filtering = computed(() => !!search.value)

// Mock filter. After integration this list comes straight from the API response.
const list = computed(() =>
  games.filter(
    (g) => (!genre.value || g.genre === genre.value) && g.name.toLowerCase().includes(search.value.toLowerCase()),
  ),
)
const clearFilters = () => router.replace({ query: {} })
</script>

<template>
  <div class="container-app space-y-8 pt-4 sm:space-y-10 sm:pt-6">
    <template v-if="!filtering">
      <div v-if="loading" class="skeleton aspect-[16/9] sm:aspect-[16/5]" />
      <HeroCarousel v-else-if="banners.length" :banners="banners" />

      <FlashSale v-if="promos.length" :promos="promos.slice(0, 4)" />
    </template>

    <section aria-labelledby="games-title">
      <h2 id="games-title" class="mb-3 text-lg font-semibold tracking-tight sm:mb-4 sm:text-xl">
        {{ filtering ? `Results for “${search}”` : 'All games' }}
      </h2>
      <GenreChips v-model="genre" :genres="genres" class="mb-5" />

      <div v-if="loading" class="grid grid-cols-3 gap-2 sm:grid-cols-4 sm:gap-3 lg:grid-cols-6">
        <div v-for="n in 12" :key="n" class="skeleton aspect-[1/1.2]" />
      </div>
      <StatusState v-else-if="error" type="error" @retry="error = false" />
      <StatusState v-else-if="!list.length" title="No games found" text="Try another name or genre.">
        <button type="button" class="btn-secondary mt-2" @click="clearFilters">Clear filters</button>
      </StatusState>
      <div v-else class="grid grid-cols-3 gap-2 sm:grid-cols-4 sm:gap-3 lg:grid-cols-6">
        <GameCard v-for="g in list" :key="g.id" :game="g" />
      </div>
    </section>

    <NewsSection v-if="!filtering" />
  </div>
</template>
