<script setup>
import { computed, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLogo from '@/components/ui/AppLogo.vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({ mode: { type: String, default: 'login' } })
const route = useRoute()
const router = useRouter()
const isLogin = computed(() => props.mode === 'login')
const auth = useAuthStore()

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' })
const errors = ref({}) // fill from 422 `errors` (e.g. { email: ['Incorrect email or password.'] })
const submitting = ref(false)

async function submit() {
  submitting.value = true
  // TODO: API — POST /auth/login or /auth/register, save the token, set auth.user.
  await new Promise((r) => setTimeout(r, 500))
  auth.mockSignIn(form)
  submitting.value = false
  router.push(route.query.redirect || (auth.isAdmin ? '/admin' : '/'))
}

const fields = computed(() => [
  ...(isLogin.value ? [] : [{ key: 'name', label: 'Name', type: 'text', autocomplete: 'name' }]),
  { key: 'email', label: 'Email', type: 'email', autocomplete: 'email' },
  { key: 'password', label: 'Password', type: 'password', autocomplete: isLogin.value ? 'current-password' : 'new-password' },
  ...(isLogin.value ? [] : [{ key: 'password_confirmation', label: 'Confirm password', type: 'password', autocomplete: 'new-password' }]),
])
</script>

<template>
  <div class="container-app grid place-items-center py-10 sm:py-16">
    <div class="card w-full max-w-md p-6 shadow-2xl shadow-primary/5 sm:p-8">
      <div class="mb-6 flex flex-col items-center gap-4 text-center">
        <AppLogo />
        <h1 class="text-2xl font-bold tracking-tight">{{ isLogin ? 'Sign in' : 'Create account' }}</h1>
      </div>
      <form class="space-y-4" novalidate @submit.prevent="submit">
        <div v-for="f in fields" :key="f.key">
          <label :for="f.key" class="label">{{ f.label }}</label>
          <input
            :id="f.key"
            v-model="form[f.key]"
            :type="f.type"
            :autocomplete="f.autocomplete"
            class="field"
            required
            :aria-invalid="!!errors[f.key]"
            :aria-describedby="errors[f.key] ? `${f.key}-err` : undefined"
          />
          <p v-if="errors[f.key]" :id="`${f.key}-err`" class="error-text">{{ errors[f.key][0] }}</p>
        </div>
        <button type="submit" class="btn-primary w-full" :disabled="submitting">
          {{ submitting ? (isLogin ? 'Signing in…' : 'Creating account…') : isLogin ? 'Sign in' : 'Create account' }}
        </button>
      </form>
      <p class="mt-4 rounded-btn bg-surface-2 p-3 text-center text-xs text-muted">
        Demo: any email signs in as a buyer. Use <span class="font-mono text-text">admin@example.com</span> for the admin panel.
      </p>
      <p class="mt-6 text-center text-sm text-muted">
        {{ isLogin ? 'New here?' : 'Already have an account?' }}
        <RouterLink :to="{ name: isLogin ? 'register' : 'login', query: route.query }" class="font-semibold text-primary hover:underline">
          {{ isLogin ? 'Create an account' : 'Sign in' }}
        </RouterLink>
      </p>
    </div>
  </div>
</template>
