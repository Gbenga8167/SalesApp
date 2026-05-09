<!DOCTYPE html>
<html>

<head>

    <style>

        /* =========================================
           PAGE BODY
        ========================================== */
        body{
            font-family: DejaVu Sans;
            font-size:12px;
            color:#2c2c2c;
        }



        /* =========================================
           HEADER TABLE
        ========================================== */
        .header-table{
            width:100%;
            border-collapse: collapse;
            margin-bottom:10px;
        }



        /* =========================================
           COMPANY LOGO
        ========================================== */
        .logo{
            width:70px;
        }



        /* =========================================
           COMPANY DETAILS
        ========================================== */
        .company-details{
            text-align:center;
        }



        /* =========================================
           COMPANY NAME
        ========================================== */
        .company-name{
            font-size:24px;
            font-weight:bold;
            color:#198754;
        }



        /* =========================================
           COMPANY ADDRESS
        ========================================== */
        .company-address{
            font-size:11px;
            color:#555;
        }



        /* =========================================
           DIVIDER LINE
        ========================================== */
        .divider{
            border-bottom:2px solid #198754;
            margin:10px 0 15px;
        }



        /* =========================================
           REPORT TITLE
        ========================================== */
        .report-title{
            text-align:center;
            font-size:18px;
            font-weight:bold;
            margin-bottom:5px;
        }



        /* =========================================
           DATE RANGE
        ========================================== */
        .date-range{
            text-align:center;
            font-size:11px;
            margin-bottom:15px;
        }



        /* =========================================
           SUMMARY BOX
        ========================================== */
        .summary-table{
            width:100%;
            margin-bottom:15px;
            border-collapse: collapse;
        }

        .summary-table td{
            padding:10px;
            border:1px solid #ddd;
            font-size:12px;
            font-weight:bold;
        }



        /* =========================================
           MAIN REPORT TABLE
        ========================================== */
        table.report-table{
            width:100%;
            border-collapse: collapse;
        }



        /* =========================================
           TABLE HEADERS
        ========================================== */
        .report-table th{
            background:#198754;
            color:white;
            padding:8px;
            font-size:11px;
            border:1px solid #ddd;
        }



        /* =========================================
           TABLE BODY
        ========================================== */
        .report-table td{
            padding:7px;
            border:1px solid #ddd;
            font-size:11px;
        }



        /* =========================================
           ALTERNATE ROW COLORS
        ========================================== */
        .report-table tbody tr:nth-child(even){
            background:#f4f6f7;
        }



        /* =========================================
           SIGNATURE SECTION
        ========================================== */
        .signature-section{
            width:100%;
            margin-top:40px;
        }



        /* =========================================
           SIGNATURE TABLE
        ========================================== */
        .signature-table{
            width:100%;
            border-collapse: collapse;
        }



        /* =========================================
           SIGNATURE LINE
        ========================================== */
        .signature-line{
            margin-top:30px;
            border-top:1px solid #000;
            width:200px;
        }



        /* =========================================
           SIGNATURE LABEL
        ========================================== */
        .signature-label{
            font-size:11px;
            margin-top:5px;
        }



        /* =========================================
           SYSTEM VERIFIED STAMP
        ========================================== */
        .stamp{
            border:2px solid #198754;
            color: #198754;
            font-weight:bold;
            font-size:13px;
            padding:10px;
            display:inline-block;
            text-align:center;
        }



        /* =========================================
           FOOTER
        ========================================== */
        .footer{
            margin-top:25px;
            text-align:center;
            font-size:10px;
            color:#777;
        }

    </style>

</head>

<body>



<!-- =========================================
     COMPANY HEADER
