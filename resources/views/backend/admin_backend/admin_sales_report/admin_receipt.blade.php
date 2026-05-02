<!DOCTYPE html>
<html>
<head>
    <title>Receipt Preview</title>

    <style>
        body {
            font-family: monospace;
            width: 300px;
            margin: auto;
        }

        .center {
            text-align: center;
            margin-top: 30px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        th, td {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .btn-secondary{
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #5a5c5b;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold; 
        }

        .btn-print {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #198754;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print:hover {
            background: #146c43;
        }

        @media print {
            .btn-print {
                display: none;
            }
            .btn-secondary{
                display:none;
            }
        }

    </style>
</head>

<body>

<div class="center">
    <h3>{{ $settings->company_name ?? 'My Shop' }}</h3>
    <p>{{ $settings->address ?? '' }}</p>
</div>

<div class="line"></div>

<p>
    Receipt: {{ $transaction->receipt_no }} <br>
    Date: {{\Carbon\Carbon::parse($transaction->created_at)->timezone($settings->timezone ?? 'Africa/Lagos')->format('d M Y h:i A')}} <br>

    Cashier: {{ $cashier->name ?? 'N/A' }}
</p>

<div class="line"></div>

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th class="right">Qty</th>
            <th class="right">Price</th>
            <th class="right">Amt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>
                {{ $item->product_name }} <br>
                <small>{{ $item->category }}</small>
            </td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">₦{{ number_format($item->price, 2) }}</td>
            <td class="right">₦{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="line"></div>

<h4>Total: ₦{{ number_format($transaction->total_amount, 2) }}</h4>
<p>Payment: {{ ucfirst($transaction->payment_method) }}</p>

<div class="line"></div>

<p style="text-align:center;">Thanks for your business!</p>

<!-- 🔥 PRINT BUTTON -->
<button class="btn-print" onclick="printReceipt()">🖨 Print Receipt</button>

        <!-- ACTIONS -->
        <div class="d-flex justify-content-between mb-3">
            <a href="/admin/sales-items-page/{{ $transaction->id }}"><button class="btn btn-secondary">← Back</button></a>
        </div>


<script>

// PRINT FUNCTION
function printReceipt(){
    window.print();
}

// AFTER PRINT → REDIRECT BACK
window.onafterprint = function() {
    window.location.href = "/admin/sales-items-page/{{ $transaction->id }}";
};

</script>
</body>
</html>