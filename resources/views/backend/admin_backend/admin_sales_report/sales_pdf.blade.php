<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #2c2c2c;
        }

        .header {
            width: 100%;
            margin-bottom: 10px;
        }

        .logo {
            width: 70px;
        }

        .company {
            text-align: center;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #198754;
        }

        .company small {
            font-size: 11px;
            color: #555;
        }

        .divider {
            border-bottom: 2px solid #198754;
            margin: 10px 0 15px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .date-range {
            text-align: center;
            font-size: 11px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #198754;
            color: white;
            padding: 8px;
            font-size: 11px;
        }

        td {
            padding: 7px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f4f6f7;
        }

        .total {
            margin-top: 12px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #198754;
        }

        /* 🔥 SIGNATURE SECTION */
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 200px;
        }

        .signature-label {
            font-size: 11px;
            margin-top: 5px;
        }

        /* 🔥 STAMP STYLE */
        .stamp {
            border: 2px dashed #198754;
            color: #198754;
            font-weight: bold;
            font-size: 14px;
            padding: 10px;
            text-align: center;
            transform: rotate(-10deg);
            display: inline-block;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

    </style>
</head>
<body>

<table class="header">
    <tr>
        <td>
            @if(!empty($settings->logo) && file_exists(public_path('uploads/settings/'.$settings->logo)))
                <img src="{{ public_path('uploads/settings/'.$settings->logo) }}" class="logo">
            @endif
        </td>
        <td class="company">
            <div class="company-name">{{ $settings->company_name ?? 'My Company' }}</div>
            <small>{{ $settings->address ?? '' }}</small><br>
        </td>
    </tr>
</table>

<div class="divider"></div>

<div class="title">SALES REPORT</div>

<div style="text-align:center; font-size:11px; margin-bottom:10px;">
    <strong>Date Range:</strong>
    @if($from && $to)
        {{ \Carbon\Carbon::parse($from)->format('d M Y') }} 
        - 
        {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
    @else
        ALL RECORDS
    @endif
</div>


<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Salesperson</th>
            <th>Receipt</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->salesperson_name }}</td>
            <td>{{ $row->receipt_no }}</td>
            <td>₦{{ number_format($row->total_amount, 2) }}</td>
            <td>{{ $row->payment_method }}</td>
            <td>
                {{ \Carbon\Carbon::parse($row->created_at)
                    ->timezone($settings->timezone ?? 'Africa/Lagos')
                    ->format('d M Y, h:i A') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total">
    Total Sales: ₦{{ number_format($total, 2) }}
</div>

<!-- 🔥 SIGNATURE + STAMP -->
<div class="signature-section">
    <table class="signature-table">
        <tr>
            <!-- SIGNATURE -->
            <td>
                <div class="signature-line"></div>
                <div class="signature-label">
                    Authorized Signature
                </div>
            </td>

            <!-- STAMP -->
            <td style="text-align: right;">
                <div class="stamp">
                    SYSTEM<br>VERIFIED
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    Generated on {{
        \Carbon\Carbon::now($settings->timezone ?? 'Africa/Lagos')
            ->format('d M Y, h:i A')
    }}
</div>

</body>
</html>