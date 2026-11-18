<script setup>
import { Instagram, Youtube, Mail, MessageCircle } from 'lucide-vue-next'
import AppLogo from '@/components/ui/AppLogo.vue'

// Social and support links are placeholders until the store has real accounts.
const socials = [
  { icon: Instagram, label: 'Instagram', href: '#' },
  { icon: Youtube, label: 'YouTube', href: '#' },
  { icon: MessageCircle, label: 'WhatsApp', href: '#' },
  { icon: Mail, label: 'Email', href: 'mailto:support@heliozstore.com' },
]
const columns = [
  { title: 'Sitemap', links: [{ label: 'Home', to: '/' }, { label: 'My orders', to: '/orders' }, { label: 'Cart', to: '/cart' }] },
  { title: 'Support', links: [{ label: 'WhatsApp', href: '#' }, { label: 'Instagram', href: '#' }, { label: 'Email', href: 'mailto:support@heliozstore.com' }] },
  { title: 'Legal', links: [{ label: 'Privacy policy', href: '#' }, { label: 'Terms & conditions', href: '#' }] },
]
</script>

<template>
  <footer class="mt-8 border-t border-line bg-surface/60">
    <div class="container-app grid gap-8 py-10 sm:grid-cols-2 lg:grid-cols-[1.6fr_1fr_1fr_1fr]">
      <div class="space-y-4 sm:col-span-2 lg:col-span-1">
        <AppLogo />
        <p class="max-w-sm text-sm text-muted">
          HeliozStore is a fast and secure game top-up store. Most orders arrive within seconds, open 24/7, with QRIS, e-wallets, and bank transfer.
          Need help? Chat with us any time.
        </p>
        <ul class="flex gap-2" aria-label="Social media">
          <li v-for="s in socials" :key="s.label">
            <a :href="s.href" class="grid size-11 place-items-center rounded-btn border border-line text-muted transition hover:border-specular hover:text-primary" :aria-label="s.label">
              <component :is="s.icon" class="size-4" />
            </a>
          </li>
        </ul>
      </div>
      <nav v-for="c in columns" :key="c.title" :aria-label="c.title">
        <h2 class="mb-3 font-sans text-sm font-semibold">{{ c.title }}</h2>
        <ul class="space-y-1">
          <li v-for="l in c.links" :key="l.label">
            <RouterLink v-if="l.to" :to="l.to" class="inline-flex min-h-9 items-center text-sm text-muted hover:text-text">{{ l.label }}</RouterLink>
            <a v-else :href="l.href" class="inline-flex min-h-9 items-center text-sm text-muted hover:text-text">{{ l.label }}</a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="border-t border-line py-5 text-center text-xs text-muted">
      <div class="container-app space-y-1">
        <p>© 2026 HeliozStore.com. All rights reserved.</p>
        <p>Demo project for learning purposes. Not affiliated with any game publisher.</p>
      </div>
    </div>
  </footer>
</template>
