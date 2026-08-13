<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $receipt->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }
        .receipt-title {
            font-size: 28px;
            color: #333;
            margin: 10px 0;
        }
        .receipt-info {
            margin: 30px 0;
        }
        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-info td {
            padding: 12px 8px;
            border-bottom: 1px solid #ddd;
        }
        .receipt-info td:first-child {
            font-weight: bold;
            width: 180px;
            color: #555;
        }
        .total-section {
            margin-top: 30px;
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
        }
        .total {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            color: #4CAF50;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        {{-- Logo - make sure to put your logo at public/images/logo.png --}}
        {{-- If you don't have a logo yet, comment out the img tag below --}}
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}"
             alt="Company Logo" class="logo">

        <div class="company-name">Your Company Name</div>
        <h1 class="receipt-title">Payment Receipt</h1>
    </div>

    <div class="receipt-info">
        <table>
            <tr>
                <td>Receipt Number:</td>
                <td>#{{ $receipt->id }}</td>
            </tr>
            <tr>
                <td>Date Issued:</td>
                <td>{{ date('F d, Y', strtotime($receipt->created_at)) }}</td>
            </tr>
            <tr>
                <td>Customer Name:</td>
                <td>{{ $receipt->customer_name }}</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>{{ $receipt->description }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>Credit Card</td>
            </tr>
        </table>
    </div>

    <div class="total-section">
        <div class="total">
            Total Amount Paid: ${{ number_format($receipt->amount, 2) }}
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>If you have any questions, please contact us at support@yourcompany.com</p>
    </div>
</body>
</html>
