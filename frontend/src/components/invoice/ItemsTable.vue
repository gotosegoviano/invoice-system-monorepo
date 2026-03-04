<script setup lang="ts">
import { ref, computed } from 'vue'
import type { InvoiceItem } from '@/types/invoice'

const props = defineProps<{
  items: InvoiceItem[]
}>()

const emit = defineEmits<{
  (e: 'add'): void
  (e: 'remove', id: string): void
}>()

const isServiceCompany = ref(false)
const tooltipOpen = ref(false)

function setServiceCompany(value: boolean) {
  isServiceCompany.value = value
}

// Cálculo de subtotal dinámico
const subtotal = computed(() => {
  return props.items.reduce((acc, item) => {
    const qty = item.quantity
    const prc = item.price
    return acc + qty * prc
  }, 0)
})
</script>

<template>
  <div>
    <table class="w-full text-sm">
      <thead class="bg-black text-white">
        <tr>
          <th class="p-3 text-left w-16">ID</th>
          <th class="p-3 text-left w-1/2">Description</th>
          <th class="p-3 w-38 relative flex items-center gap-1">
            {{ isServiceCompany ? 'Hours' : 'Quantity' }}
            <!-- Badge -->
            <button
              @click="tooltipOpen = !tooltipOpen"
              class="bg-gray-200 text-gray-700 rounded-full w-5 h-5 text-xs flex items-center justify-center relative z-10"
            >
              i
            </button>
            <!-- Tooltip -->
            <div
              v-if="tooltipOpen"
              class="absolute text-black top-full left-1/2 -translate-x-1/2 mt-2 w-64 bg-white border rounded shadow-lg p-4 text-sm z-20"
            >
              <p class="mb-2 font-medium">Are you a service based company?</p>
              <p class="mb-3 font-medium">Switch to hours/rates:</p>
              <div class="flex justify-center gap-3">
                <button
                  class="py-1 rounded text-gray-700"
                  @click="setServiceCompany(true);"
                >Yes</button>
                /
                <button
                  class="py-1 rounded text-gray-700"
                  @click="setServiceCompany(false);"
                >No</button>
              </div>
              <button
                class="absolute top-1 right-1 text-gray-400"
                @click="tooltipOpen = false"
              >✕</button>
            </div>

          </th>
          <th class="p-3 w-32">{{ isServiceCompany ? 'Rate' : 'Price' }}</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(item, index) in items" :key="item.id" class="border-b relative group">
          <!-- ID -->
          <td class="p-3 text-gray-400">
            <input type="text" class="input-clean w-20" :placeholder="String(index + 1).padStart(2, '0')" />
          </td>

          <!-- Description -->
          <td class="p-3">
            <input v-model="item.description" class="input-clean w-full outline-none" />
          </td>

          <!-- Quantity -->
          <td class="p-3 text-right">
            <input type="number" v-model.number="item.quantity" class="input-clean w-20" />
          </td>

          <!-- Price + remove button -->
          <td class="p-3 text-center relative">
            <input type="number" v-model.number="item.price" class="input-clean text-center w-28" />
            <button
              @click="emit('remove', item.id)"
              class="font-semibold remove-btn rounded-full border-2 w-6 h-6 "
            >
              ✕
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <button
      @click="emit('add')"
      class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 mt-4"
    >
      + Add Item
    </button>
  </div>
</template>