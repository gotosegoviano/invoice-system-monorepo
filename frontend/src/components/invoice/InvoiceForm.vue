<script setup lang="ts">
import { ref, computed } from "vue"
import { useInvoice } from '@/composables/useInvoice'
import { useFileUpload } from '@/composables/useFileUpload'
import { useInvoiceValidation } from '@/composables/useInvoiceValidation'
import { useInvoiceApi } from '@/composables/useInvoiceApi'
import ItemsTable from './ItemsTable.vue'
import TotalsPanel from './TotalsPanel.vue'
import Datepicker from "vue3-datepicker";

const { invoice, addItem, removeItem, subtotal } = useInvoice()
const { onFileChange, onDrop, onClickSelect } = useFileUpload()

const invoiceDate = ref(new Date());
const dueDate = ref(new Date());
const taxAmount = ref(0)
const discountAmount = ref(0)
const taxType = ref<'$' | '%'>('%')
const discountType = ref<'$' | '%'>('%')
const invoiceNumber = ref(0)
const today = new Date()
const minDate = today

// handlers para logo upload
function handleFileSelect(e: Event) {
  onFileChange(e, (dataUrl) => {
    invoice.value.company.logo_path = dataUrl;
  });
}

function handleDrop(e: DragEvent) {
  onDrop(e, (dataUrl) => {
    invoice.value.company.logo_path = dataUrl;
  });
}

const total = computed(() => {
  let value = subtotal.value
  const type = invoice.value.type 

  const calcTax = (base: number) => {
    return taxType.value === '%' ? base * (taxAmount.value / 100) : taxAmount.value
  }

  const calcDiscount = (base: number) => {
    return discountType.value === '%' ? base * (discountAmount.value / 100) : discountAmount.value
  }

  if (type === 'service') {
    if (discountAmount.value) {
      value -= calcDiscount(value)
    }
    if (taxAmount.value) {
      value += calcTax(value)
    }
  } else if (type === 'product') {
    if (taxAmount.value) {
      value += calcTax(value)
    }
    if (discountAmount.value) {
      value -= calcDiscount(value)
    }
  }

  return value
})

const { createInvoice, loading, errorMessage, pdfLink } = useInvoiceApi(
  invoice,
  invoiceNumber,
  invoiceDate,
  dueDate,
  taxAmount,
  discountAmount,
  total, 
  taxType,
  discountType
);
</script>

