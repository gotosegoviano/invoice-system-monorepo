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
            margin: 0;
            padding: 40px;
            background: #ffffff;
        }

        .container {
            width: 100%;
        }

        /* ===== HEADER ===== */

        .header {
            width: 100%;
            margin-bottom: 50px;
        }

        .left-column {
            width: 60%;
            float: left;
        }

        .right-column {
            width: 40%;
            float: right;
            text-align: right;
        }

        .invoice-title {
            font-size: 48px;
            font-weight: bold;
            font-family: serif;
            display: inline-block;
            padding: 4px 10px;
            background: #dbeafe;
            margin-bottom: 30px;
        }

        .company-block,
        .client-block {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #0f172a;
        }

        .muted {
            color: #64748b;
        }

        .meta p {
            margin: 4px 0;
        }

        .logo-box {
            border: 2px dashed #f97316;
            padding: 25px;
            text-align: center;
            margin-bottom: 40px;
        }

        .clearfix {
            clear: both;
        }

        /* ===== TABLE ===== */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        thead {
            background: #000000;
            color: #ffffff;
        }

        th {
            padding: 12px 10px;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right {
            text-align: right;
        }

        /* ===== NOTES + TOTALS ===== */

        .bottom-section {
            margin-top: 60px;
            width: 100%;
        }

        .notes {
            width: 55%;
            float: left;
            border: 1px solid #e2e8f0;
            padding: 25px;
            min-height: 100px;
        }

        .totals {
            width: 40%;
            float: right;
        }

        .totals table td {
            padding: 8px 0;
        }

        .totals .label {
            color: #64748b;
        }

        .totals .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }

        .footer {
            margin-top: 100px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="container">

        {{-- ================= HEADER ================= --}}
        <div class="header">

            <div class="left-column">
                <div class="invoice-title">Invoice</div>

                <div class="company-block">
                    <div class="section-title">{{ $invoice->company->name }}</div>
                    <div class="muted">
                        {{ $invoice->company->email }}
                    </div>
                </div>

                <div class="client-block">
                    <div class="section-title">
                    {{ $invoice->customer->name }}
                    </div>
                    <div class="muted">
                        {{ $invoice->customer->email }}
                    </div>
                </div>
            </div>

            <div class="right-column">

                {{-- Logo --}}
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
                    <img src="{{ $logoPath }}" width="140" style="margin-bottom:40px;">
                @else
                    <div class="logo-box">
                        Company Logo
                    </div>
                @endif

                {{-- Meta --}}
                <div class="meta">
                    <p><strong>Invoice No:</strong> {{ $invoice->uuid }}</p>
                    <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>
                    <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
                </div>

            </div>

            <div class="clearfix"></div>
        </div>

        {{-- ================= ITEMS TABLE ================= --}}
        <table>
            <thead>
                <tr>
                    <th width="40">ID</th>
                    <th>Description</th>
                    <th width="80">Qty</th>
                    <th width="100" class="text-right">Price</th>
                    <th width="100" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    @php
                        $lineTotal = $item->quantity * $item->price;
                    @endphp
                    <tr>
                        <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">${{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ================= NOTES + TOTALS ================= --}}
        <div class="bottom-section">

            <div class="notes">
                <strong>Notes:</strong><br><br>
                {{ $invoice->notes ?? 'Any additional comments.' }}
            </div>

            <div class="totals">
                <table width="100%">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax</td>
                        <td class="text-right">${{ number_format($invoice->tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label grand-total">Total</td>
                        <td class="text-right grand-total">
                            ${{ number_format($invoice->total, 2) }}
                        </td>
                    </tr>
                </table>
            </div>

            <div class="clearfix"></div>
        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="footer">
            Thank you for your business.
        </div>

    </div>

</body>

</html>