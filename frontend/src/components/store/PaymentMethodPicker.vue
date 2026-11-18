<script setup>
import { ref } from 'vue'
import { Check, ChevronDown } from 'lucide-vue-next'
import { paymentMethods } from '@/data/mock'
import { formatMoney } from '@/utils/format'

// Uniform accordion groups (DESIGN.md §3). `total` is shown per method; no admin fee in the MVP.
const selected = defineModel({ type: [String, null], default: null })
defineProps({ total: { type: Number, default: 0 } })
const open = ref(null)

const logo = (name) => `/payments/${name}.svg`
const strip = (g) => g.strip ?? g.items.map((m) => m.logo)
const picked = (g) => g.items.find((m) => m.value === selected.value)
</script>

<template>
  <div class="space-y-2.5" role="radiogroup" aria-label="Payment method">
    <div
      v-for="g in paymentMethods"
      :key="g.group"
      class="overflow-hidden rounded-btn border bg-bg/60 transition"
      :class="picked(g) ? 'border-primary/60' : 'border-line'"
    >
      <button
        type="button"
        class="flex min-h-12 w-full cursor-pointer items-center gap-2 px-4 text-left"
        :aria-expanded="open === g.group"
        @click="open = open === g.group ? null : g.group"
      >
        <span class="font-semibold">{{ g.group }}</span>
        <span v-if="picked(g)" class="pill bg-primary/15 text-primary-hover">
          <Check class="size-3" aria-hidden="true" />{{ picked(g).label.split(' · ')[0] }}
        </span>
        <ChevronDown class="ml-auto size-5 shrink-0 text-muted transition duration-200" :class="open === g.group && 'rotate-180'" aria-hidden="true" />
      </button>

      <!-- One row of logos; chips shrink instead of wrapping -->
      <div class="flex justify-end gap-1 border-t border-line bg-surface-2/50 px-3 py-2 sm:gap-1.5 sm:px-4" aria-hidden="true">
        <span v-for="l in strip(g)" :key="l" class="flex h-4 min-w-0 max-w-12 flex-1 items-center justify-center rounded-[3px] bg-white px-0.5 sm:h-5 sm:max-w-14 sm:px-1">
          <img :src="logo(l)" alt="" class="max-h-full max-w-full" loading="lazy" />
        </span>
      </div>

      <div v-if="open === g.group" class="grid grid-cols-1 gap-2 border-t border-line p-3 sm:grid-cols-2">
        <label
          v-for="m in g.items"
          :key="m.value"
          class="relative flex min-h-12 min-w-0 cursor-pointer items-center justify-between gap-3 rounded-btn border px-3 transition duration-200 has-focus-visible:outline-2 has-focus-visible:outline-primary"
          :class="selected === m.value ? 'border-primary bg-surface-2 shadow-glow' : 'border-line bg-surface hover:border-specular'"
        >
          <input v-model="selected" type="radio" name="payment_method" :value="m.value" class="sr-only" />
          <span class="flex min-w-0 items-center gap-2.5 text-sm font-semibold">
            <span class="flex h-6 w-12 shrink-0 items-center justify-center rounded-[5px] bg-white px-1"><img :src="logo(m.logo)" alt="" class="max-h-4 max-w-full" loading="lazy" /></span>
            <span class="truncate">{{ m.label }}</span>
            <Check v-if="selected === m.value" class="size-4 shrink-0 text-primary" aria-hidden="true" />
          </span>
          <span class="price shrink-0 text-sm text-muted">{{ formatMoney(total) }}</span>
        </label>
      </div>
    </div>
  </div>
</template>
