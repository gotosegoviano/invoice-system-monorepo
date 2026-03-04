<script setup lang="ts">
defineProps<{
  subtotal: number
  total: number
}>()
</script>

<template>
  <div class="text-right space-y-4 text-lg">
    <div class="flex justify-between">
      <span>Subtotal:</span>
      <span>{{ subtotal.toFixed(2) }}</span>
    </div>

    <div class="flex justify-between">
      <span>Tax:</span>
      <input type="number" :value="subtotal" class="input-clean flex-1 text-right"></input>
    </div>

    <div class="flex justify-between">
      <span>Discount:</span>
      <input type="number" :value="subtotal" class="input-clean flex-1 text-right"></input>
    </div>

    <div class="border-t pt-4 flex justify-between font-semibold text-2xl">
      <span>Total:</span>
      <span>{{ total.toFixed(2) }}</span>
    </div>
  </div>
</template>