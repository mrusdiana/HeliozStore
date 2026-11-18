<script setup>
// Horizontal bars with direct labels (name left, value right). Used for "Best-selling games".
defineProps({ items: { type: Array, required: true } }) // [{ name, percent, items_sold }]
</script>

<template>
  <ul class="space-y-3">
    <li v-for="g in items" :key="g.name" class="space-y-1.5">
      <div class="flex items-baseline justify-between gap-3 text-sm">
        <span class="truncate font-semibold">{{ g.name }}</span>
        <span class="shrink-0 font-mono text-xs text-muted"><span class="text-text">{{ g.percent }}%</span> · {{ g.items_sold }} sold</span>
      </div>
      <div class="h-2 overflow-hidden rounded-full bg-surface-2" role="presentation">
        <div class="h-full rounded-full bg-gradient-to-r from-secondary to-primary" :style="{ width: `${g.percent}%` }" />
      </div>
    </li>
  </ul>
</template>
