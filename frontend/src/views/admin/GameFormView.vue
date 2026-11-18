<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/admin/PageHeader.vue'
import ImageUpload from '@/components/admin/ImageUpload.vue'
import AccountFieldsEditor from '@/components/admin/AccountFieldsEditor.vue'
import { adminGames, genres } from '@/data/mock'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const toast = useToastStore()
// TODO: API — when editing, load GET /admin/games/{id} instead of the mock.
const editing = adminGames.find((g) => g.id === Number(route.params.id))

const form = reactive({
  name: editing?.name ?? '',
  genre: editing?.genre ?? '',
  currency_name: editing?.currency_name ?? '',
  account_instructions: editing?.account_instructions ?? '',
  account_fields: structuredClone(editing?.account_fields ?? [{ key: 'user_id', label: 'User ID', type: 'numeric', min: 5, max: 12, placeholder: '' }]),
  is_active: editing?.is_active ?? true,
  sort_order: editing?.sort_order ?? 0,
  image: null,
  banner: null,
})
const errors = ref({})
const saving = ref(false)

async function save() {
  saving.value = true
  // TODO: API — build FormData; POST /admin/games or POST /admin/games/{id} with _method=PUT. Map 422 errors into `errors`.
  await new Promise((r) => setTimeout(r, 500))
  saving.value = false
  toast.show('Game saved')
  router.push('/admin/games')
}
</script>

<template>
  <PageHeader :title="editing ? `Edit ${editing.name}` : 'New game'" back-to="/admin/games" />
  <form class="card max-w-3xl space-y-5 p-4 sm:p-6" @submit.prevent="save">
    <div class="grid gap-5 sm:grid-cols-2">
      <div><label for="name" class="label">Name</label><input id="name" v-model="form.name" class="field" required /></div>
      <div>
        <label for="genre" class="label">Genre</label>
        <input id="genre" v-model="form.genre" class="field" list="genres" required />
        <datalist id="genres"><option v-for="g in genres" :key="g" :value="g" /></datalist>
      </div>
      <div><label for="currency" class="label">Currency name</label><input id="currency" v-model="form.currency_name" class="field" placeholder="Diamonds" required /></div>
      <div><label for="sort" class="label">Sort order</label><input id="sort" v-model.number="form.sort_order" type="number" min="0" class="field" /></div>
    </div>
    <div class="grid gap-5 sm:grid-cols-[auto_1fr]">
      <ImageUpload v-model="form.image" :current="editing?.image_url" label="Icon (1:1)" :error="errors.image?.[0]" />
      <ImageUpload v-model="form.banner" :current="editing?.banner_url" wide label="Game page banner (16:9, optional)" :error="errors.banner?.[0]" />
    </div>
    <div>
      <label for="instructions" class="label">Account instructions</label>
      <textarea id="instructions" v-model="form.account_instructions" rows="3" class="field py-3" />
      <p class="hint">Shown above the account form, e.g. “Find your ID in Profile”.</p>
    </div>
    <AccountFieldsEditor v-model="form.account_fields" />
    <label class="flex min-h-11 cursor-pointer items-center gap-3">
      <input v-model="form.is_active" type="checkbox" class="size-5 accent-primary" />
      <span class="font-semibold">Active</span><span class="text-sm text-muted">Shown in the store</span>
    </label>
    <div class="flex justify-end gap-2 border-t border-line pt-5">
      <RouterLink to="/admin/games" class="btn-ghost">Cancel</RouterLink>
      <button type="submit" class="btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Save game' }}</button>
    </div>
  </form>
</template>
