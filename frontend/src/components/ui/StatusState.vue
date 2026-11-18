<script setup>
import { SearchX, AlertTriangle } from 'lucide-vue-next'

// Shared empty / error block (DESIGN.md §5: every data screen has these states).
defineProps({ type: { type: String, default: 'empty' }, title: String, text: String })
defineEmits(['retry'])
</script>

<template>
  <div class="card flex flex-col items-center gap-3 px-6 py-12 text-center">
    <AlertTriangle v-if="type === 'error'" class="size-10 text-danger" aria-hidden="true" />
    <SearchX v-else class="size-10 text-muted" aria-hidden="true" />
    <h2 class="text-lg font-semibold">{{ title ?? (type === 'error' ? 'Something went wrong. Please try again.' : 'Nothing here yet') }}</h2>
    <p v-if="text" class="max-w-sm text-sm text-muted">{{ text }}</p>
    <button v-if="type === 'error'" type="button" class="btn-secondary mt-2" @click="$emit('retry')">Try again</button>
    <slot />
  </div>
</template>
