<script setup>
defineProps({ step: { type: Number, required: true }, title: { type: String, required: true }, note: String })
</script>

<template>
  <section class="card p-4 sm:p-6" :aria-labelledby="`step-${step}`">
    <div class="mb-4 flex items-center gap-3 sm:mb-5">
      <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-primary/15 font-mono text-xs font-semibold text-primary-hover">
        {{ String(step).padStart(2, '0') }}
      </span>
      <h2 :id="`step-${step}`" class="text-base font-bold tracking-tight sm:text-xl">{{ title }}</h2>
      <span v-if="note" class="ml-auto hidden text-sm text-muted sm:block">{{ note }}</span>
    </div>
    <slot />
  </section>
</template>
