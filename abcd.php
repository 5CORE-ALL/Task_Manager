<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    // Load Excel file
    $filePath = '2285.xlsx'; // Correct file path
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();

    // Convert all formulas to values
    foreach ($sheet->getCellCollection() as $cell) {
        if ($cell->isFormula()) {
            $cell->setValueExplicit($cell->getCalculatedValue(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }
    }

    // Read data after converting formulas to values
    $data = $sheet->toArray(null, true, true, false);

    echo "<pre>";
    print_r($data);
    echo "</pre>";

} catch (\PhpOffice\PhpSpreadsheet\Calculation\Exception $e) {
    echo "Formula Error: " . $e->getMessage();
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo "File Read Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
?>
