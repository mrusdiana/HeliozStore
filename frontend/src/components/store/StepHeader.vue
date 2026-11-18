<script setup>
defineProps({ step: { type: Number, required: true }, title: { type: String, required: true } })
</script>

<template>
  <h2 class="flex items-center gap-3 text-xl font-semibold tracking-tight">
    <span class="grid size-8 shrink-0 place-items-center rounded-full border border-primary bg-primary/14 font-mono text-sm text-primary">{{ step }}</span>
    {{ title }}
  </h2>
</template>
