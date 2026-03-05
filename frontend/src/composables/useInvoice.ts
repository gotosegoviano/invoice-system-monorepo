import { ref, computed, watchEffect } from "vue";
import type { InvoiceItem, InvoiceState } from "@/types/invoice";

export function useInvoice() {
  const invoice = ref<InvoiceState>({
    company: {
      name: "",
      first_name: "",
      last_name: "",
      web_page_url: "",
      address: "",
      city: "",
      state: "",
      zip: "",
      country: "",
      phone: "",
      email: "",
      logo_path: "",
    },
    customer: {
      name: "",
      first_name: "",
      last_name: "",
      address: "",
      city: "",
      state: "",
      zip: "",
      country: "",
      email: "",
    },
    items: [],
    invoiceNumber: 0,
    invoiceDate: new Date().toISOString().slice(0, 10),
    dueDate: "",
    notes: "",
    type: "product",
  });

  // ---------- Get invoice from localStorage ----------
  const savedInvoice = localStorage.getItem("invoice");
  if (savedInvoice) {
    const parsed = JSON.parse(savedInvoice);
    invoice.value = parsed;
  }

  // Save to localStorage whenever invoice changes
  watchEffect(() => {
    localStorage.setItem("invoice", JSON.stringify(invoice.value));
  });

  function addItem() {
    invoice.value.items.push({
      id: crypto.randomUUID(),
      itemId: invoice.value.items.length + 1,
      description: "",
      quantity: 1,
      price: 0.0,
    });
  }

  function removeItem(id: string) {
    invoice.value.items = invoice.value.items.filter((i) => i.id !== id);
  }

  const subtotal = computed(() => invoice.value.items.reduce((acc, item) => acc + item.quantity * item.price, 0));

  const total = computed(() => subtotal.value);

  return {
    invoice,
    addItem,
    removeItem,
    subtotal,
    total,
  };
}
