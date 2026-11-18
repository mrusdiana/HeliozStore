<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/admin/PageHeader.vue'
import ImageUpload from '@/components/admin/ImageUpload.vue'
import { adminGames, banners } from '@/data/mock'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const toast = useToastStore()
// TODO: API — when editing, load the banner from the API instead of the mock.
const editing = banners.find((b) => b.id === Number(route.params.id))

const form = reactive({
  title: editing?.title ?? '',
  subtitle: editing?.subtitle ?? '',
  game_id: adminGames.find((g) => g.slug === editing?.game_slug)?.id ?? adminGames[0].id,
  sort_order: editing?.sort_order ?? 0,
  is_active: editing?.is_active ?? true,
  image: null,
})
const saving = ref(false)

async function save() {
  saving.value = true
  // TODO: API — FormData → POST /admin/banners or POST /admin/banners/{id} with _method=PUT.
  await new Promise((r) => setTimeout(r, 500))
  saving.value = false
  toast.show('Banner saved')
  router.push('/admin/banners')
}
</script>

<template>
  <PageHeader :title="editing ? 'Edit banner' : 'New banner'" back-to="/admin/banners" />
  <form class="card max-w-3xl space-y-5 p-4 sm:p-6" @submit.prevent="save">
    <ImageUpload v-model="form.image" :current="editing?.image_url" wide label="Image (wide, about 16:5)" />
    <div class="grid gap-5 sm:grid-cols-2">
      <div><label for="title" class="label">Title</label><input id="title" v-model="form.title" class="field" required /></div>
      <div><label for="subtitle" class="label">Subtitle</label><input id="subtitle" v-model="form.subtitle" class="field" placeholder="Save up to 10% this week" /></div>
      <div>
        <label for="bgame" class="label">Game</label>
        <select id="bgame" v-model.number="form.game_id" class="field"><option v-for="g in adminGames" :key="g.id" :value="g.id">{{ g.name }}</option></select>
        <p class="hint">Where the banner links to.</p>
      </div>
      <div><label for="bsort" class="label">Sort order</label><input id="bsort" v-model.number="form.sort_order" type="number" min="0" class="field" /></div>
    </div>
    <label class="flex min-h-11 cursor-pointer items-center gap-3">
      <input v-model="form.is_active" type="checkbox" class="size-5 accent-primary" />
      <span class="font-semibold">Active</span><span class="text-sm text-muted">Shown on the home page</span>
    </label>
    <div class="flex justify-end gap-2 border-t border-line pt-5">
      <RouterLink to="/admin/banners" class="btn-ghost">Cancel</RouterLink>
      <button type="submit" class="btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Save banner' }}</button>
    </div>
  </form>
</template>
