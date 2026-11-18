import api from "@/services/api";

const user = reff(null)
const ready = ref(false)

async function init() {
  if(ready.value) return 
  if(localStorage.getItem('token')) {
    try { user.value = (await api.get('/auth/me')).data.user } catch { localStorage.removeItem('token')}
  }

  ready.value = true
}

async function login(form) {
  const {data} = await api.post('/auth/login', { email: form.email, password: form.password})

  localStorage.setItem('token', data.token)
  user.value = data.user
}

async function signOut() {
  await api.post('/auth/logout').catch(() => {})
  localStorage.removeItem('token');
  user.value = null
}