<template>
  <div class="bg-[#f5f6f7] min-h-screen py-12">
    <div class="max-w-6xl mx-auto bg-white p-10 shadow-sm">

      <!-- HEADER -->
      <div class="grid grid-cols-2 gap-10">

        <!-- LEFT -->
        <div>
          <h1 class="text-6xl font-serif inline-block px-3">
            Invoice
          </h1>

          <div class="mt-10 space-y-8 text-sm text-gray-600">

            <!-- COMPANY -->
            <div>
              <div class="font-semibold text-gray-800">
                <input class="input-clean" placeholder="Company Name*" v-model="invoice.company.name" />
              </div>
              <div class="grid grid-cols-2">
                <input class="input-clean" placeholder="First Name*" v-model="invoice.company.first_name" />
                <input class="input-clean" placeholder="Last Name*" v-model="invoice.company.last_name" />
                <input class="input-clean" placeholder="Web Page URL*" v-model="invoice.company.web_page_url" />
                <input class="input-clean" placeholder="Address" v-model="invoice.company.address" />
                <input class="input-clean" placeholder="City" v-model="invoice.company.city" />
                <input class="input-clean" placeholder="State" v-model="invoice.company.state" />
                <input class="input-clean" placeholder="ZIP" v-model="invoice.company.zip" />
                <input class="input-clean" placeholder="Country" v-model="invoice.company.country" />
                <input class="input-clean" placeholder="Phone*" v-model="invoice.company.phone" />
                <input class="input-clean" placeholder="Email*" v-model="invoice.company.email" />
              </div>
            </div>

            <!-- CUSTOMER -->
            <div>
              <div class="font-semibold text-gray-800">
                <input class="input-clean" placeholder="Client's Company*" v-model="invoice.customer.name" />
              </div>
              <div class="grid grid-cols-2">
                <input class="input-clean" placeholder="First Name" v-model="invoice.customer.first_name" />
                <input class="input-clean" placeholder="Last Name" v-model="invoice.customer.last_name" />
                <input class="input-clean" placeholder="Address" v-model="invoice.customer.address" />
                <input class="input-clean" placeholder="City" v-model="invoice.customer.city" />
                <input class="input-clean" placeholder="State" v-model="invoice.customer.state" />
                <input class="input-clean" placeholder="ZIP" v-model="invoice.customer.zip" />
                <input class="input-clean" placeholder="Country" v-model="invoice.customer.country" />
                <input class="input-clean" placeholder="Email" v-model="invoice.customer.email" />
              </div>
            </div>

          </div>
        </div>

        <!-- RIGHT DRAG & DROP LOGO & DATES-->
        <div class="flex flex-col items-end h-full justify-between">

          <!-- Drag & Drop Logo -->
          <div
            class="border-2 border-dashed border-orange-400 p-10 text-center w-72 text-gray-500 cursor-pointer"
            @dragover.prevent
            @dragenter.prevent
            @drop.prevent="handleDrop"
            @click="onClickSelect"
          >
            <div v-if="invoice.company.logo_path">
              <img :src="invoice.company.logo_path" alt="Logo" class="max-h-20 mx-auto" />
              <p class="text-sm text-gray-400 mt-2">Click or drag a new file to replace</p>
            </div>
            <div v-else>
              <p>Drag & Drop Logo</p>
              <p class="text-sm text-gray-400">or click to select</p>
            </div>
            <input
              type="file"
              ref="fileInput"
              class="hidden"
              @change="handleFileSelect"
              accept="image/*"
            />
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="mt-4 w-full max-w-md bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error: </strong>
            <span class="block sm:inline">{{ errorMessage }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="errorMessage = ''">
              <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"><title>Close</title><path
                  d="M14.348 5.652a1 1 0 1 0-1.414-1.414L10 7.172 7.066 4.238a1 1 0 1 0-1.414 1.414L8.828 10l-3.176 3.176a1 1 0 0 0 1.414 1.414L10 12.828l2.934 2.934a1 1 0 0 0 1.414-1.414L11.172 10l3.176-3.176z" /></svg>
            </span>
          </div>

          <!-- Dates -->
          <div class="text-gray-500">
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Invoice No:</span>
              <input type="text" v-model="invoiceNumber" class="input-clean flex-1 text-right pr-6" placeholder="####" />
            </div>
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Invoice Date:</span>
              <Datepicker 
                v-model="invoiceDate" 
                input-class="input-clean flex-1 datepicker-input-right"
                :lowerLimit="minDate"
                :upperLimit="dueDate"
                inputFormat="MM/dd/yyyy"
              />
            </div>
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Due Date:</span>
              <Datepicker 
                v-model="dueDate" 
                input-class="input-clean flex-1 datepicker-input-right" 
                :lowerLimit="minDate"
                inputFormat="MM/dd/yyyy"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- TABLE -->
      <div class="mt-16">
        <ItemsTable
          :items="invoice.items"
          @add="addItem"
          @remove="removeItem"
        />
      </div>

      <!-- NOTES + TOTALS -->
      <div class="grid grid-cols-2 gap-6 mt-16">
        <div class="border rounded-xl p-4 text-gray-500">
          <p class="font-medium text-gray-700 mb-2 text-center">Notes</p>
          <textarea
            v-model="invoice.notes"
            placeholder="Any additional comments"
            class="input-clean resize-none text-center"
            rows="4"
          ></textarea>
        </div>

        <TotalsPanel
          :subtotal="subtotal"
          :total="total"
          v-model:taxAmount="taxAmount"
          v-model:discountAmount="discountAmount"
          @update:taxType="val => taxType = val"
          @update:discountType="val => discountType = val"
        />

      </div>

    </div>
  </div>

  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2">
    <button
      @click="createInvoice"
      :disabled="loading"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
    >
      {{ loading ? 'Creating...' : 'Create PDF Invoice' }}
    </button>

    <a
      v-if="pdfLink"
      :href="pdfLink"
      target="_blank"
      class="text-white-600 underline mt-1 hover:text-green-700 bg-green-100 px-3 py-1 rounded"
    >
      Open PDF
    </a>
  </div>
</template>

<style scoped>
.input {
  @apply border border-gray-300 rounded px-3 py-2 w-full;
}
</style>