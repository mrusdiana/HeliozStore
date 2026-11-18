<script setup>
import { computed } from 'vue'

// Single-series column chart in plain HTML/CSS (no chart library). Hover or focus a column for its value.
const props = defineProps({
  data: { type: Array, required: true }, // [{ label, value }]
  format: { type: Function, default: (v) => v },
  title: String,
})
const max = computed(() => Math.max(1, ...props.data.map((d) => d.value)))
const ticks = computed(() => [1, 0.5, 0].map((f) => ({ f, label: props.format(Math.round(max.value * f)) })))
</script>

<template>
  <figure>
    <div class="relative flex h-52 gap-3 pl-16 sm:gap-4">
      <!-- recessive grid -->
      <div v-for="t in ticks" :key="t.f" class="pointer-events-none absolute right-0 left-16 border-t border-dashed border-line" :style="{ bottom: `${t.f * 100}%` }">
        <span class="absolute -top-2 -left-16 w-14 text-right font-mono text-[11px] text-muted">{{ t.label }}</span>
      </div>
      <div
        v-for="d in data"
        :key="d.label"
        class="group relative flex flex-1 items-end justify-center outline-none"
        tabindex="0"
        :aria-label="`${d.label}: ${format(d.value)}`"
      >
        <div class="w-full max-w-10 rounded-t-[4px] bg-primary/85 transition group-hover:bg-primary group-focus-visible:bg-primary" :style="{ height: `${(d.value / max) * 100}%` }" />
        <div class="pointer-events-none absolute bottom-full z-10 mb-1 rounded-lg border border-line bg-surface-2 px-2 py-1 text-center text-xs whitespace-nowrap opacity-0 shadow-lg transition group-hover:opacity-100 group-focus-visible:opacity-100">
          <span class="block text-muted">{{ d.label }}</span><span class="price">{{ format(d.value) }}</span>
        </div>
      </div>
    </div>
    <div class="mt-2 flex gap-3 pl-16 sm:gap-4">
      <span v-for="d in data" :key="d.label" class="flex-1 text-center text-[11px] text-muted">{{ d.short ?? d.label }}</span>
    </div>
    <figcaption v-if="title" class="sr-only">{{ title }}</figcaption>
  </figure>
</template>
