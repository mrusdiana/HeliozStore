<script setup>
import { computed } from 'vue'
import ItemIcon from '@/components/ui/ItemIcon.vue'
import { formatMoney } from '@/utils/format'

const props = defineProps({ promo: { type: Object, required: true } })
const discount = computed(() => Math.round((1 - props.promo.price / props.promo.original_price) * 100))
const soldOut = computed(() => props.promo.stock <= 0)
</script>

<template>
  <RouterLink
    :to="{ name: 'game', params: { slug: promo.game.slug }, query: { package: promo.id } }"
    class="relative flex flex-col gap-2 rounded-btn border border-line bg-bg p-2.5 transition duration-200 ease-out-soft hover:border-primary/60 sm:flex-row sm:items-center sm:gap-3 sm:p-3"
    :class="{ 'pointer-events-none opacity-45': soldOut }"
  >
    <ItemIcon :currency="promo.game.currency_name" class="size-8 shrink-0 sm:size-10" />
    <div class="min-w-0 flex-1">
      <p class="truncate font-mono text-[11px] text-muted sm:pr-12 sm:text-xs">{{ promo.game.name }}</p>
      <p class="truncate text-[13px] font-semibold sm:text-sm">{{ promo.name }}</p>
      <p class="flex flex-wrap items-baseline gap-x-1.5">
        <span class="price text-sm sm:text-[15px]">{{ formatMoney(promo.price) }}</span>
        <s class="price text-[11px] text-muted sm:text-xs">{{ formatMoney(promo.original_price) }}</s>
      </p>
      <div class="mt-1.5 h-1 overflow-hidden rounded-full bg-surface-2" aria-hidden="true">
        <div class="h-full rounded-full bg-primary" :style="{ width: `${Math.max(15, 100 - Math.min(promo.stock, 100))}%` }" />
      </div>
    </div>
    <span class="absolute top-2 right-2 rounded-md bg-primary px-1.5 py-0.5 font-mono text-[11px] font-bold text-on-primary sm:text-xs">
      {{ soldOut ? 'Sold out' : `-${discount}%` }}
    </span>
  </RouterLink>
</template>
