<?php
/**
 * Lurnixe Health Card System - DataTables Server Side Query
 * June 2026
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Enforce login validation
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

// Read parameters from DataTables GET request
$draw = intval($_GET['draw'] ?? 1);
$start = intval($_GET['start'] ?? 0);
$length = intval($_GET['length'] ?? 25);
$search_value = $_GET['search']['value'] ?? '';

// Read filter parameters
$status_filter = $_GET['status'] ?? '';
$blood_filter = $_GET['blood_group'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Determine sort order
$columns = ['photo', 'name', 'member_id', 'mobile', 'blood_group', 'validity_date', 'status'];
$order_col_idx = intval($_GET['order'][0]['column'] ?? 1);
$order_dir = ($_GET['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$order_by = $columns[$order_col_idx] ?? 'name';

try {
    // 1. Total records count
    $total_records = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
    
    // 2. Build filter conditions
    $conditions = [];
    $params = [];
    
    if (!empty($search_value)) {
        $conditions[] = "(name LIKE ? OR member_id LIKE ? OR mobile LIKE ?)";
        $params[] = "%$search_value%";
        $params[] = "%$search_value%";
        $params[] = "%$search_value%";
    }
    
    if (!empty($status_filter)) {
        $conditions[] = "status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($blood_filter)) {
        $conditions[] = "blood_group = ?";
        $params[] = $blood_filter;
    }
    
    if (!empty($date_from)) {
        $conditions[] = "created_at >= ?";
        $params[] = $date_from . " 00:00:00";
    }
    
    if (!empty($date_to)) {
        $conditions[] = "created_at <= ?";
        $params[] = $date_to . " 23:59:59";
    }
    
    $where_sql = "";
    if (count($conditions) > 0) {
        $where_sql = " WHERE " . implode(" AND ", $conditions);
    }
    
    // 3. Filtered records count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM members" . $where_sql);
    $count_stmt->execute($params);
    $filtered_records = $count_stmt->fetchColumn();
    
    // 4. Fetch data
    $sql = "SELECT * FROM members" . $where_sql . " ORDER BY " . $order_by . " " . $order_dir . " LIMIT " . $start . ", " . $length;
    $data_stmt = $pdo->prepare($sql);
    $data_stmt->execute($params);
    $rows = $data_stmt->fetchAll();
    
    // Package for output
    $data = [];
    foreach ($rows as $row) {
        $data[] = [
            'photo' => $row['photo'],
            'name' => htmlspecialchars($row['name']),
            'member_id' => $row['member_id'],
            'mobile' => htmlspecialchars($row['mobile']),
            'blood_group' => $row['blood_group'],
            'validity_date' => format_date($row['validity_date']),
            'status' => $row['status']
        ];
    }
    
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($total_records),
        "recordsFiltered" => intval($filtered_records),
        "data" => $data
    ]);
    
} catch (PDOException $e) {
    error_log("AJAX Member search query error: " . $e->getMessage());
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Database query error: " . $e->getMessage()
    ]);
}
