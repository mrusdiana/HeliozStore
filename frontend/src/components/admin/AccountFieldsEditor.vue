<script setup>
import { Plus, Trash2 } from 'lucide-vue-next'

const rows = defineModel({ type: Array, required: true })
const add = () => rows.value.push({ key: '', label: '', type: 'numeric', min: 1, max: 20, placeholder: '' })
const cols = [
  { key: 'key', label: 'Key', type: 'text' },
  { key: 'label', label: 'Label', type: 'text' },
  { key: 'min', label: 'Min', type: 'number' },
  { key: 'max', label: 'Max', type: 'number' },
  { key: 'placeholder', label: 'Placeholder', type: 'text' },
]
</script>

<template>
  <fieldset>
    <legend class="label">Account fields</legend>
    <div class="space-y-3">
      <div v-for="(row, i) in rows" :key="i" class="grid grid-cols-2 gap-2 rounded-btn border border-line bg-surface-2/50 p-3 sm:grid-cols-[1fr_1fr_120px_72px_72px_1fr_auto] sm:items-end">
        <label v-for="c in cols.slice(0, 2)" :key="c.key" class="text-xs text-muted">
          {{ c.label }}<input v-model="row[c.key]" class="field mt-1 min-h-10 text-sm" />
        </label>
        <label class="text-xs text-muted">
          Type
          <select v-model="row.type" class="field mt-1 min-h-10 text-sm"><option value="numeric">Numeric</option><option value="text">Text</option></select>
        </label>
        <label v-for="c in cols.slice(2)" :key="c.key" class="text-xs text-muted">
          {{ c.label }}<input v-model="row[c.key]" :type="c.type" class="field mt-1 min-h-10 text-sm" />
        </label>
        <button type="button" class="btn-icon text-muted hover:text-danger disabled:opacity-30" :disabled="rows.length <= 1" :aria-label="`Remove field ${i + 1}`" @click="rows.splice(i, 1)">
          <Trash2 class="size-4" />
        </button>
      </div>
    </div>
    <button type="button" class="btn-ghost mt-2" @click="add"><Plus class="size-4" aria-hidden="true" />Add field</button>
  </fieldset>
</template>
