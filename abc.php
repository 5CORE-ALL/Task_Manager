<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

$servername = "118.139.177.19";
$username = "coreadmin";
$password = "g*b(=6hZel*z";
$dbname = "core_hrms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    
    if (!$file) {
        echo "Please upload a file.";
        exit;
    }

    // Load Excel file
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    // Get headers
    $headers = array_shift($data);

    // Mapping of Excel columns to DB columns
    $columnMap = [
        'ID' => 'task_id',
        'Group' => 'group',
        'Task' => 'title',
        'Description' => 'description',
        'Assignor' => 'assignor',
        'Assignee' => 'assign_to',
        'Priority' => 'priority',
        'TID' => 'start_date',
        'Due' => 'due_date',
        'Status' => 'status',
        'L 1' => 'link1',
        'L 2' => 'link2'
    ];

    // Find the column indexes in the Excel file
    $columnIndexes = [];
    foreach ($headers as $index => $header) {
        if (isset($columnMap[$header])) {
            $columnIndexes[$columnMap[$header]] = $index;
        }
    }

    // Insert data into MySQL
    $totalRows = count($data);
    $insertedRows = 0;

    foreach ($data as $row) {
        $values = [];
        foreach ($columnIndexes as $dbColumn => $excelIndex) {
            $values[$dbColumn] = isset($row[$excelIndex]) ? $conn->real_escape_string($row[$excelIndex]) : '';
        }

        // Convert empty strings to NULL where necessary
        $sql = "INSERT INTO tasks (task_id, `group`, title, description, assignor, assign_to, priority, start_date, due_date, status, link1, link2)
                VALUES ('{$values['task_id']}', '{$values['group']}', '{$values['title']}', '{$values['description']}', 
                        '{$values['assignor']}', '{$values['assign_to']}', '{$values['priority']}', 
                        '{$values['start_date']}', '{$values['due_date']}', '{$values['status']}', 
                        '{$values['link1']}', '{$values['link2']}')";

        if ($conn->query($sql) === TRUE) {
            $insertedRows++;
        }
    }

    echo "<script>alert('Successfully inserted $insertedRows out of $totalRows rows');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Excel Upload</title>
    <script>
        function showProgress() {
            document.getElementById("progress").style.display = "block";
        }
    </script>
</head>
<body>

<form method="post" enctype="multipart/form-data" onsubmit="showProgress()">
    <input type="file" name="excel_file" accept=".xlsx, .xls" required>
    <button type="submit" name="submit">Upload & Import</button>
</form>

<div id="progress" style="display:none;">
    <p>Processing... Please wait.</p>
</div>

</body>
</html>
