<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Search, X } from 'lucide-vue-next'

// Search lives in the URL (?search=) so it survives refresh and can be shared. Debounced 300ms.
const route = useRoute()
const router = useRouter()
const text = ref(route.query.search ?? '')
let timer

watch(() => route.query.search, (v) => (text.value = v ?? ''))

function onInput() {
  clearTimeout(timer)
  timer = setTimeout(() => {
    const query = { ...route.query, search: text.value.trim() || undefined }
    router.push({ name: 'home', query })
  }, 300)
}

function clear() {
  text.value = ''
  onInput()
}
</script>

<template>
  <label class="relative block w-full">
    <span class="sr-only">Search games</span>
    <Search class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-muted" aria-hidden="true" />
    <input v-model="text" type="search" placeholder="Search games" class="field pr-10 pl-10 [&::-webkit-search-cancel-button]:hidden" @input="onInput" />
    <button v-if="text" type="button" class="absolute top-1/2 right-1 grid size-9 -translate-y-1/2 place-items-center rounded-lg text-muted hover:text-text" aria-label="Clear search" @click="clear">
      <X class="size-4" />
    </button>
  </label>
</template>
