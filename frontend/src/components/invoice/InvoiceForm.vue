<script setup lang="ts">
import { ref } from "vue"
import { useInvoice } from '@/composables/useInvoice'
import ItemsTable from './ItemsTable.vue'
import TotalsPanel from './TotalsPanel.vue'
import Datepicker from "vue3-datepicker";

const issueDate = ref(new Date());
const dueDate = ref(new Date());

const { invoice, addItem, removeItem, subtotal, total } = useInvoice()

// Ref for the hidden file input
const fileInput = ref<HTMLInputElement>()

// Drag and drop + file select handlers
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
                <input class="input-clean" placeholder="Web Page URL" v-model="invoice.company.web_page_url" />
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
                <input class="input-clean" placeholder="Client's Company*" v-model="invoice.customer.company_name" />
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

          <!-- Dates -->
          <div class="text-gray-500">
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Invoice No:</span>
              <input type="text" class="input-clean flex-1 text-right" v-model="invoice.invoiceId" />
            </div>
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Invoice Date:</span>
              <Datepicker v-model="issueDate" input-class="input-clean flex-1 datepicker-input-right" />
             <!--  <input type="date" class="input-clean flex-1" v-model="invoice.issueDate" /> -->
            </div>
            <div class="flex items-center gap-1">
              <span class="w-28 font-medium">Due Date:</span>
              <!-- <input type="date" class="input-clean flex-1" v-model="invoice.dueDate" /> -->
              <Datepicker v-model="dueDate" input-class="input-clean flex-1 datepicker-input-right" />
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
      <div class="grid grid-cols-2 gap-10 mt-16">

        <div class="border rounded-xl p-8 text-gray-500">
          <p class="font-medium text-gray-700 mb-2">Notes</p>
          <textarea
            v-model="invoice.notes"
            class="w-full border rounded p-3"
            rows="4"
          />
        </div>

        <TotalsPanel
          :subtotal="subtotal"
          :total="total"
        />

      </div>

    </div>
  </div>
</template>

<style scoped>
.input {
  @apply border border-gray-300 rounded px-3 py-2 w-full;
}
</style>