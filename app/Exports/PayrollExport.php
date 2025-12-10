<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PayrollExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $payrolls;
    protected $month;

    public function __construct($payrolls, $month)
    {
        $this->payrolls = $payrolls;
        $this->month = $month;
    }

    public function collection()
    {
        return $this->payrolls;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Net Payable', 
            'Bank 1',
            'Bank 2',
            'UPI'
        ];
    }

    public function map($payroll): array
    {
        return [
            $payroll->name ?? 'N/A',
            $payroll->total_payable ? number_format($payroll->total_payable, 0) : '0',
            $payroll->bank1 ?? '',
            $payroll->bank2 ?? '',
            $payroll->up ?? ''
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Name
            'B' => 20,  // Net Payable
            'C' => 30,  // Bank 1
            'D' => 30,  // Bank 2
            'E' => 25,  // UPI
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '495057'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Style all data rows
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:E' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align Net Payable column
        $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
