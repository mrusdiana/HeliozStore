// Display formatters from DESIGN.md §6. Pure functions, safe to reuse after API integration.
const money = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 })
const date = new Intl.DateTimeFormat('en-GB', {
  day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false,
})

export const formatMoney = (value) => money.format(Number(value) || 0).replace(/\s/g, ' ')
export const formatDate = (value) => (value ? date.format(new Date(value)).replace(' at ', ', ') : '')

// { user_id: '123', zone_id: '45' } -> "ID 123 (45)"
export function formatAccount(data = {}) {
  const [first, ...rest] = Object.values(data).filter(Boolean)
  if (!first) return ''
  return rest.length ? `ID ${first} (${rest.join(' / ')})` : `ID ${first}`
}

export const ORDER_STATUS = {
  pending_payment: { label: 'Unpaid', icon: 'Clock', tone: 'warning' },
  paid: { label: 'Paid', icon: 'CreditCard', tone: 'primary' },
  completed: { label: 'Completed', icon: 'CheckCircle2', tone: 'success' },
  cancelled: { label: 'Cancelled', icon: 'XCircle', tone: 'danger' },
}
