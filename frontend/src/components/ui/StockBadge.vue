<script setup>
defineProps({ stock: { type: Number, required: true } })
</script>

<template>
  <span v-if="stock <= 0" class="pill bg-danger/12 text-danger">Out of stock</span>
  <span v-else-if="stock <= 5" class="pill bg-warning/12 text-warning">Only {{ stock }} left</span>
  <span v-else class="pill bg-success/12 text-success">In stock</span>
</template>
