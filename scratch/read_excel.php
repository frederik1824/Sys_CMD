<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'c:/Users/flopez/Videos/sys_carnet/Pendientes Frederik/DATA ROBERITZA.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->rangeToArray('A1:L2'); 
echo json_encode($data, JSON_PRETTY_PRINT);
