<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  subtotal: number
  total: number
}>()

const taxType = ref<'$' | '%'>('%')
const discountType = ref<'$' | '%'>('%')
</script>

<template>
  <div class="text-right space-y-2">
    <!-- Subtotal -->
    <div class="flex justify-between">
      <span>Subtotal:</span>
      <span class="pr-6 font-semibold">{{ subtotal.toFixed(2) }}</span>
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

        <input type="number" placeholder="0" class="input-clean w-full text-right pl-6 pr-6 font-semibold"/>

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

        <input type="number" placeholder="0" class="input-clean w-full text-right pl-6 pr-6 font-semibold"/>

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
      <span class="pr-6">{{ total.toFixed(2) }}</span>
    </div>
  </div>
</template>