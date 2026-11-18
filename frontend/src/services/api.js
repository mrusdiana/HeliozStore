import axios from 'axios'
import router from '@/router'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: { Accept: 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`

  return config
})

api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) {
      localStorage.removeItem('token')
      router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
    }
    return Promise.reject(err)
  },
)

export function errorMessage(err) {
    const s = err.response?.status
    if (s === 502) return 'Payment service is unavailable. Please try again.'
    if (s === 404) return "We couldn't find that."
    return err.response?.data?.message ?? 'Something went wrong. Please try again.'
}

export default api;
