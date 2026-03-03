<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->uuid }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #334155;
        }

        .container {
            width: 100%;
        }

        .top-section {
            width: 100%;
            margin-bottom: 40px;
        }

        .logo {
            float: left;
        }

        .invoice-title {
            float: right;
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 2px;
            color: #0f172a;
        }

        .invoice-title p {
            margin: 2px 0;
            font-size: 12px;
            color: #64748b;
        }

        .clearfix {
            clear: both;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .info-box strong {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background: #f1f5f9;
        }

        th {
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            margin-top: 30px;
            width: 40%;
            float: right;
        }

        .totals table {
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 0;
        }

        .totals .label {
            color: #64748b;
        }

        .totals .value {
            text-align: right;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
        }

        .footer {
            margin-top: 80px;
            font-size: 10px;
            text-align: center;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Top Section -->
        <div class="top-section">
            <div class="logo">
                @php
                    $logoPath = null;

                    if ($invoice->company->logo) {
                        $storagePath = storage_path('app/public/' . $invoice->company->logo);

                        if (file_exists($storagePath)) {
                            $logoPath = $storagePath;
                        }
                    }
                @endphp

                @if($logoPath)
                    <img src="{{ $logoPath }}" width="120">
                @else
                    <div style="font-size:18px; font-weight:bold; color:#0f172a;">
                        {{ $invoice->company->name }}
                    </div>
                @endif
            </div>

            <div class="invoice-title">
                <h1>INVOICE</h1>
                <p>Invoice #: {{ $invoice->uuid }}</p>
                <p>Issue Date: {{ $invoice->created_at->format('Y-m-d') }}</p>
                <p>Due Date: {{ $invoice->due_date }}</p>
            </div>

            <div class="clearfix"></div>
        </div>

        <!-- Company & Customer -->
        <div class="info-section">
            <div class="info-box">
                <strong>From</strong>
                {{ $invoice->company->name }}<br>
                {{ $invoice->company->email }}
            </div>

            <div class="info-box">
                <strong>Bill To</strong>
                {{ $invoice->customer->name }}<br>
                {{ $invoice->customer->email }}
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th width="60">Qty</th>
                    <th width="90" class="text-right">Unit Price</th>
                    <th width="60" class="text-right">Tax</th>
                    <th width="100" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    @php
                        $lineTotal = ($item->quantity * $item->price) * (1 + $item->tax_rate / 100);
                    @endphp
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ $item->tax_rate }}%</td>
                        <td class="text-right">${{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table width="100%">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">${{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">${{ number_format($invoice->tax, 2) }}</td>
                </tr>
                <tr>
                    <td class="label grand-total">Total</td>
                    <td class="value grand-total">
                        ${{ number_format($invoice->total, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="clearfix"></div>

        <!-- Footer -->
        <div class="footer">
            Thank you for your business.
        </div>

    </div>

</body>

</html>