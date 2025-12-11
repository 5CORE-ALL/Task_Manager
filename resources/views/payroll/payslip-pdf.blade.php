<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salary Slip</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
        margin: 0;
    }
    .salary-slip {
        max-width: 900px;
        margin: auto;
        background: white;
        padding: 0 30px 20px;
        border: 1px solid #ccc;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    /* HEADER */
    .header {
        background-color: white;
        margin-bottom: 0;
    }
    .logo-row {
        display: table;
        width: 100%;
    }
    .header-left {
        display: table-cell;
        vertical-align: middle;
        width: 140px;
        text-align: center;
        padding: 15px 20px;
    }
    .header-left img {
        height: 60px;
        margin-bottom: 5px;
    }
    .header-left span {
        font-size: 14px;
        font-weight: bold;
    }
    .header-right {
        display: table-cell;
        vertical-align: middle;
        text-align: right;
        padding-left: 15px;
    }
    .header-right h1 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
    }
    .header-right p {
        margin: 2px 0;
        font-size: 12px;
        color: #333;
    }
    .logo-line {
        border-bottom: 2px solid #e36c5c;
        margin-bottom: 20px;
    }

    /* MOTIVATION */
    .motivation {
        text-align: center;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 25px;
        color: #c20000ff;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th {
        text-align: left;
        font-weight: bold;
        color: #e36c5c;
        border-bottom: 2px solid #000;
        padding: 8px;
        font-size: 14px;
        background-color: #fafafa;
    }
    td {
        padding: 8px;
        font-size: 14px;
        border-bottom: 1px solid #ccc;
    }
    tr:last-child td {
        border-bottom: none;
    }
    .total-row td {
        font-weight: bold;
        background-color: #fafafa;
    }
    .bank-details {
        font-size: 14px;
        margin-top: 10px;
    }
    .bank-details td {
        padding: 6px 0;
        border-bottom: 1px solid #ccc;
    }
    .bank-details tr:first-child td {
        border-bottom: none;
    }

    /* BANK SECTION */
    .bank-section {
        border-top: 2px solid #e36c5c;
        padding-top: 15px;
        margin-top: 20px;
    }

    /* FOOTER */
    .footer {
        background-color: #e36c5c;
        padding: 15px;
        margin-top: 20px;
        text-align: center;
    }
    .footer-email {
        font-size: 14px;
        font-weight: bold;
        color: white;
    }
    
    /* Hide print controls in PDF */
    .print-controls {
        display: none;
    }
</style>
</head>
<body>

<div class="salary-slip">
    <!-- HEADER -->
    <div class="header">
        <div class="logo-row">
            <div class="header-left">
                @php
                    $logoPath = public_path('images/WhatsApp Image 2025-08-10 at 05.13.57_627fe9be.jpg');
                    $logoExists = file_exists($logoPath);
                    $logoBase64 = '';
                    if ($logoExists) {
                        $imageData = file_get_contents($logoPath);
                        $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($imageData);
                    }
                @endphp
                @if($logoExists && $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-width: 140px; max-height: 60px;">
                @else
                    <span style="font-size: 14px; font-weight: bold;">5CORE INC</span>
                @endif
            </div>
            <div class="header-right">
                <h1>SALARY SLIP</h1>
                <p>1221 W Sandusky Ave Suite C,Bellefontaine OH 43311</p>
                <p>+19513836157</p>
            </div>
        </div>
        <div class="logo-line"></div>
    </div>

    <!-- Employee Details -->
    <table class="bank-details">
        <tr>
            <td><strong>Employee Name:</strong> {{ $payroll->name ?? $employee->name ?? 'N/A' }}</td>
            <td><strong>Designation:</strong> {{ $employee->department ?? $payroll->department ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Email:</strong> {{ $employee->email_address ?? $payroll->email_address ?? 'N/A' }}</td>
            <td><strong>Month:</strong> {{ $payroll->month ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Salary Table -->
    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Salary Previous</td>
            <td>{{ number_format($payroll->sal_previous ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td>Increment</td>
            <td>{{ number_format($payroll->increment ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td>Current Salary</td>
            <td>{{ number_format($payroll->salary_current ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td>Productive Hrs</td>
            <td>{{ round($productive_hrs ?? $payroll->productive_hrs ?? 0) }} hr</td>
        </tr>
        <tr>
            <td>Approved Hrs</td>
            <td>{{ round($approved_hrs ?? $payroll->approved_hrs ?? 0) }} hr</td>
        </tr>
        <tr>
            <td>Incentive</td>
            <td>{{ number_format($payroll->incentive ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td>Payable Amount</td>
            <td>{{ number_format($payroll->payable ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td>Deduction</td>
            <td>{{ number_format($payroll->advance ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td>Extra</td>
            <td>{{ number_format($payroll->extra ?? 0, 0) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total Payable Rs</td>
            <td><strong>{{ number_format($payroll->total_payable ?? 0, 0) }}</strong></td>
        </tr>
    </table>

    <!-- BANK DETAILS SECTION -->
    <div class="bank-section">
        <table class="bank-details">
            <tr>
                <td><strong>Transaction Date:</strong> ___________________</td>
                <td><strong>Signatures:</strong> ___________________</td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-email">
            If you have any queries, Please contact our HR team by dropping an email at hr@5core.com
        </div>
    </div>
    <br>
    <!-- Motivation -->
    <div class="motivation">
        This is a system-generated PDF and does not require a response.
    </div>

</div>

</body>
</html>
