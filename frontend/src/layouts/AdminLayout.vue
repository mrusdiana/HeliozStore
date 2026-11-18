<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { LayoutDashboard, Receipt, Gamepad2, Package, Image, Store, LogOut, Menu, X } from 'lucide-vue-next'
import AppLogo from '@/components/ui/AppLogo.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()
const open = ref(false)
watch(() => route.fullPath, () => (open.value = false))

const links = [
  { to: '/admin', label: 'Dashboard', icon: LayoutDashboard, exact: true },
  { to: '/admin/orders', label: 'Orders', icon: Receipt },
  { to: '/admin/games', label: 'Games', icon: Gamepad2 },
  { to: '/admin/packages', label: 'Packages', icon: Package },
  { to: '/admin/banners', label: 'Banners', icon: Image },
]
const item = 'flex min-h-11 items-center gap-3 rounded-btn px-3 text-sm font-semibold text-muted transition hover:bg-surface-2 hover:text-text'

function signOut() {
  auth.signOut()
  router.push('/')
}
</script>

<template>
  <div class="min-h-dvh lg:grid lg:grid-cols-[240px_1fr]">
    <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-line bg-glass px-4 backdrop-blur-xl lg:hidden">
      <AppLogo />
      <button type="button" class="btn-icon text-text hover:bg-surface-2" :aria-label="open ? 'Close menu' : 'Open menu'" :aria-expanded="open" @click="open = !open">
        <X v-if="open" class="size-5" /><Menu v-else class="size-5" />
      </button>
    </header>

    <aside
      class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col gap-1 border-r border-line bg-surface p-4 transition-transform duration-200 lg:sticky lg:top-0 lg:h-dvh lg:w-auto lg:translate-x-0"
      :class="open ? 'translate-x-0' : '-translate-x-full'"
      aria-label="Admin"
    >
      <div class="mb-4 px-2 pt-1"><AppLogo /><p class="mt-2 text-xs font-semibold tracking-wider text-muted uppercase">Admin panel</p></div>
      <RouterLink v-for="l in links" :key="l.to" :to="l.to" :class="item" :active-class="l.exact ? '' : '!bg-primary/12 !text-primary'" exact-active-class="!bg-primary/12 !text-primary">
        <component :is="l.icon" class="size-4" aria-hidden="true" />{{ l.label }}
      </RouterLink>
      <div class="mt-auto space-y-1 border-t border-line pt-3">
        <RouterLink to="/" :class="item"><Store class="size-4" aria-hidden="true" />Back to store</RouterLink>
        <button type="button" :class="[item, 'w-full']" @click="signOut"><LogOut class="size-4" aria-hidden="true" />Sign out</button>
      </div>
    </aside>
    <div v-if="open" class="fixed inset-0 z-40 bg-black/60 lg:hidden" aria-hidden="true" @click="open = false" />

    <main class="min-w-0 p-4 sm:p-6 lg:p-8">
      <RouterView />
    </main>
  </div>
</template>
