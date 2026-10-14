<?php
// admin/index.php - YDS THREADS Merchant Dashboard & Courier Logistics
require_once __DIR__ . '/../config.php';

// Fetch orders
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();

// Fetch products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// Fetch customers
$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();

// Calculations
$totalRevenue = 0;
$pendingOrders = 0;
foreach ($orders as $o) {
    $totalRevenue += (float)$o['total_amount'];
    if ($o['order_status'] === 'PENDING') $pendingOrders++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YDS THREADS — Merchant Admin & Courier Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0b0f19] text-slate-200 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation -->
    <nav class="bg-[#111726] border-b border-slate-800 px-6 py-4 sticky top-0 z-40 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="../assets/logo.png" alt="YDS Threads" class="h-10 w-auto">
            <div>
                <h1 class="text-white font-black text-sm tracking-tight">YDS THREADS &bull; Merchant Admin</h1>
                <p class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">Courier Dispatch & Order Pipeline</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="../" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> View Store
            </a>
        </div>
    </nav>

    <!-- Main Admin Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-8">

        <!-- 4 KPI Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-[#111726] p-5 rounded-2xl border border-slate-800 shadow-md">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Sales (LKR)</div>
                <div class="text-2xl font-black text-amber-300 mt-1">Rs. <?= number_format($totalRevenue, 0) ?></div>
            </div>
            <div class="bg-[#111726] p-5 rounded-2xl border border-slate-800 shadow-md">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Orders</div>
                <div class="text-2xl font-black text-white mt-1"><?= count($orders) ?></div>
            </div>
            <div class="bg-[#111726] p-5 rounded-2xl border border-slate-800 shadow-md">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pending Dispatches</div>
                <div class="text-2xl font-black text-amber-500 mt-1"><?= $pendingOrders ?></div>
            </div>
            <div class="bg-[#111726] p-5 rounded-2xl border border-slate-800 shadow-md">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active Catalog Items</div>
                <div class="text-2xl font-black text-emerald-400 mt-1"><?= count($products) ?></div>
            </div>
        </div>

        <!-- Section: Orders Pipeline & Courier 1-Click Export -->
        <section class="bg-[#111726] rounded-3xl border border-slate-800 p-6 shadow-xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-base font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-packing text-amber-400"></i>
                        <span>Customer Orders & Courier Dispatch Sheets</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Click export to download CSV formatted directly for Koombiyo or Domex Merchant Portals.</p>
                </div>
                <div class="flex gap-2">
                    <a href="../api/export_courier.php?courier=koombiyo" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-excel"></i> Export Koombiyo CSV
                    </a>
                    <a href="../api/export_courier.php?courier=domex" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-csv"></i> Export Domex CSV
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase font-black text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Order Code</th>
                            <th class="py-3 px-4">Customer Details</th>
                            <th class="py-3 px-4">Delivery Address</th>
                            <th class="py-3 px-4">Ordered Items & Variant</th>
                            <th class="py-3 px-4">COD Total (Rs.)</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">WhatsApp Direct</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 font-medium">
                        <?php foreach ($orders as $ord): ?>
                            <tr class="hover:bg-slate-900/50 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-black text-amber-300 font-mono"><?= htmlspecialchars($ord['order_code']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($ord['tracking_number'] ?: 'Pending') ?></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-white"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <div class="text-emerald-400 font-mono text-[11px]"><i class="fa-solid fa-phone text-[9px]"></i> <?= htmlspecialchars($ord['customer_phone1']) ?></div>
                                    <?php if ($ord['customer_phone2']): ?>
                                        <div class="text-slate-500 font-mono text-[10px]"><?= htmlspecialchars($ord['customer_phone2']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="text-slate-300 truncate"><?= htmlspecialchars($ord['delivery_address']) ?></div>
                                    <span class="text-[10px] bg-slate-900 text-slate-400 px-1.5 py-0.5 rounded font-bold border border-slate-800"><?= htmlspecialchars($ord['city_district'] ?: 'Islandwide') ?></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-200"><?= htmlspecialchars($ord['product_name']) ?></div>
                                    <div class="text-[11px] text-amber-400/80"><?= htmlspecialchars($ord['variant_details'] ?: 'Standard') ?></div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-black text-amber-300">Rs. <?= number_format($ord['total_amount'], 2) ?></div>
                                    <div class="text-[10px] text-slate-400"><?= htmlspecialchars($ord['payment_method']) ?></div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <select onchange="updateStatus(<?= $ord['id'] ?>, this.value)" class="text-xs font-bold rounded-xl border border-slate-700 bg-slate-900 text-slate-200 px-2.5 py-1 focus:ring-1 focus:ring-amber-500">
                                        <option value="PENDING" <?= $ord['order_status'] === 'PENDING' ? 'selected' : '' ?>>⏳ Pending</option>
                                        <option value="CONFIRMED" <?= $ord['order_status'] === 'CONFIRMED' ? 'selected' : '' ?>>✅ Confirmed</option>
                                        <option value="DISPATCHED" <?= $ord['order_status'] === 'DISPATCHED' ? 'selected' : '' ?>>🚚 Dispatched</option>
                                        <option value="DELIVERED" <?= $ord['order_status'] === 'DELIVERED' ? 'selected' : '' ?>>📦 Delivered</option>
                                        <option value="CANCELLED" <?= $ord['order_status'] === 'CANCELLED' ? 'selected' : '' ?>>❌ Cancelled</option>
                                    </select>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $ord['customer_phone1']) ?>?text=<?= urlencode("Hello {$ord['customer_name']}! Regarding your order #{$ord['order_code']} at YDS Threads...") ?>" target="_blank" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 rounded-lg font-bold text-xs transition inline-flex items-center gap-1.5">
                                        <i class="fa-brands fa-whatsapp text-sm text-emerald-400"></i> Chat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section: Product Catalog Inventory -->
        <section class="bg-[#111726] rounded-3xl border border-slate-800 p-6 shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-base font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-shirt text-amber-400"></i>
                        <span>Catalog Inventory (<?= count($products) ?> Luxury Items)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Fashion, Footwear, and Perfumes available on the live storefront.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($products as $p): ?>
                    <div class="bg-slate-900 rounded-2xl border border-slate-800 p-3.5 flex gap-3 items-center">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-16 h-16 rounded-xl object-cover bg-slate-800 border border-slate-700 flex-shrink-0">
                        <div class="min-w-0 flex-1 text-xs">
                            <span class="text-[9px] uppercase font-bold text-amber-400 bg-amber-950/60 px-1.5 py-0.5 rounded"><?= htmlspecialchars($p['category']) ?></span>
                            <h4 class="font-bold text-white truncate mt-1"><?= htmlspecialchars($p['name']) ?></h4>
                            <div class="text-amber-300 font-black mt-0.5">Rs. <?= number_format($p['price'], 2) ?></div>
                            <div class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($p['sizes']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

    <script>
        async function updateStatus(orderId, newStatus) {
            await fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: orderId, status: newStatus })
            });
        }
    </script>
</body>
</html>
