// Quick self-check: node src/utils/format.test.js
import assert from 'node:assert'
import { formatMoney, formatAccount, formatDate } from './format.js'

assert.equal(formatMoney(25000), 'Rp 25.000')
assert.equal(formatAccount({ user_id: '123456789', zone_id: '1234' }), 'ID 123456789 (1234)')
assert.equal(formatAccount({ user_id: '99' }), 'ID 99')
assert.equal(formatDate('2026-11-14T19:05:00'), '14 Nov 2026, 19:05')
console.log('format ok')
