<script setup>
import AppHeader from '@/components/store/AppHeader.vue'
import AppFooter from '@/components/store/AppFooter.vue'
</script>

<template>
  <div class="flex min-h-dvh flex-col">
    <AppHeader />
    <main class="flex-1 pb-12">
      <RouterView />
    </main>
    <AppFooter />
  </div>
</template>
