<script setup>
import { computed } from 'vue'
import { TrendingUp, TrendingDown } from 'lucide-vue-next'

// `previous` is optional; when given, the card shows the change in text + arrow (never color alone).
const props = defineProps({ label: String, value: String, icon: Object, current: Number, previous: Number, note: String })
const change = computed(() => {
  if (props.previous == null || !props.previous) return null
  return Math.round(((props.current - props.previous) / props.previous) * 100)
})
</script>

<template>
  <div class="card space-y-3 p-4 sm:p-5">
    <div class="flex items-center justify-between gap-2">
      <p class="text-[13px] font-medium text-muted">{{ label }}</p>
      <span class="grid size-9 place-items-center rounded-btn bg-primary/10 text-primary"><component :is="icon" class="size-4" aria-hidden="true" /></span>
    </div>
    <p class="price text-2xl text-text">{{ value }}</p>
    <p v-if="change !== null" class="flex items-center gap-1 text-xs font-semibold" :class="change >= 0 ? 'text-success' : 'text-danger'">
      <component :is="change >= 0 ? TrendingUp : TrendingDown" class="size-3.5" aria-hidden="true" />
      {{ change >= 0 ? '+' : '' }}{{ change }}% <span class="font-medium text-muted">vs yesterday</span>
    </p>
    <p v-else-if="note" class="text-xs text-muted">{{ note }}</p>
  </div>
</template>
