<script setup>
import { computed } from 'vue'
import { Clock, CreditCard, CheckCircle2, XCircle } from 'lucide-vue-next'
import { ORDER_STATUS } from '@/utils/format'

const props = defineProps({ status: { type: String, required: true } })
const icons = { Clock, CreditCard, CheckCircle2, XCircle }
const tones = {
  warning: 'bg-warning/12 text-warning',
  primary: 'bg-primary/12 text-primary',
  success: 'bg-success/12 text-success',
  danger: 'bg-danger/12 text-danger',
}
const meta = computed(() => ORDER_STATUS[props.status] ?? ORDER_STATUS.pending_payment)
</script>

<template>
  <span class="pill" :class="tones[meta.tone]">
    <component :is="icons[meta.icon]" class="size-3.5" aria-hidden="true" />
    {{ meta.label }}
  </span>
</template>
