<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  subtotal: number
  taxAmount: number
  discountAmount: number
  total: number
}>()

const emit = defineEmits<{
  (e: 'update:taxAmount', value: number): void
  (e: 'update:discountAmount', value: number): void
}>()

// Local reactive refs
const localTax = ref(props.taxAmount)
const localDiscount = ref(props.discountAmount)
const taxType = ref<'$' | '%'>('%')
const discountType = ref<'$' | '%'>('%')

// Sync props changes from parent
watch(() => props.taxAmount, val => localTax.value = val)
watch(() => props.discountAmount, val => localDiscount.value = val)
</script>

<template>
  <div class="text-right space-y-2">
    <!-- Subtotal -->
    <div class="flex justify-between">
      <span>Subtotal:</span>
      <span class="pr-6 font-semibold">{{ props.subtotal.toFixed(2) }}</span>
    </div>

    <!-- Tax -->
    <div class="flex justify-between items-center gap-2">
      <span>Tax:</span>
      <div class="relative w-28 flex items-center">
        <button 
          v-if="taxType === '$'" 
          @click="taxType = '%'" 
          class="absolute left-0 pl-2 text-gray-700 cursor-pointer font-semibold"
        >
          $
        </button>

        <input 
          type="number" 
          v-model.number="localTax" 
          @input="emit('update:taxAmount', localTax)" 
          placeholder="0" 
          class="input-clean w-full text-right pl-6 pr-6 font-semibold"
        />

        <button
          v-if="taxType === '%'" 
          @click="taxType = '$'" 
          class="absolute right-0 text-gray-700 cursor-pointer font-semibold"
        >
          %
        </button>
      </div>
    </div>

    <!-- Discount -->
    <div class="flex justify-between items-center gap-2">
      <span>Discount:</span>
      <div class="relative w-28 flex items-center">
        <button
          v-if="discountType === '$'"
          @click="discountType = '%'"
          class="absolute left-0 pl-2 text-gray-700 cursor-pointer font-semibold"
        >$</button>

        <input 
          type="number" 
          v-model.number="localDiscount" 
          @input="emit('update:discountAmount', localDiscount)" 
          placeholder="0" 
          class="input-clean w-full text-right pl-6 pr-6 font-semibold"
        />

        <button
          v-if="discountType === '%'"
          @click="discountType = '$'"
          class="absolute right-0 text-gray-700 cursor-pointer font-semibold"
        >%</button>
      </div>
    </div>

    <!-- Total -->
    <div class="border-t pt-4 flex justify-between font-semibold">
      <span>Total:</span>
      <span class="pr-6">{{ props.total.toFixed(2) }}</span>
    </div>
  </div>
</template>

<style scoped>
.input-clean {
  @apply border border-gray-300 rounded px-3 py-2 w-full;
}
</style>