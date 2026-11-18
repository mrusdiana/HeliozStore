<script setup>
import { computed } from 'vue'
import { Clock, CreditCard, CheckCircle2, XCircle } from 'lucide-vue-next'

const props = defineProps({ status: { type: String, required: true } })

const steps = computed(() => {
  if (props.status === 'cancelled') {
    return [
      { label: 'Unpaid', icon: Clock, state: 'done' },
      { label: 'Cancelled', icon: XCircle, state: 'danger' },
    ]
  }
  const order = ['pending_payment', 'paid', 'completed']
  const at = order.indexOf(props.status)
  return [
    { label: 'Unpaid', icon: Clock },
    { label: 'Paid', icon: CreditCard },
    { label: 'Completed', icon: CheckCircle2 },
  ].map((s, i) => ({ ...s, state: i < at ? 'done' : i === at ? (i === 2 ? 'success' : 'active') : 'todo' }))
})

const tone = {
  done: 'border-primary/50 bg-primary/10 text-primary',
  active: 'border-primary bg-primary/15 text-primary shadow-glow',
  success: 'border-success bg-success/15 text-success',
  danger: 'border-danger bg-danger/15 text-danger',
  todo: 'border-line bg-surface-2 text-muted',
}
</script>

<template>
  <ol class="flex items-start" aria-label="Order progress">
    <li v-for="(s, i) in steps" :key="s.label" class="flex flex-1 flex-col items-center gap-2 text-center" :class="i < steps.length - 1 && 'relative'">
      <span class="relative z-10 grid size-10 place-items-center rounded-full border" :class="tone[s.state]" :aria-current="s.state === 'active' ? 'step' : undefined">
        <component :is="s.icon" class="size-5" aria-hidden="true" />
      </span>
      <span class="text-xs font-semibold" :class="s.state === 'todo' ? 'text-muted' : 'text-text'">{{ s.label }}</span>
      <span v-if="i < steps.length - 1" class="absolute top-5 left-1/2 h-px w-full" :class="steps[i + 1].state === 'todo' ? 'bg-line' : 'bg-primary/50'" aria-hidden="true" />
    </li>
  </ol>
</template>
