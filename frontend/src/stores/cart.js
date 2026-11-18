import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { cart as mockCart } from '@/data/mock'

export const useCartStore = defineStore('cart', () => {
  // TODO: API — fill from GET /cart; replace with the response after POST/PATCH/DELETE /cart/items.
  const items = ref(mockCart.items)
  const count = computed(() => items.value.reduce((s, i) => s + i.quantity, 0))

  // Buy now item lives only in memory (DESIGN.md §5.1 /checkout). A refresh clears it on purpose.
  const buyNowItem = ref(null)

  return { items, count, buyNowItem }
})
