<script setup>
import { ref, watch } from 'vue'
import { Gamepad2 } from 'lucide-vue-next'

// Image with a reserved box (no layout shift) and a fallback icon when the URL is missing or broken.
const props = defineProps({ src: String, alt: { type: String, required: true }, eager: Boolean })
const failed = ref(false)
watch(() => props.src, () => (failed.value = false))
</script>

<template>
  <img
    v-if="src && !failed"
    :src="src"
    :alt="alt"
    :loading="eager ? 'eager' : 'lazy'"
    decoding="async"
    class="size-full object-cover"
    @error="failed = true"
  />
  <div v-else class="grid size-full place-items-center bg-gradient-to-br from-secondary/30 to-surface-2" role="img" :aria-label="alt">
    <Gamepad2 class="size-1/3 text-primary/70" aria-hidden="true" />
  </div>
</template>
