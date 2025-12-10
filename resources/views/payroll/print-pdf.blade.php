<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Report - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        
        .table-container {
            width: 100%;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        
        thead th {
            background-color: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
        }
        
        tbody td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e8f4fd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .bank-info {
            max-width: 150px;
            word-wrap: break-word;
            line-height: 1.3;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            .header {
                margin-bottom: 20px;
            }
            
            /* Hide elements not needed in print */
            .no-print {
                display: none !important;
            }
            
            /* Ensure table fits on page */
            table {
                font-size: 10px;
            }
            
            .bank-info {
                max-width: 120px;
                font-size: 9px;
            }
        }
        
        /* Print button for manual printing when PDF generation fails */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    @if(isset($print_mode) && $print_mode)
        <button class="print-button no-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Print to PDF
        </button>
    @endif
    
    <div class="header">
        <h1>Payroll Report</h1>
        <p><strong>Month:</strong> {{ $month }}</p>
        <p><strong>Generated on:</strong> {{ $generated_at }}</p>
        <p><strong>Total Records:</strong> {{ count($payrolls) }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Sl No</th>
                    <th>Name</th>
                    <th>Net Payable</th>
                    <th>Bank 1</th>
                    <th>Bank 2</th>
                    <th>UP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $index => $payroll)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $payroll->name ?? 'N/A' }}</strong></td>
                        <td class="text-right">
                            <strong>{{ $payroll->total_payable ? number_format((float)$payroll->total_payable, 0) : '0' }}</strong>
                        </td>
                        <td class="bank-info">{{ $payroll->bank1 ?? '-' }}</td>
                        <td class="bank-info">{{ $payroll->bank2 ?? '-' }}</td>
                        <td>{{ $payroll->up ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px; color: #7f8c8d;">
                            No payroll records found for {{ $month }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This document was generated automatically on {{ date('Y-m-d H:i:s') }}</p>
        <p>© {{ date('Y') }} 5Core HR Management System</p>
    </div>
</body>
</html>
