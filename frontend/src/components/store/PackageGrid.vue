<script setup>
import { Check } from 'lucide-vue-next'
import ItemIcon from '@/components/ui/ItemIcon.vue'
import { formatMoney } from '@/utils/format'

// Radio group: arrow keys move between packages, Space/click selects (DESIGN.md §7).
const selected = defineModel({ type: [Number, null], default: null })
const props = defineProps({ packages: { type: Array, required: true }, currency: String })

function move(e, i) {
  const step = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 }[e.key]
  if (!step) return
  e.preventDefault()
  const list = props.packages
  for (let k = 1; k <= list.length; k++) {
    const next = list[(i + step * k + list.length) % list.length]
    if (next.stock > 0) {
      selected.value = next.id
      e.currentTarget.parentElement.querySelector(`[data-id="${next.id}"]`)?.focus()
      return
    }
  }
}
</script>

<template>
  <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4" role="radiogroup" :aria-label="`${currency} packages`">
    <button
      v-for="(p, i) in packages"
      :key="p.id"
      type="button"
      role="radio"
      :data-id="p.id"
      :aria-checked="selected === p.id"
      :aria-disabled="p.stock <= 0"
      :tabindex="selected === p.id || (!selected && i === 0) ? 0 : -1"
      class="relative flex min-h-24 cursor-pointer flex-col items-start gap-1 rounded-card border p-3 text-left transition duration-200 ease-out-soft"
      :class="[
        selected === p.id ? 'border-primary bg-surface-2 shadow-glow [background-image:linear-gradient(rgb(59_130_246/0.1),rgb(59_130_246/0.1))]' : 'border-line bg-surface hover:-translate-y-0.5 hover:border-specular',
        p.stock <= 0 && 'pointer-events-none opacity-45',
      ]"
      @click="p.stock > 0 && (selected = p.id)"
      @keydown="move($event, i)"
    >
      <span class="flex w-full items-center gap-1.5">
        <ItemIcon :currency="currency" class="size-8 shrink-0" />
        <span v-if="p.original_price" class="pill ml-auto bg-hot/15 text-hot">Promo</span>
        <span v-else-if="p.hot" class="pill ml-auto bg-warning/12 text-warning">Hot</span>
      </span>
      <span class="font-semibold">{{ p.name }}</span>
      <span class="mt-auto flex flex-wrap items-baseline gap-x-2">
        <span class="price text-[17px] text-text">{{ formatMoney(p.price) }}</span>
        <s v-if="p.original_price" class="price text-xs text-muted">{{ formatMoney(p.original_price) }}</s>
      </span>
      <span v-if="p.stock <= 0" class="text-xs font-semibold text-danger">Out of stock</span>
      <span v-else-if="p.stock <= 5" class="text-xs font-semibold text-warning">Only {{ p.stock }} left</span>
      <span v-if="selected === p.id" class="absolute -top-2 -right-2 grid size-6 place-items-center rounded-full bg-primary text-on-primary" aria-hidden="true">
        <Check class="size-3.5" stroke-width="3" />
      </span>
    </button>
  </div>
</template>
