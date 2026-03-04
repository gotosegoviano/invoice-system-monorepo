export interface InvoiceItem {
  id: string;
  itemId: number;
  description: string;
  quantity: number;
  price: number;
}

export interface InvoiceState {
  company: Company;
  customer: Customer;
  items: InvoiceItem[];
  invoiceId: number;
  issueDate: string;
  dueDate: string;
  notes: string;
}

export interface InvoiceTotals {
  subtotal: number;
  total: number;
}

export interface Company {
  name: string;
  first_name: string;
  last_name: string;
  web_page_url: string;
  address?: string;
  city?: string;
  state?: string;
  zip?: string;
  country?: string;
  phone: string;
  email: string;
  logo_path?: string;
}

export interface Customer {
  company_name?: string;
  first_name?: string;
  last_name?: string;
  address?: string;
  city?: string;
  state?: string;
  zip?: string;
  country?: string;
  email?: string;
}
