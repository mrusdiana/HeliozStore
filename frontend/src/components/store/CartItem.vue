<script setup>
import { Trash2, AlertCircle } from 'lucide-vue-next'
import AppImage from '@/components/ui/AppImage.vue'
import QuantityStepper from '@/components/ui/QuantityStepper.vue'
import { formatAccount, formatMoney } from '@/utils/format'

defineProps({ item: { type: Object, required: true } })
defineEmits(['update:quantity', 'remove'])

const problemText = {
  insufficient_stock: 'Not enough stock. Lower the quantity.',
  unavailable: 'This package is no longer available. Remove it to continue.',
}
</script>

<template>
  <li class="flex flex-wrap gap-3 p-4 sm:flex-nowrap sm:items-center" :class="item.problem && 'bg-danger/5'">
    <div class="size-14 shrink-0 overflow-hidden rounded-img">
      <AppImage :src="item.game.image_url" :alt="item.game.name" />
    </div>
    <div class="min-w-0 flex-1">
      <p class="font-semibold">{{ item.product.name }}</p>
      <p class="truncate font-mono text-[13px] text-muted">{{ formatAccount(item.account_data) }}</p>
      <p v-if="item.problem" class="mt-1 flex items-center gap-1 text-[13px] text-danger">
        <AlertCircle class="size-3.5 shrink-0" aria-hidden="true" />{{ problemText[item.problem] }}
      </p>
    </div>
    <div class="flex w-full items-center justify-between gap-3 sm:w-auto">
      <QuantityStepper :model-value="item.quantity" :max="item.product.stock" @update:model-value="$emit('update:quantity', $event)" />
      <span class="price w-28 text-right">{{ formatMoney(item.product.price * item.quantity) }}</span>
      <button type="button" class="btn-icon text-muted hover:bg-danger/10 hover:text-danger" :aria-label="`Remove ${item.product.name}`" @click="$emit('remove')">
        <Trash2 class="size-4" />
      </button>
    </div>
  </li>
</template>
