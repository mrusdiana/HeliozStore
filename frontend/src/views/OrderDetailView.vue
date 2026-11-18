<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import OrderPanel from '@/components/store/OrderPanel.vue'
import StatusState from '@/components/ui/StatusState.vue'
import { orders } from '@/data/mock'

const route = useRoute()
// TODO: API — GET /orders/{number} (response is wrapped in `data`); while status is 'paid', refetch every 3s until 'completed'.
const order = computed(() => orders.find((o) => o.order_number === route.params.number))

async function check(done) {
  // TODO: API — POST /orders/{number}/check-payment and replace the order with the response.
  await new Promise((r) => setTimeout(r, 600))
  done()
}
function pay() {
  // TODO: API — POST /orders/{number}/pay → window.snap.pay(snap_token).
}
</script>

<template>
  <div class="container-app max-w-3xl pt-6">
    <StatusState v-if="!order" title="We couldn't find that.">
      <RouterLink to="/orders" class="btn-secondary mt-2">My orders</RouterLink>
    </StatusState>
    <OrderPanel v-else :order="order" @check="check" @pay="pay" />
  </div>
</template>
