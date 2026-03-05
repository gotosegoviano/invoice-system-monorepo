import { ref, computed, type Ref } from "vue";
import type { InvoiceState } from "@/types/invoice";

export function useInvoiceValidation(
  invoice: Ref<InvoiceState>,
  invoiceNumber: Ref<number>,
  invoiceDate: Ref<Date>,
  dueDate: Ref<Date>,
) {
  const errorMessage = ref<string>("");

  const isValidPhone = (phone: string) => /^\d{10}$/.test(phone);
  const isValidEmail = (email: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

  const canCreateInvoice = computed(() => {
    errorMessage.value = "";
    //Company fields
    if (!invoice.value.company.name?.trim()) {
      errorMessage.value = "Company Name is required";
      return false;
    }
    if (!invoice.value.company.first_name?.trim()) {
      errorMessage.value = "Company First Name is required";
      return false;
    }
    if (!invoice.value.company.last_name?.trim()) {
      errorMessage.value = "Company Last Name is required";
      return false;
    }
    if (!invoice.value.company.web_page_url?.trim()) {
      errorMessage.value = "Company Web Page URL is required";
      return false;
    }
    if (!invoice.value.company.phone?.trim()) {
      errorMessage.value = "Company Phone is required";
      return false;
    }
    if (!isValidPhone(invoice.value.company.phone)) {
      errorMessage.value = "Company Phone must be 10 digits";
      return false;
    }
    if (!invoice.value.company.email?.trim()) {
      errorMessage.value = "Company Email is required";
      return false;
    }
    if (!isValidEmail(invoice.value.company.email)) {
      errorMessage.value = "Company Email is invalid";
      return false;
    }

    // Customer fields
    if (!invoice.value.customer.name?.trim()) {
      errorMessage.value = "Customer Company Name is required";
      return false;
    }

    // Invoice general
    if (!invoiceNumber.value) {
      errorMessage.value = "Invoice Number is required";
      return false;
    }
    if (!invoiceDate.value) {
      errorMessage.value = "Invoice Date is required";
      return false;
    }
    if (!dueDate.value) {
      errorMessage.value = "Due Date is required";
      return false;
    }

    // Items
    if (!invoice.value.items || invoice.value.items.length === 0) {
      errorMessage.value = "At least one item is required in the invoice";
      return false;
    }

    return true;
  });

  return { canCreateInvoice, errorMessage };
}
