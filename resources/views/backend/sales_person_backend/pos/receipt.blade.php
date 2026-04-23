<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>

    <style>
        body {
            font-family: monospace;
            width: 300px;
            margin: auto;
        }

        .center {
            text-align: center;
            margin-top:50px;
        }

        .thanks {
            text-align: center;
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

    </style>
</head>

<body>

    {{-- 🔥 COMPANY INFO --}}
    <div class="center">
        <h3>{{ $settings->company_name ?? 'My Shop' }}</h3>
        <p>{{ $settings->address ?? '' }}</p>
    </div>

    <div class="line"></div>

    {{-- 🔥 RECEIPT INFO --}}
    <p>
        Receipt: {{ $transaction->receipt_no }} <br>
        Date: {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y h:i A') }} <br>
        Cashier: {{ $cashier->name ?? 'N/A' }}
    </p>


    <div class="line"></div>

    {{-- 🔥 ITEMS --}}
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
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

    {{-- 🔥 TOTAL --}}
    <h4>Total: ₦{{ number_format($transaction->total_amount, 2) }}</h4>
    <p>Payment: {{ ucfirst($transaction->payment_method) }}</p>

    <div class="line"></div>

    <div class="thanks">
        <p>Thanks for shopping with us!</p>
    </div>



    <script>
    window.onload = function() {
        window.print();
    };

    window.onafterprint = function() {
        window.close(); // closes receipt tab
    };
</script>
</body>
</html>