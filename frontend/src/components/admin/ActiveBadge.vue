<script setup>
defineProps({ active: Boolean })
</script>

<template>
  <span class="pill" :class="active ? 'bg-success/12 text-success' : 'bg-surface-2 text-muted'">{{ active ? 'Active' : 'Inactive' }}</span>
</template>
