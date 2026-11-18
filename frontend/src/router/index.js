import { createRouter, createWebHistory } from 'vue-router'
import StoreLayout from '@/layouts/StoreLayout.vue'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

// Views are lazy-loaded so the first page stays light.
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior: (to, from, saved) => saved ?? (to.hash ? { el: to.hash, top: 130, behavior: 'smooth' } : { top: 0 }),
  routes: [
    {
      path: '/',
      component: StoreLayout,
      children: [
        { path: '', name: 'home', component: () => import('@/views/HomeView.vue') },
        { path: 'games/:slug', name: 'game', component: () => import('@/views/GameView.vue') },
        { path: 'cart', name: 'cart', component: () => import('@/views/CartView.vue'), meta: { auth: true } },
        // Buy now works for guests; the cart mode checks sign-in inside the view.
        { path: 'checkout', name: 'checkout', component: () => import('@/views/CheckoutView.vue') },
        { path: 'track', name: 'track', component: () => import('@/views/TrackView.vue') },
        { path: 'orders', name: 'orders', component: () => import('@/views/OrdersView.vue'), meta: { auth: true } },
        { path: 'orders/:number', name: 'order', component: () => import('@/views/OrderDetailView.vue'), meta: { auth: true } },
        { path: 'login', name: 'login', component: () => import('@/views/AuthView.vue'), props: { mode: 'login' } },
        { path: 'register', name: 'register', component: () => import('@/views/AuthView.vue'), props: { mode: 'register' } },
      ],
    },
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { auth: true, admin: true },
      children: [
        { path: '', name: 'admin-dashboard', component: () => import('@/views/admin/DashboardView.vue') },
        { path: 'orders', name: 'admin-orders', component: () => import('@/views/admin/OrdersView.vue') },
        { path: 'orders/:number', name: 'admin-order', component: () => import('@/views/admin/OrderDetailView.vue') },
        { path: 'games', name: 'admin-games', component: () => import('@/views/admin/GamesView.vue') },
        { path: 'games/new', name: 'admin-game-new', component: () => import('@/views/admin/GameFormView.vue') },
        { path: 'games/:id/edit', name: 'admin-game-edit', component: () => import('@/views/admin/GameFormView.vue') },
        { path: 'packages', name: 'admin-packages', component: () => import('@/views/admin/PackagesView.vue') },
        { path: 'packages/new', name: 'admin-package-new', component: () => import('@/views/admin/PackageFormView.vue') },
        { path: 'packages/:id/edit', name: 'admin-package-edit', component: () => import('@/views/admin/PackageFormView.vue') },
        { path: 'banners', name: 'admin-banners', component: () => import('@/views/admin/BannersView.vue') },
        { path: 'banners/new', name: 'admin-banner-new', component: () => import('@/views/admin/BannerFormView.vue') },
        { path: 'banners/:id/edit', name: 'admin-banner-edit', component: () => import('@/views/admin/BannerFormView.vue') },
      ],
    },
    { path: '/:pathMatch(.*)*', component: StoreLayout, children: [{ path: '', component: () => import('@/views/NotFoundView.vue') }] },
  ],
})

// DESIGN.md §8.4: 401 → /login then back, 403 → / with a toast.
router.beforeEach(async (to) => {
  const auth = useAuthStore()
  await auth.init()
  if (to.meta.auth && !auth.isSignedIn) return { name: 'login', query: { redirect: to.fullPath } }
  if (to.meta.admin && !auth.isAdmin) {
    useToastStore().show("You don't have access to that page.", 'danger')
    return { name: 'home' }
  }
})

export default router
