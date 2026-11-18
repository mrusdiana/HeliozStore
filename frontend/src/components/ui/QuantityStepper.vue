<script setup>
import { Minus, Plus } from 'lucide-vue-next'

const qty = defineModel({ type: Number, default: 1 })
defineProps({ max: { type: Number, default: 99 }, disabled: Boolean })
</script>

<template>
  <div class="inline-flex items-center rounded-btn border border-line bg-surface-2" role="group" aria-label="Quantity">
    <button type="button" class="btn-icon rounded-r-none text-text hover:bg-surface" :disabled="disabled || qty <= 1" aria-label="Decrease quantity" @click="qty--">
      <Minus class="size-4" />
    </button>
    <output class="w-10 text-center font-mono font-semibold tabular-nums" aria-live="polite">{{ qty }}</output>
    <button type="button" class="btn-icon rounded-l-none text-text hover:bg-surface" :disabled="disabled || qty >= max" aria-label="Increase quantity" @click="qty++">
      <Plus class="size-4" />
    </button>
  </div>
</template>
