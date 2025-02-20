<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

function readExcelFile($filePath) {
    if (file_exists($filePath)) {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Hapus baris pertama (header)
        array_shift($data);

        return $data;
    }
    return null;
}

$month = isset($_GET['month']) ? $_GET['month'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;

$machineResults = [];
$rejectTypes = [];
$downtimeTypes = [];

if ($month && $year) {
    $folderPath = __DIR__ . "/pts_db/$year/$month/";
    $filePaths = [
        'output' => $folderPath . "output_entries.xlsx",
        'jobOrder' => $folderPath . "job_order_changes.xlsx",
        'reject' => $folderPath . "reject_entries.xlsx",
        'machineChanges' => $folderPath . "machine_changes.xlsx",
    ];

    $jobOrderData = readExcelFile($filePaths['jobOrder']);
    $outputData = readExcelFile($filePaths['output']);
    $rejectData = readExcelFile($filePaths['reject']);
    $machineChangesData = readExcelFile($filePaths['machineChanges']);

    if (!$jobOrderData || !$outputData || !$rejectData || !$machineChangesData) {
        die("Salah satu file tidak ditemukan atau kosong.");
    }

    // Ambil jenis reject dari file
    foreach ($rejectData as $row) {
        if (!empty($row[2]) && !empty($row[3])) {
            $rejectTypes[trim($row[2])] = true;
        }
    }

    // Ambil jenis downtime dari file
    foreach ($machineChangesData as $row) {
        $downtimeType = trim($row[3]);
        if (!empty($downtimeType) && strtolower($downtimeType) !== "null") {
            $downtimeTypes[$downtimeType] = true;
        }
    }

    

    // Inisialisasi status downtime terakhir
    $lastStatus = [];
    $lastDowntimeReason = [];

    // Proses berdasarkan MachineID dengan filter bulan & tahun
    foreach ($jobOrderData as $row) {
        if (!empty($row[1]) && !empty($row[2]) && !empty($row[6])) {
            $jobOrderID = trim($row[1]);
            $machineID = trim($row[2]);
            if ($machineID === "NULL" || $machineID === "" || $machineID === null) continue; // Hapus baris NULL
            
            $postingDate = DateTime::createFromFormat('Y-m-d H:i:s', trim($row[6]));

            if ($postingDate && (int)$postingDate->format('m') == (int)$month && (int)$postingDate->format('Y') == (int)$year) {
                if (!isset($machineResults[$machineID])) {
                    $machineResults[$machineID] = [
                        'totalOutput' => 0,
                        'totalDowntime' => 0,
                        'rejects' => array_fill_keys(array_keys($rejectTypes), 0),
                        'downtimes' => array_fill_keys(array_keys($downtimeTypes), 0),
                    ];
                }

                // Hitung total output per MachineID
                foreach ($outputData as $outRow) {
                    if (trim($outRow[2]) == $jobOrderID) {
                        $machineResults[$machineID]['totalOutput'] += (float) $outRow[3];
                    }
                }
            }
            // Hitung reject per MachineID
            foreach ($rejectData as $rejRow) {
                if (trim($rejRow[1]) == $jobOrderID) {
                    $rejectType = trim($rejRow[2]);
                    $rejectQty = (float) $rejRow[3];
                    if (isset($machineResults[$machineID]['rejects'][$rejectType])) {
                        $machineResults[$machineID]['rejects'][$rejectType] += $rejectQty;
                    }
                }
            }
        }
    }


    // Proses downtime per MachineID
    foreach ($machineChangesData as $mcRow) {
        if (!empty($mcRow[1]) && !empty($mcRow[3]) && !empty($mcRow[5])) {
            $machineID = trim($mcRow[1]);
            if ($machineID === "NULL" || $machineID === "" || $machineID === null) continue;
            
            $downtimeType = trim($mcRow[3]);
            $downtimeDuration = round((float) $mcRow[5] / 3600, 2); // Konversi ke jam
            $status = trim($mcRow[2]); // Status ['1' atau '0']
    
            if (!isset($machineResults[$machineID])) {
                $machineResults[$machineID] = [
                    'totalOutput' => 0,
                    'totalDowntime' => 0,
                    'rejects' => [],
                    'downtimes' => array_fill_keys(array_keys($downtimeTypes), 0),
                ];
            }
    
            if (!isset($lastStatus[$machineID])) {
                $lastStatus[$machineID] = '1'; // Default berjalan
                $lastDowntimeReason[$machineID] = null;
            }
    
            if ($status === '0' && !empty($downtimeType) && isset($downtimeTypes[$downtimeType])) {
                // Jika status '0', hitung downtime
                $machineResults[$machineID]['downtimes'][$downtimeType] += $downtimeDuration;
                $machineResults[$machineID]['totalDowntime'] += $downtimeDuration;
                $lastDowntimeReason[$machineID] = $downtimeType;
            } elseif ($status === '1' && isset($lastDowntimeReason[$machineID])) {
                // Jika status '1', gunakan alasan downtime terakhir
                $machineResults[$machineID]['downtimes'][$lastDowntimeReason[$machineID]] += $downtimeDuration;
                $machineResults[$machineID]['totalDowntime'] += $downtimeDuration;
            }
    
            $lastStatus[$machineID] = $status;
        }
    }
}

    ?>