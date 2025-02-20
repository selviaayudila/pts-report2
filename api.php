<?php
require_once 'config.php'; // Koneksi database

header('Content-Type: application/json');

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!isset($data['timePeriod']) || !isset($data['machines']) || !is_array($data['machines']) || empty($data['machines'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid required parameters']);
    exit;
}

// Fungsi untuk mendapatkan rentang tanggal
function getDateRange($timePeriod, $customFrom = null, $customTo = null) {
    $from = new DateTime();
    $to = new DateTime();

    switch ($timePeriod) {
        case 'today':
            $from->setTime(0, 0, 0);
            $to->setTime(23, 59, 59);
            break;
        case 'yesterday':
            $from->modify('-1 day')->setTime(0, 0, 0);
            $to->modify('-1 day')->setTime(23, 59, 59);
            break;
        case 'this_week':
            $from->modify('monday this week')->setTime(0, 0, 0);
            $to->modify('sunday this week')->setTime(23, 59, 59);
            break;
        case 'last_week':
            $from->modify('monday last week')->setTime(0, 0, 0);
            $to->modify('sunday last week')->setTime(23, 59, 59);
            break;
        case 'this_month':
            $from->modify('first day of this month')->setTime(0, 0, 0);
            $to->modify('last day of this month')->setTime(23, 59, 59);
            break;
        case 'last_month':
            $from->modify('first day of last month')->setTime(0, 0, 0);
            $to->modify('last day of last month')->setTime(23, 59, 59);
            break;
        case 'custom':
            if (!$customFrom || !$customTo) {
                http_response_code(400);
                echo json_encode(['error' => 'Custom date range requires both dateFrom and toDate']);
                exit;
            }
            $from = new DateTime($customFrom);
            $to = new DateTime($customTo);
            $to->setTime(23, 59, 59);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid timePeriod value']);
            exit;
    }

    return ['from' => $from->format('Y-m-d H:i:s'), 'to' => $to->format('Y-m-d H:i:s')];
}

try {
    $dateRange = getDateRange($data['timePeriod'], $data['dateFrom'] ?? null, $data['toDate'] ?? null);
    $machines = $data['machines'];
    $placeholders = implode(',', array_fill(0, count($machines), '?'));

    $query = "
    SELECT machineID, downtimeReason, timestamp, machineStatus
    FROM machine_changes
    WHERE machineID IN ($placeholders) 
    AND timestamp BETWEEN ? AND ? 
    ORDER BY machineID, timestamp;
    ";

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $mysqli->error);
    }

    $types = str_repeat('s', count($machines)) . 'ss';
    $params = array_merge($machines, [$dateRange['from'], $dateRange['to']]);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $rawDowntimeData = [];
    while ($row = $result->fetch_assoc()) {
        $rawDowntimeData[] = $row;
    }

    $downtimeData = [];
    for ($i = 0; $i < count($rawDowntimeData); $i++) {
        $current = $rawDowntimeData[$i];
        
        if ($current['machineStatus'] == '0') { // Mulai downtime
            $nextTimestamp = null;
            
            // Cari entri berikutnya untuk mesin yang sama sebagai endDowntime
            for ($j = $i + 1; $j < count($rawDowntimeData); $j++) {
                if ($rawDowntimeData[$j]['machineID'] == $current['machineID']) {
                    $nextTimestamp = $rawDowntimeData[$j]['timestamp'];
                    break;
                }
            }

            $machineID = $current['machineID'];
            if (!isset($downtimeData[$machineID])) {
                $downtimeData[$machineID] = [
                    'machine_id' => $machineID,
                    'downtimes' => []
                ];
            }

            $downtimeData[$machineID]['downtimes'][] = [
                'start' => date('Y-m-d H:i', strtotime($current['timestamp'])),
                'end' => $nextTimestamp ? date('Y-m-d H:i', strtotime($nextTimestamp)) : null,
                'reason' => $current['downtimeReason']
            ];
        }
    }

    // Tambahkan chartDuration berdasarkan date range
    $output = [
        'chartDuration' => [
            'from' => $dateRange['from'],
            'to' => $dateRange['to']
        ],
        'machines' => array_values($downtimeData)
    ];

    echo json_encode($output, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
