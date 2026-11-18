<script setup>
import { Plus, ChevronLeft } from 'lucide-vue-next'

defineProps({ title: { type: String, required: true }, newTo: String, newLabel: String, backTo: String })
</script>

<template>
  <div class="mb-6">
    <RouterLink v-if="backTo" :to="backTo" class="mb-2 inline-flex min-h-11 items-center gap-1 text-sm font-semibold text-muted hover:text-text">
      <ChevronLeft class="size-4" aria-hidden="true" />Back
    </RouterLink>
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-2xl font-bold tracking-tight sm:text-[28px]">{{ title }}</h1>
      <RouterLink v-if="newTo" :to="newTo" class="btn-primary"><Plus class="size-4" aria-hidden="true" />{{ newLabel }}</RouterLink>
    </div>
  </div>
</template>
