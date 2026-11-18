<script setup>
import { nextTick, ref, watch } from 'vue'

// Native <dialog>: Esc closes and focus returns to the opener automatically.
const props = defineProps({ open: Boolean, title: String, text: String, confirmLabel: { type: String, default: 'Delete' } })
const emit = defineEmits(['confirm', 'cancel'])
const el = ref()

watch(() => props.open, async (v) => {
  await nextTick()
  v ? el.value?.showModal() : el.value?.close()
})
</script>

<template>
  <dialog
    ref="el"
    class="m-auto w-[calc(100%-2rem)] max-w-sm rounded-card border border-line bg-surface p-6 text-text backdrop:bg-black/60 backdrop:backdrop-blur-sm"
    @close="open && emit('cancel')"
  >
    <h2 class="text-lg font-semibold">{{ title }}</h2>
    <p class="mt-2 text-sm text-muted">{{ text }}</p>
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" class="btn-ghost" @click="emit('cancel')">Cancel</button>
      <button type="button" class="btn-danger" @click="emit('confirm')">{{ confirmLabel }}</button>
    </div>
  </dialog>
</template>
