<script setup>
import { useId } from 'vue'

// HeliozStore mark: a processor chip with an "H" split by a lightning bolt. Same artwork as public/favicon.svg.
defineProps({ to: { type: String, default: '/' } })
// Unique gradient id: a hidden copy of the logo (e.g. mobile header) would otherwise break the visible one.
const id = useId()
const pins = [22, 32, 42]
</script>

<template>
  <RouterLink :to="to" class="flex shrink-0 items-center gap-2" aria-label="HeliozStore home">
    <svg viewBox="0 0 64 64" class="size-8 sm:size-9" aria-hidden="true">
      <defs>
        <linearGradient :id="id" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#38BDF8" /><stop offset="1" stop-color="#2563EB" />
        </linearGradient>
      </defs>
      <g :stroke="`url(#${id})`" stroke-width="4" stroke-linecap="round" fill="none">
        <rect x="12" y="12" width="40" height="40" rx="8" />
        <template v-for="p in pins" :key="p">
          <path :d="`M${p} 4v8M${p} 52v8M4 ${p}h8M52 ${p}h8`" />
        </template>
      </g>
      <g :fill="`url(#${id})`">
        <path d="M20 20h7v9.5L20 36z M20 39l7-6.5V44h-7z" />
        <path d="M37 20h7v24h-7v-4l3-4.5h-3V31z" />
        <path d="M36.5 16 25 34h6l-3.5 14L40 29h-6.2z" stroke="#05080F" stroke-width="2" stroke-linejoin="round" paint-order="stroke" />
      </g>
    </svg>
    <span class="font-display text-base leading-none font-bold tracking-tight sm:text-lg">
      Helioz<span class="text-primary-hover">Store</span><span class="hidden text-sm font-semibold text-muted sm:inline">.com</span>
    </span>
  </RouterLink>
</template>
