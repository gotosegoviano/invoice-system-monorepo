<script setup lang="ts">
import { ref, computed } from "vue"
import { useInvoice } from '@/composables/useInvoice'
import ItemsTable from './ItemsTable.vue'
import TotalsPanel from './TotalsPanel.vue'
import Datepicker from "vue3-datepicker";

const invoiceDate = ref(new Date());
const dueDate = ref(new Date());
const pdfLink = ref('')
const loading = ref(false)
const errorMessage = ref('')
const taxAmount = ref(0)
const discountAmount = ref(0)
const taxType = ref<'$' | '%'>('%')
const discountType = ref<'$' | '%'>('%')
const invoiceNumber = ref(0)

const { invoice, addItem, removeItem, subtotal } = useInvoice()

// Ref for the hidden file input
const fileInput = ref<HTMLInputElement>()

// Drag & drop + file select handlers
function onFileChange(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) loadFile(file)
}

function onClickSelect() {
  fileInput.value?.click()
}

function onDrop(event: DragEvent) {
  const file = event.dataTransfer?.files?.[0]
  if (file) loadFile(file)
}

function loadFile(file: File) {
  const reader = new FileReader()
  reader.onload = () => {
    invoice.value.company.logo_path = reader.result as string
  }
  reader.readAsDataURL(file)
}

// Convert DataURL to Blob
function dataURLtoBlob(dataurl: string) {
  const arr = dataurl.split(',')
  if (arr.length < 2) {
    throw new Error('Invalid data URL')
  }

  const mimeMatch = arr[0]?.match(/:(.*?);/)
  const mime = mimeMatch ? mimeMatch[1] : 'application/octet-stream'

  const bstr = atob(arr[1] ?? '')
  const n = bstr.length
  const u8arr = new Uint8Array(n)

  for (let i = 0; i < n; i++) {
    u8arr[i] = bstr.charCodeAt(i)
  }

  return new Blob([u8arr], { type: mime })
}

const total = computed(() => {
  let value = subtotal.value

  if (taxAmount.value) {
    value += taxType.value === '%' ? subtotal.value * (taxAmount.value / 100) : taxAmount.value
  }

  if (discountAmount.value) {
    value -= discountType.value === '%' ? subtotal.value * (discountAmount.value / 100) : discountAmount.value
  }

  return value
})

// Phone Number Validation (10 digits, no formatting)
function isValidPhone(phone: string) {
  const regex = /^\d{10}$/
  return regex.test(phone)
}

// Validate form before creating invoice
const canCreateInvoice = computed(() => {
  if (!invoice.value.company.name) { errorMessage.value = 'Company Name is required.'; return false }
  if (!invoice.value.company.email) { errorMessage.value = 'Company Email is required.'; return false }
  if (!invoice.value.company.phone) { errorMessage.value = 'Company Phone is required.'; return false }
  if (!isValidPhone(invoice.value.company.phone)) { errorMessage.value = 'Please enter a valid Company Phone (10 digits).'; return false }

  if (!invoice.value.customer.name) { errorMessage.value = "Client's Company Name is required."; return false }

  if (invoice.value.items.length === 0) { errorMessage.value = 'At least one item is required.'; return false }
  const allItemsValid = invoice.value.items.every(item => item.description && item.quantity > 0 && item.price > 0)
  if (!allItemsValid) { errorMessage.value = 'All items must have a description, quantity, and price greater than 0.'; return false }

  errorMessage.value = ''
  return true
})

// Create Invoice Handler
async function createInvoice() {
  if (!canCreateInvoice.value) return

  loading.value = true
  errorMessage.value = ''

  try {
    const formData = new FormData()

    // --- Company fields ---
    Object.entries(invoice.value.company).forEach(([key, value]) => {
      if (key === 'logo_path') return
      formData.append(`company[${key}]`, (value ?? '') as string)
    })

    // --- Add logo as Blob if exists ---
    if (invoice.value.company.logo_path) {
      formData.append(
        'company[logo]',
        dataURLtoBlob(invoice.value.company.logo_path),
        'logo.png'
      )
    }

    // --- Customer fields ---
    Object.entries(invoice.value.customer).forEach(([key, value]) => {
      formData.append(`customer[${key}]`, (value ?? '') as string)
    })

    // --- Items ---
    invoice.value.items.forEach((item, i) => {
      Object.entries(item).forEach(([key, value]) => {
        formData.append(`items[${i}][${key}]`, String(value ?? ''))
      })
    })

    // --- Dates ---
    formData.append('invoice_date', (invoiceDate.value?.toISOString().split('T')[0] ?? ''))
    formData.append('due_date', (dueDate.value?.toISOString().split('T')[0] ?? ''))
    formData.append('invoice_number', (invoiceNumber.value ?? 0).toString())
    
    // Tax, discount, total
    formData.append('tax_total', String(taxAmount.value))
    formData.append('discount_total', String(discountAmount.value))
    formData.append('total', String(total.value))
    formData.append('type', invoice.value.type)
    formData.append('comments', invoice.value.notes ?? '')

    // --- Fetch al backend ---
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/invoices`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
      },
      body: formData,
    })

    const data = await response.json()

    if (response.ok) {
      pdfLink.value = `${data.pdf_path}`
    } else {
      if (data.errors) {
        const firstError = Object.values(data.errors)[0] as string[]
        errorMessage.value = firstError[0] ?? 'Unknown error creating invoice'
      } else if (data.message) {
        errorMessage.value = data.message
      } else {
        errorMessage.value = 'Error creating invoice'
      }
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = 'Error connecting to server'
  } finally {
    loading.value = false
  }
}
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
            @drop.prevent="onDrop"
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
              @change="onFileChange"
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
              />
            </div>
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Due Date:</span>
              <Datepicker 
                v-model="dueDate" 
                input-class="input-clean flex-1 datepicker-input-right" 
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
      class="text-blue-600 underline mt-1"
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