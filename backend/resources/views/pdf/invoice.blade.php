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
            padding: 50px;
        }

        .clearfix {
            clear: both;
        }

        /* ===== HEADER ===== */

        .left {
            width: 60%;
            float: left;
        }

        .right {
            width: 40%;
            float: right;
            text-align: right;
        }

        .invoice-title {
            font-size: 58px;
            font-family: serif;
            display: inline-block;
            padding: 5px 12px;
            margin-bottom: 40px;
        }

        .block {
            margin-bottom: 25px;
        }

        .block-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .muted {
            color: #64748b;
        }

        .logo-box {
            border: 2px dashed #fb923c;
            padding: 25px;
            margin-bottom: 35px;
        }

        .meta p {
            margin: 4px 0;
        }

        /* ===== TABLE ===== */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
        }

        thead {
            background: #000;
            color: #fff;
        }

        th {
            padding: 12px;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right {
            text-align: right;
        }

        /* ===== BOTTOM ===== */

        .notes {
            width: 45%;
            float: left;
            border: 1px solid #e2e8f0;
            padding: 20px;
            min-height: 100px;
        }

        .totals {
            width: 40%;
            float: right;
        }

        .totals table td {
            padding: 6px 0;
        }

        .totals .label {
            color: #64748b;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
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
    {{-- ================= HEADER ================= --}}
    <div class="left">

        <div class="invoice-title">Invoice</div>

        {{-- COMPANY --}}
        <div class="block">
            <div class="block-title">{{ $invoice->company->name }}</div>
            <div class="muted">
                {{ $invoice->company->first_name }} {{ $invoice->company->last_name }}<br>

                @if($invoice->company->address)
                    {{ $invoice->company->address }}<br>
                @endif

                @if($invoice->company->city || $invoice->company->state || $invoice->company->zip)
                    {{ $invoice->company->city }}
                    {{ $invoice->company->state }}
                    {{ $invoice->company->zip }}<br>
                @endif

                @if($invoice->company->country)
                    {{ $invoice->company->country }}<br>
                @endif

                @if($invoice->company->phone)
                    {{ $invoice->company->phone }}<br>
                @endif

                @if($invoice->company->email)
                    {{ $invoice->company->email }}
                @endif
            </div>
        </div>

        {{-- CUSTOMER --}}
        <div class="block">
            <div class="block-title">{{ $invoice->customer->name }}</div>
            <div class="muted">
                {{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}<br>
                {{ $invoice->customer->address }}<br>
                {{ $invoice->customer->city }}, {{ $invoice->customer->state }} {{ $invoice->customer->zip }}<br>
                {{ $invoice->customer->country }}<br>
                {{ $invoice->customer->email }}
            </div>
        </div>

    </div>

    <div class="right">

        {{-- LOGO --}}
        @php
            $logoPath = null;
            if ($invoice->company->logo) {
                $path = storage_path('app/public/' . $invoice->company->logo_path);
                if (file_exists($path))
                    $logoPath = $path;
            }
        @endphp

        @if($logoPath)
            <img src="{{ $logoPath }}" width="150" style="margin-bottom:40px;">
        @else
            <div class="logo-box">Company Logo</div>
        @endif

        {{-- META --}}
        <div class="meta">
            <p><strong>Invoice No:</strong> {{ $invoice->uuid }}</p>
            <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
        </div>

    </div>

    <div class="clearfix"></div>

    {{-- ================= ITEMS ================= --}}
    <table>
        <thead>
            <tr>
                <th width="40">#</th>
                <th>Description</th>
                <th width="70">Qty</th>
                <th width="100" class="text-right">Price</th>
                <th width="100" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                @php $lineTotal = $item->quantity * $item->price; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">${{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ================= NOTES + TOTALS ================= --}}
    <div style="margin-top:60px;">

        <div class="notes">
            <strong>Notes</strong><br><br>
            {{ $invoice->comments ?? 'Any additional comments.' }}
        </div>

        <div class="totals">
            <table width="80%">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Tax</td>
                    <td class="text-right">${{ number_format($invoice->tax, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Discount</td>
                    <td class="text-right">
                        ${{ number_format($invoice->discount ?? 0, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="grand-total">Total</td>
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

</body>

</html>