<script setup>
import { CheckCircle2, AlertCircle } from 'lucide-vue-next'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
</script>

<template>
  <div class="pointer-events-none fixed inset-x-0 top-20 z-[60] flex flex-col items-center gap-2 px-4" aria-live="polite">
    <TransitionGroup
      enter-from-class="opacity-0 -translate-y-2"
      leave-to-class="opacity-0"
      enter-active-class="transition duration-200"
      leave-active-class="transition duration-150"
    >
      <div
        v-for="t in toast.toasts"
        :key="t.id"
        class="pointer-events-auto flex items-center gap-2 rounded-btn border border-line bg-surface-2 px-4 py-3 text-sm font-medium shadow-xl shadow-black/40"
      >
        <CheckCircle2 v-if="t.tone === 'success'" class="size-4 text-success" aria-hidden="true" />
        <AlertCircle v-else class="size-4 text-danger" aria-hidden="true" />
        {{ t.message }}
      </div>
    </TransitionGroup>
  </div>
</template>