========================================= -->
<table class="header-table">

    <tr>

        <!-- COMPANY LOGO -->
        <td width="80">

            @if(
                !empty($settings->logo)
                &&
                file_exists(
                    public_path('uploads/settings/'.$settings->logo)
                )
            )

                <img
                    src="{{ public_path('uploads/settings/'.$settings->logo) }}"
                    class="logo"
                >

            @endif

        </td>



        <!-- COMPANY DETAILS -->
        <td class="company-details">

            <!-- COMPANY NAME -->
            <div class="company-name">

                {{ $settings->company_name ?? 'My Company' }}

            </div>



            <!-- COMPANY ADDRESS -->
            <div class="company-address">

                {{ $settings->address ?? '' }} <br> 
                {{ $settings->phone_number ?? ''}} <br>
                 {{$settings->email}}

            </div>

        </td>

    </tr>

</table>



<!-- DIVIDER -->
<div class="divider"></div>



<!-- REPORT TITLE -->
<div class="report-title">

    PROFIT REPORT

</div>



<!-- DATE RANGE -->
<div class="date-range">

    <strong>Date Range:</strong>

    @if($from && $to)

        {{ \Carbon\Carbon::parse($from)->format('d M Y') }}

        -

        {{ \Carbon\Carbon::parse($to)->format('d M Y') }}

    @else

        ALL RECORDS

    @endif

</div>



<!-- =========================================
     SUMMARY SECTION
========================================= -->
<table class="summary-table">

    <tr>

        <!-- TOTAL SALES -->
        <td style="color:#198754;">

            Total Sales:
            ₦{{ number_format($totalSales, 2) }}

        </td>



        <!-- TOTAL COST -->
        <td style="color:red;">

            Total Cost:
            ₦{{ number_format($totalCost, 2) }}

        </td>



        <!-- TOTAL PROFIT -->
        <td style="color:green;">

            Total Profit:
            ₦{{ number_format($totalProfit, 2) }}

        </td>

    </tr>

</table>



<!-- =========================================
     MAIN REPORT TABLE
========================================= -->
<table class="report-table">

    <thead>

        <tr>

            <th>#</th>
            <th>Salesperson</th>
            <th>Product</th>
            <th>Category</th>
            <th>Qty</th>
            <th>Sales</th>
            <th>Cost</th>
            <th>Profit</th>
            <th>Date</th>

        </tr>

    </thead>



    <tbody>

        @foreach($data as $index => $row)

        <tr>

            <!-- SERIAL NUMBER -->
            <td>
                {{ $index + 1 }}
            </td>



            <!-- SALESPERSON -->
            <td>
                {{ $row->salesperson_name }}
            </td>



            <!-- PRODUCT -->
            <td>
                {{ $row->product_name }}
            </td>



            <!-- CATEGORY -->
            <td>
                {{ $row->category }}
            </td>



            <!-- QUANTITY -->
            <td>
                {{ $row->quantity }}
            </td>



            <!-- SALES -->
            <td>
                ₦{{ number_format($row->subtotal, 2) }}
            </td>



            <!-- COST -->
            <td>
                ₦{{ number_format($row->total_cost, 2) }}
            </td>



            <!-- PROFIT -->
            <td>
                ₦{{ number_format($row->profit, 2) }}
            </td>



            <!-- DATE -->
            <td>

                {{
                    \Carbon\Carbon::parse($row->created_at)
                    ->timezone($settings->timezone ?? 'Africa/Lagos')
                    ->format('d M Y, h:i A')
                }}

            </td>

        </tr>

        @endforeach

    </tbody>

</table>



<!-- =========================================
     SIGNATURE SECTION
========================================= -->
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



            <!-- SYSTEM VERIFIED STAMP -->
            <td style="text-align:right;">

                <div class="stamp">

                    SYSTEM <br>
                    VERIFIED

                </div>

            </td>

        </tr>

    </table>

</div>



<!-- =========================================
     FOOTER
========================================= -->
<div class="footer">

    Generated on

    {{
        \Carbon\Carbon::now(
            $settings->timezone ?? 'Africa/Lagos'
        )->format('d M Y, h:i A')
    }}

</div>



</body>
</html>