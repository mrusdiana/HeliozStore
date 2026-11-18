<script setup>
import { Info } from 'lucide-vue-next'
import { computed, ref } from 'vue'

// Fields come from the game's account_fields. Client checks only mirror the backend rules for fast feedback;
// the backend stays the source of truth (422 errors map to `serverErrors`).
const props = defineProps({
  fields: { type: Array, required: true },
  instructions: String,
  serverErrors: { type: Object, default: () => ({}) },
  // Turn on once the buyer moves ahead (e.g. picks a payment method) so empty fields get flagged.
  showErrors: Boolean,
})
const values = defineModel({ type: Object, required: true })
const touched = ref({})

function errorFor(f) {
  const server = props.serverErrors[`account_data.${f.key}`]
  if (server) return Array.isArray(server) ? server[0] : server
  if (!touched.value[f.key] && !props.showErrors) return ''
  const v = (values.value[f.key] ?? '').trim()
  // Empty fields are flagged only after the buyer moves ahead, not just for leaving the field.
  if (!v) return props.showErrors ? `Enter your ${f.label}.` : ''
  if (f.type === 'numeric' && !/^\d+$/.test(v)) return 'Digits only.'
  if (v.length < f.min || v.length > f.max) return f.type === 'numeric' ? `Use ${f.min}–${f.max} digits.` : `Use ${f.min}–${f.max} characters.`
  return ''
}

// Two fields (e.g. User ID + Zone ID) stay side by side even on phones; the first one gets more room.
const grid = computed(() => (props.fields.length === 2 ? 'grid-cols-[3fr_2fr] gap-3 sm:grid-cols-2 sm:gap-4' : props.fields.length > 2 ? 'sm:grid-cols-2' : ''))
</script>

<template>
  <div class="space-y-4">
    <p v-if="instructions" class="flex gap-2.5 rounded-btn border border-primary/20 bg-primary/8 p-3 text-sm text-text/90">
      <Info class="mt-0.5 size-4 shrink-0 text-primary" aria-hidden="true" />
      {{ instructions }}
    </p>
    <div class="grid gap-4" :class="grid">
      <div v-for="f in fields" :key="f.key" class="min-w-0">
        <label :for="`acc-${f.key}`" class="label">{{ f.label }}</label>
        <input
          :id="`acc-${f.key}`"
          v-model="values[f.key]"
          class="field font-mono"
          :inputmode="f.type === 'numeric' ? 'numeric' : 'text'"
          :placeholder="f.placeholder"
          :maxlength="f.max"
          autocomplete="off"
          :aria-invalid="!!errorFor(f)"
          :aria-describedby="errorFor(f) ? `acc-${f.key}-msg` : undefined"
          @blur="touched[f.key] = true"
        />
        <!-- Message only when there is a problem; nothing shown up front -->
        <p v-if="errorFor(f)" :id="`acc-${f.key}-msg`" class="mt-1 text-[11px] leading-snug text-danger sm:text-xs" role="alert">{{ errorFor(f) }}</p>
      </div>
    </div>
  </div>
</template>
