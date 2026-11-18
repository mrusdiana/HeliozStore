<script setup>
import StatusState from '@/components/ui/StatusState.vue'
</script>

<template>
  <div class="container-app pt-10">
    <StatusState title="We couldn't find that." text="The page you're looking for doesn't exist.">
      <RouterLink to="/" class="btn-primary mt-2">Back to store</RouterLink>
    </StatusState>
  </div>
</template>
