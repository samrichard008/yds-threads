<?php
// api/export_courier.php - Export YDS Threads orders for Koombiyo & Domex Portals
require_once __DIR__ . '/../config.php';

$courier = strtolower($_GET['courier'] ?? 'koombiyo');
$status = $_GET['status'] ?? 'all';

$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];
if ($status !== 'all') {
    $sql .= " AND order_status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$filename = "yds_threads_{$courier}_" . date('Y-m-d_Hi') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

if ($courier === 'domex') {
    fputcsv($out, ['Waybill_No', 'Reference_No', 'Consignee_Name', 'Delivery_Address', 'City', 'Contact_1', 'Contact_2', 'COD_Amount_LKR', 'Package_Contents', 'Status']);
    foreach ($orders as $o) {
        fputcsv($out, [
            $o['tracking_number'] ?: 'DX' . $o['id'],
            $o['order_code'],
            $o['customer_name'],
            $o['delivery_address'],
            $o['city_district'] ?: 'Colombo',
            $o['customer_phone1'],
            $o['customer_phone2'] ?: '',
            number_format($o['total_amount'], 2, '.', ''),
            $o['product_name'] . ' (' . ($o['variant_details'] ?: 'Standard') . ')',
            $o['order_status']
        ]);
    }
} else {
    // Koombiyo Official
    fputcsv($out, ['Waybill_No', 'Order_ID', 'Receiver_Name', 'Delivery_Address', 'District_City', 'Primary_Phone', 'Alternative_Phone', 'COD_Amount_Rs', 'Item_Description', 'Payment_Type', 'Order_Date']);
    foreach ($orders as $o) {
        fputcsv($out, [
            $o['tracking_number'] ?: 'KB' . $o['id'],
            $o['order_code'],
            $o['customer_name'],
            $o['delivery_address'],
            $o['city_district'] ?: 'Colombo',
            $o['customer_phone1'],
            $o['customer_phone2'] ?: '',
            number_format($o['total_amount'], 2, '.', ''),
            $o['product_name'] . ' - ' . ($o['variant_details'] ?: 'Standard'),
            $o['payment_method'],
            $o['created_at']
        ]);
    }
}

fclose($out);
exit;
