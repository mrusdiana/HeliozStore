<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Menu, X, ShoppingCart, ShoppingBag, PackageSearch, Newspaper, Receipt, LayoutDashboard, LogIn, LogOut, UserPlus } from 'lucide-vue-next'
import AppLogo from '@/components/ui/AppLogo.vue'
import SearchBar from './SearchBar.vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth = useAuthStore()
const cart = useCartStore()
const route = useRoute()
const menuOpen = ref(false)
watch(() => route.fullPath, () => (menuOpen.value = false))

// Row 2 links. "Top up" counts as active on home and game pages.
const main = computed(() => [
  { to: '/', label: 'Top up', icon: ShoppingBag, active: route.name === 'home' || route.name === 'game' },
  { to: '/track', label: 'Track order', icon: PackageSearch, active: route.name === 'track' },
  { to: '/#news', label: 'News', icon: Newspaper, active: false },
])
const link = 'relative flex h-11 shrink-0 items-center gap-2 text-sm font-semibold transition hover:text-text'
const underline = 'after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:rounded-full after:bg-primary'
</script>

<template>
  <header class="sticky top-0 z-40 border-b border-line bg-glass backdrop-blur-xl">
    <!-- Row 1: logo, search, cart -->
    <div class="container-app flex h-14 items-center gap-3 sm:h-16 sm:gap-6">
      <AppLogo />
      <div class="hidden flex-1 md:block"><SearchBar /></div>
      <RouterLink to="/cart" class="btn-icon relative ml-auto text-text hover:bg-surface-2 md:ml-0" :aria-label="`Cart, ${cart.count} items`">
        <ShoppingCart class="size-5" />
        <span v-if="cart.count && auth.isSignedIn" class="absolute top-1 right-1 grid min-w-5 place-items-center rounded-full bg-primary px-1 font-mono text-[11px] font-semibold text-on-primary" aria-hidden="true">
          {{ cart.count }}
        </span>
      </RouterLink>
      <button type="button" class="btn-icon -mr-2 text-text hover:bg-surface-2 md:hidden" :aria-label="menuOpen ? 'Close menu' : 'Open menu'" :aria-expanded="menuOpen" aria-controls="mobile-menu" @click="menuOpen = !menuOpen">
        <X v-if="menuOpen" class="size-5" /><Menu v-else class="size-5" />
      </button>
    </div>
    <div class="container-app pb-2.5 md:hidden"><SearchBar /></div>

    <!-- Mobile: hamburger menu -->
    <Transition enter-from-class="opacity-0 -translate-y-1" leave-to-class="opacity-0" enter-active-class="transition duration-200" leave-active-class="transition duration-150">
      <nav v-if="menuOpen" id="mobile-menu" class="border-t border-line md:hidden" aria-label="Mobile">
        <ul class="container-app py-2">
          <li v-for="l in main" :key="l.label">
            <RouterLink :to="l.to" class="flex min-h-11 items-center gap-3 rounded-btn px-2 text-sm font-semibold" :class="l.active ? 'bg-primary/10 text-text' : 'text-muted'">
              <component :is="l.icon" class="size-4" :class="l.active && 'text-primary-hover'" aria-hidden="true" />{{ l.label }}
            </RouterLink>
          </li>
          <li class="my-2 border-t border-line" role="separator" />
          <template v-if="auth.isSignedIn">
            <li><RouterLink to="/orders" class="flex min-h-11 items-center gap-3 px-2 text-sm font-semibold text-muted"><Receipt class="size-4" aria-hidden="true" />My orders</RouterLink></li>
            <li v-if="auth.isAdmin"><RouterLink to="/admin" class="flex min-h-11 items-center gap-3 px-2 text-sm font-semibold text-muted"><LayoutDashboard class="size-4" aria-hidden="true" />Admin</RouterLink></li>
            <li><button type="button" class="flex min-h-11 w-full items-center gap-3 px-2 text-sm font-semibold text-muted" @click="auth.signOut()"><LogOut class="size-4" aria-hidden="true" />Sign out</button></li>
          </template>
          <li v-else class="grid grid-cols-2 gap-2 pt-1 pb-2">
            <RouterLink to="/login" class="btn-secondary"><LogIn class="size-4" aria-hidden="true" />Sign in</RouterLink>
            <RouterLink to="/register" class="btn-primary"><UserPlus class="size-4" aria-hidden="true" />Register</RouterLink>
          </li>
        </ul>
      </nav>
    </Transition>

    <!-- Desktop row 2: navigation -->
    <nav class="hidden border-t border-line/60 md:block" aria-label="Main">
      <div class="container-app flex items-center gap-7">
        <RouterLink v-for="l in main" :key="l.label" :to="l.to" :class="[link, l.active ? `text-text ${underline}` : 'text-muted']" :aria-current="l.active ? 'page' : undefined">
          <component :is="l.icon" class="size-4" :class="l.active && 'text-primary-hover'" aria-hidden="true" />{{ l.label }}
        </RouterLink>

        <div class="ml-auto flex items-center gap-5 pl-4 sm:gap-7">
          <template v-if="auth.isSignedIn">
            <RouterLink to="/orders" :class="[link, route.path.startsWith('/orders') ? `text-text ${underline}` : 'text-muted']">
              <Receipt class="size-4" aria-hidden="true" />My orders
            </RouterLink>
            <RouterLink v-if="auth.isAdmin" to="/admin" :class="[link, 'text-muted']"><LayoutDashboard class="size-4" aria-hidden="true" />Admin</RouterLink>
            <button type="button" :class="[link, 'cursor-pointer text-muted']" @click="auth.signOut()"><LogOut class="size-4" aria-hidden="true" />Sign out</button>
          </template>
          <template v-else>
            <RouterLink to="/login" :class="[link, route.name === 'login' ? `text-text ${underline}` : 'text-muted']"><LogIn class="size-4" aria-hidden="true" />Sign in</RouterLink>
            <RouterLink to="/register" :class="[link, route.name === 'register' ? `text-text ${underline}` : 'text-muted']"><UserPlus class="size-4" aria-hidden="true" />Register</RouterLink>
          </template>
        </div>
      </div>
    </nav>
  </header>
</template>
