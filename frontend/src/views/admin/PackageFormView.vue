<script setup>
import { computed, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/admin/PageHeader.vue'
import ImageUpload from '@/components/admin/ImageUpload.vue'
import { adminGames, adminProducts } from '@/data/mock'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const toast = useToastStore()
// TODO: API — when editing, load the package from the API instead of the mock.
const editing = adminProducts.find((p) => p.id === Number(route.params.id))

const form = reactive({
  game_id: editing?.game_id ?? adminGames[0].id,
  name: editing?.name ?? '',
  item_amount: editing?.item_amount ?? null,
  price: editing?.price ?? null,
  original_price: editing?.original_price ?? null,
  stock: editing?.stock ?? 0,
  is_active: editing?.is_active ?? true,
  image: null,
})
const saving = ref(false)
// Mirrors the backend rule so the admin sees it before submitting.
const promoError = computed(() =>
  form.original_price && form.price && form.original_price <= form.price ? 'Original price must be higher than Price.' : '',
)

async function save() {
  if (promoError.value) return
  saving.value = true
  // TODO: API — FormData → POST /admin/products or POST /admin/products/{id} with _method=PUT.
  await new Promise((r) => setTimeout(r, 500))
  saving.value = false
  toast.show('Package saved')
  router.push('/admin/packages')
}
</script>

<template>
  <PageHeader :title="editing ? `Edit ${editing.name}` : 'New package'" back-to="/admin/packages" />
  <form class="card max-w-3xl space-y-5 p-4 sm:p-6" @submit.prevent="save">
    <div class="grid gap-5 sm:grid-cols-2">
      <div>
        <label for="game" class="label">Game</label>
        <select id="game" v-model.number="form.game_id" class="field"><option v-for="g in adminGames" :key="g.id" :value="g.id">{{ g.name }}</option></select>
      </div>
      <div><label for="pname" class="label">Name</label><input id="pname" v-model="form.name" class="field" placeholder="86 Diamonds" required /></div>
      <div><label for="amount" class="label">Item amount</label><input id="amount" v-model.number="form.item_amount" type="number" min="1" inputmode="numeric" class="field" required /></div>
      <div><label for="stock" class="label">Stock</label><input id="stock" v-model.number="form.stock" type="number" min="0" inputmode="numeric" class="field" required /></div>
      <div><label for="price" class="label">Price (Rp)</label><input id="price" v-model.number="form.price" type="number" min="0" inputmode="numeric" class="field font-mono" required /></div>
      <div>
        <label for="original" class="label">Original price (Rp)</label>
        <input id="original" v-model.number="form.original_price" type="number" min="0" inputmode="numeric" class="field font-mono" :aria-invalid="!!promoError" aria-describedby="original-msg" />
        <p id="original-msg" :class="promoError ? 'error-text' : 'hint'">{{ promoError || 'Optional. Fill it to show this package as a promo.' }}</p>
      </div>
    </div>
    <ImageUpload v-model="form.image" :current="editing?.image_url" label="Image (optional)" />
    <label class="flex min-h-11 cursor-pointer items-center gap-3">
      <input v-model="form.is_active" type="checkbox" class="size-5 accent-primary" />
      <span class="font-semibold">Active</span><span class="text-sm text-muted">Can be bought</span>
    </label>
    <div class="flex justify-end gap-2 border-t border-line pt-5">
      <RouterLink to="/admin/packages" class="btn-ghost">Cancel</RouterLink>
      <button type="submit" class="btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Save package' }}</button>
    </div>
  </form>
</template>
