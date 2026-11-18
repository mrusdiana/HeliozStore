<script setup>
import { computed } from 'vue'

// Original SVG item art per in-game currency (no official game assets). Unknown currencies get a generic coin.
const props = defineProps({ currency: { type: String, default: '' } })

const KINDS = {
  diamonds: 'diamond',
  'genesis crystals': 'crystal',
  'oneiric shards': 'crystal',
  robux: 'robux',
  vp: 'vp',
  uc: 'coin',
  cp: 'coin',
  tokens: 'coin',
}
const kind = computed(() => KINDS[props.currency.toLowerCase()] ?? 'coin')
const label = computed(
  () => ({ uc: 'UC', cp: 'CP', tokens: 'T', gold: 'G', 'delta coins': 'D', monochrome: 'M', vouchers: 'V', 'efootball coins': 'eF' })[props.currency.toLowerCase()] ?? '$',
)
const uid = `i${Math.random().toString(36).slice(2, 8)}`
</script>

<template>
  <svg viewBox="0 0 48 48" aria-hidden="true">
    <!-- Diamond -->
    <template v-if="kind === 'diamond'">
      <defs><linearGradient :id="uid" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#BAE6FD" /><stop offset="1" stop-color="#0284C7" /></linearGradient></defs>
      <path d="M14 8h20l9 11-19 23L5 19z" :fill="`url(#${uid})`" />
      <path d="M5 19h38M14 8l5 11 5-11 5 11 5-11M19 19l5 23 5-23" fill="none" stroke="#E0F2FE" stroke-opacity=".7" stroke-width="1.4" stroke-linejoin="round" />
    </template>
    <!-- Crystal -->
    <template v-else-if="kind === 'crystal'">
      <defs><linearGradient :id="uid" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F5D0FE" /><stop offset="1" stop-color="#7C3AED" /></linearGradient></defs>
      <path d="M24 3 31 17 45 24 31 31 24 45 17 31 3 24 17 17z" :fill="`url(#${uid})`" />
      <path d="M24 3v42M3 24h42" stroke="#FAE8FF" stroke-opacity=".6" stroke-width="1.2" />
    </template>
    <!-- Robux-style hexagon -->
    <template v-else-if="kind === 'robux'">
      <defs><linearGradient :id="uid" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#6EE7B7" /><stop offset="1" stop-color="#047857" /></linearGradient></defs>
      <path d="M24 4 41 14v20L24 44 7 34V14z" :fill="`url(#${uid})`" />
      <path d="M24 13 33 18.5v11L24 35l-9-5.5v-11z" fill="none" stroke="#ECFDF5" stroke-width="3" stroke-linejoin="round" />
    </template>
    <!-- VP-style shard -->
    <template v-else-if="kind === 'vp'">
      <defs><linearGradient :id="uid" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FDA4AF" /><stop offset="1" stop-color="#BE123C" /></linearGradient></defs>
      <circle cx="24" cy="24" r="20" :fill="`url(#${uid})`" />
      <path d="M14 15h6l4 11 4-11h6l-8 18h-4z" fill="#FFF1F2" />
    </template>
    <!-- Coin -->
    <template v-else>
      <defs><linearGradient :id="uid" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FDE68A" /><stop offset="1" stop-color="#D97706" /></linearGradient></defs>
      <circle cx="24" cy="24" r="20" :fill="`url(#${uid})`" />
      <circle cx="24" cy="24" r="15" fill="none" stroke="#FFFBEB" stroke-opacity=".6" stroke-width="1.5" />
      <text x="24" y="29.5" text-anchor="middle" font-family="Space Grotesk, sans-serif" font-weight="700" :font-size="label.length > 1 ? 13 : 16" fill="#78350F">{{ label }}</text>
    </template>
  </svg>
</template>
