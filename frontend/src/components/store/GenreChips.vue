<script setup>
const selected = defineModel({ type: String, default: '' })
defineProps({ genres: { type: Array, required: true } })
</script>

<template>
  <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:px-0" role="group" aria-label="Filter by genre">
    <button
      v-for="g in ['', ...genres]"
      :key="g"
      type="button"
      class="min-h-10 shrink-0 cursor-pointer rounded-full border px-4 text-sm font-semibold transition duration-200"
      :class="selected === g ? 'border-primary bg-primary text-on-primary' : 'border-line bg-surface-2 text-muted hover:text-text'"
      :aria-pressed="selected === g"
      @click="selected = g"
    >
      {{ g || 'All' }}
    </button>
  </div>
</template>
