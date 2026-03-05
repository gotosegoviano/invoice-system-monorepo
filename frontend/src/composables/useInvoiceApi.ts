import { ref, type Ref } from "vue";
import { useInvoiceValidation } from "./useInvoiceValidation";
import type { InvoiceState } from "@/types/invoice";

export function useInvoiceApi(
  invoice: Ref<InvoiceState>,
  invoiceNumber: Ref<number>,
  invoiceDate: Ref<Date>,
  dueDate: Ref<Date>,
  taxAmount: Ref<number>,
  discountAmount: Ref<number>,
  total: Ref<number>,
  taxType: Ref<"$" | "%">,
  discountType: Ref<"$" | "%">,
) {
  const loading = ref(false);
  const errorMessage = ref("");
  const pdfLink = ref("");

  const { canCreateInvoice, errorMessage: validationError } = useInvoiceValidation(
    invoice,
    invoiceNumber,
    invoiceDate,
    dueDate,
  );

  // Convert data URL to Blob for file upload
  function dataURLtoBlob(dataurl: string): Blob {
    const arr = dataurl.split(",");
    const mimeMatch = arr[0]?.match(/:(.*?);/);
    const mime = mimeMatch ? mimeMatch[1] : "application/octet-stream";
    const bstr = atob(arr[1] ?? "");
    const n = bstr.length;
    const u8arr = new Uint8Array(n);
    for (let i = 0; i < n; i++) u8arr[i] = bstr.charCodeAt(i);
    return new Blob([u8arr], { type: mime });
  }

  async function createInvoice() {
    if (!canCreateInvoice.value) {
      errorMessage.value = validationError.value;
      return;
    }

    loading.value = true;
    errorMessage.value = "";

    try {
      const formData = new FormData();

      // --- Company fields ---
      Object.entries(invoice.value.company).forEach(([key, value]) => {
        if (key === "logo_path") return;
        formData.append(`company[${key}]`, (value ?? "") as string);
      });

      // --- Add logo as Blob if exists ---
      if (invoice.value.company.logo_path) {
        formData.append("company[logo]", dataURLtoBlob(invoice.value.company.logo_path), "logo.png");
      }

      // --- Customer fields ---
      Object.entries(invoice.value.customer).forEach(([key, value]) => {
        formData.append(`customer[${key}]`, (value ?? "") as string);
      });

      // --- Items ---
      invoice.value.items.forEach((item, i) => {
        Object.entries(item).forEach(([key, value]) => {
          formData.append(`items[${i}][${key}]`, String(value ?? ""));
        });
      });

      // --- Dates ---
      formData.append("invoice_date", invoiceDate.value?.toISOString().split("T")[0] ?? "");
      formData.append("due_date", dueDate.value?.toISOString().split("T")[0] ?? "");
      formData.append("invoice_number", (invoiceNumber.value ?? 0).toString());

      // Tax, discount, total
      formData.append("tax_total", String(taxAmount.value));
      formData.append("discount_total", String(discountAmount.value));
      formData.append("total", String(total.value));
      formData.append("type", invoice.value.type);
      formData.append("comments", invoice.value.notes ?? "");
      formData.append("tax_type", taxType.value ?? "");
      formData.append("discount_type", discountType.value ?? "");

      // --- Fetch al backend ---
      const response = await fetch(`${import.meta.env.VITE_API_URL}/api/invoices`, {
        method: "POST",
        headers: {
          Accept: "application/json",
        },
        body: formData,
      });

      const data = await response.json();

      if (response.ok) {
        pdfLink.value = `${data.pdf_path}`;
      } else {
        if (data.errors) {
          const firstError = Object.values(data.errors)[0] as string[];
          errorMessage.value = firstError[0] ?? "Unknown error creating invoice";
        } else if (data.message) {
          errorMessage.value = data.message;
        } else {
          errorMessage.value = "Error creating invoice";
        }
      }
    } catch (error) {
      console.error(error);
      errorMessage.value = "Error connecting to server";
    } finally {
      loading.value = false;
      localStorage.removeItem("invoice");
    }
  }

  return { createInvoice, loading, errorMessage, pdfLink };
}
