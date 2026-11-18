<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import PromoCard from './PromoCard.vue'

defineProps({ promos: { type: Array, required: true } })

// Counts down to the end of the local day. shortcut: no end time from the API yet; add one if sales get real schedules.
const left = ref(['00', '00', '00'])
let timer
function tick() {
  const end = new Date()
  end.setHours(23, 59, 59, 999)
  const s = Math.max(0, Math.floor((end - Date.now()) / 1000))
  left.value = [s / 3600, (s % 3600) / 60, s % 60].map((n) => String(Math.floor(n)).padStart(2, '0'))
}
onMounted(() => {
  tick()
  timer = setInterval(tick, 1000)
})
onBeforeUnmount(() => clearInterval(timer))
</script>

<template>
  <section class="rounded-[18px] border border-primary/40 bg-surface p-3 sm:p-5" aria-labelledby="flash-title">
    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
      <!-- Blinking live dot -->
      <span class="relative flex size-3 shrink-0" aria-hidden="true">
        <span class="absolute inline-flex size-full animate-ping rounded-full bg-primary-hover opacity-75" />
        <span class="relative inline-flex size-3 rounded-full bg-primary" />
      </span>
      <div>
        <h2 id="flash-title" class="text-base leading-tight font-bold tracking-tight sm:text-xl">Flash Sale</h2>
        <p class="hidden text-[13px] text-muted sm:block">Special prices for today</p>
      </div>
      <div class="ml-auto flex items-center gap-1 sm:gap-2" role="timer" :aria-label="`Ends in ${left.join(':')}`">
        <span class="hidden text-[13px] text-muted sm:inline">Ends in</span>
        <template v-for="(part, i) in left" :key="i">
          <span v-if="i" class="font-mono text-sm font-bold text-primary-hover" aria-hidden="true">:</span>
          <span class="grid h-7 min-w-7 place-items-center rounded-md border border-line bg-bg px-1 font-mono text-sm font-bold tabular-nums sm:h-9 sm:min-w-9 sm:text-base" aria-hidden="true">{{ part }}</span>
        </template>
      </div>
    </div>
    <div class="mt-3 grid grid-cols-2 gap-2 sm:mt-4 sm:gap-2.5 lg:grid-cols-4">
      <PromoCard v-for="p in promos" :key="p.id" :promo="p" />
    </div>
  </section>
</template>
