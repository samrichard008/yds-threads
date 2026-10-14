<?php
// api/place_order.php - Ultra Fast 1-Click Checkout Handler for YDS Threads
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$customerName = trim($input['name'] ?? '');
$phone1 = trim($input['phone1'] ?? '');
$phone2 = trim($input['phone2'] ?? '');
$address = trim($input['address'] ?? '');
$city = trim($input['city'] ?? 'Colombo');
$productName = trim($input['product_name'] ?? 'YDS Threads Collection');
$variant = trim($input['variant'] ?? '');
$quantity = (int)($input['quantity'] ?? 1);
if ($quantity < 1) $quantity = 1;

$subtotal = (float)($input['subtotal'] ?? 0);
$deliveryFee = 350.00;
$total = $subtotal + $deliveryFee;
$paymentMethod = strtoupper(trim($input['payment_method'] ?? 'COD'));
$bankSlipUrl = trim($input['bank_slip_url'] ?? '');

if (!$customerName || !$phone1 || !$address) {
    echo json_encode([
        'success' => false,
        'error' => 'Please provide Customer Name, Mobile Number, and Delivery Address.'
    ]);
    exit;
}

$orderCode = 'YDS-' . rand(1020, 9999);
$tracking = 'KB' . rand(1000000, 9999999) . 'LK';

$stmt = $pdo->prepare("
    INSERT INTO orders (
        order_code, customer_name, customer_phone1, customer_phone2,
        delivery_address, city_district, product_name, variant_details,
        quantity, subtotal, delivery_fee, total_amount, payment_method,
        bank_slip_url, order_status, courier_name, tracking_number
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', 'Koombiyo', ?)
");

$stmt->execute([
    $orderCode,
    $customerName,
    $phone1,
    $phone2,
    $address,
    $city,
    $productName,
    $variant,
    $quantity,
    $subtotal,
    $deliveryFee,
    $total,
    $paymentMethod,
    $bankSlipUrl,
    $tracking
]);

$orderId = $pdo->lastInsertId();

// Automatic CRM enrichment
try {
    $cleanP = preg_replace('/\D/', '', $phone1);
    $cSearch = '%' . substr($cleanP, -7) . '%';
    $cFind = $pdo->prepare("SELECT id, total_orders, total_spent FROM customers WHERE phone1 LIKE ? OR phone2 LIKE ? LIMIT 1");
    $cFind->execute([$cSearch, $cSearch]);
    $cust = $cFind->fetch();

    if ($cust) {
        $newOrd = (int)$cust['total_orders'] + 1;
        $newSp = (float)$cust['total_spent'] + $total;
        $cType = $newOrd >= 2 ? 'VIP' : 'CUSTOMER';
        $cUp = $pdo->prepare("UPDATE customers SET total_orders = ?, total_spent = ?, customer_type = ?, address = ?, city_district = ? WHERE id = ?");
        $cUp->execute([$newOrd, $newSp, $cType, $address, $city, $cust['id']]);
    } else {
        $cIns = $pdo->prepare("INSERT INTO customers (name, phone1, phone2, address, city_district, customer_type, total_orders, total_spent) VALUES (?, ?, ?, ?, ?, 'CUSTOMER', 1, ?)");
        $cIns->execute([$customerName, $phone1, $phone2, $address, $city, $total]);
    }
} catch (Exception $e) {}

// Trigger WhatsApp notification via local server on 3000
try {
    $waMsg = "✨ *YDS THREADS — ORDER CONFIRMED* ✨\n\nDear {$customerName},\nThank you for shopping with YDS Threads! Your order has been placed successfully.\n\n📋 *Order ID:* #{$orderCode}\n👗 *Item:* {$productName}\n🏷️ *Details:* {$variant}\n💵 *Total to pay (COD):* Rs. " . number_format($total, 2) . " (Includes Rs. 350 Islandwide Delivery)\n🚚 *Courier:* Koombiyo Delivery (Tracking: {$tracking})\n\nOur team is packing your luxury package. Expected delivery within 2-3 working days. Thank you! 👑";

    $ch = curl_init('http://127.0.0.1:3000/send-message');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'phone' => $phone1,
        'message' => $waMsg
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_exec($ch);
    curl_close($ch);
} catch (Exception $e) {}

echo json_encode([
    'success' => true,
    'message' => 'Order placed successfully!',
    'order' => [
        'id' => $orderId,
        'order_code' => $orderCode,
        'customer_name' => $customerName,
        'phone1' => $phone1,
        'address' => $address,
        'product' => $productName,
        'variant' => $variant,
        'total' => $total,
        'tracking' => $tracking
    ]
]);
