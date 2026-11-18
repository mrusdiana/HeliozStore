<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { ImagePlus } from 'lucide-vue-next'

// v-model holds the File; `current` is the saved image URL when editing.
const file = defineModel({ type: [File, null], default: null })
const props = defineProps({ current: String, wide: Boolean, label: { type: String, default: 'Image' }, error: String })
const preview = ref(null)

function pick(e) {
  const f = e.target.files[0]
  if (!f) return
  if (preview.value) URL.revokeObjectURL(preview.value)
  preview.value = URL.createObjectURL(f)
  file.value = f
}
onBeforeUnmount(() => preview.value && URL.revokeObjectURL(preview.value))
const shown = computed(() => preview.value ?? props.current)
</script>

<template>
  <div>
    <span class="label">{{ label }}</span>
    <label
      class="flex cursor-pointer items-center justify-center overflow-hidden rounded-card border border-dashed bg-surface-2 transition hover:border-primary focus-within:border-primary"
      :class="[wide ? 'aspect-[16/5]' : 'aspect-square max-w-48', error ? 'border-danger' : 'border-line']"
    >
      <img v-if="shown" :src="shown" alt="Selected image preview" class="size-full object-cover" />
      <span v-else class="flex flex-col items-center gap-2 p-4 text-center text-sm text-muted">
        <ImagePlus class="size-6" aria-hidden="true" />Choose an image
      </span>
      <input type="file" accept="image/png,image/jpeg,image/webp" class="sr-only" :aria-invalid="!!error" @change="pick" />
    </label>
    <p :class="error ? 'error-text' : 'hint'">{{ error || 'PNG, JPG, or WebP. Max 2 MB.' }}</p>
  </div>
</template>
