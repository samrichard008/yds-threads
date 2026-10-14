<?php
// index.php - YDS THREADS Luxury Online Storefront (Mobile-First 2026 Edition)
require_once __DIR__ . '/config.php';

// Fetch all active products
$stmt = $pdo->query("SELECT * FROM products WHERE in_stock = 1 ORDER BY id ASC");
$allProducts = $stmt->fetchAll();

// Group products by category
$categories = [
    'all' => '🌟 All Collections',
    'Fashion' => '👗 Fashion & Abayas',
    'Shoes' => '👟 Shoes & Sneakers',
    'Perfumes' => '✨ Oud & Cosmetics'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>YDS THREADS — Luxury Fashion, Shoes & Fragrances</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .gold-gradient {
            background: linear-gradient(135deg, #FFF3B0 0%, #D4AF37 50%, #AA7C11 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-btn {
            background: linear-gradient(135deg, #D4AF37 0%, #AA7C11 100%);
            color: #0b0f19;
            font-weight: 800;
        }
        .gold-btn:hover {
            background: linear-gradient(135deg, #E5C158 0%, #C49725 100%);
        }
        .gold-border {
            border-color: rgba(212, 175, 55, 0.35);
        }
        .gold-border:hover {
            border-color: rgba(212, 175, 55, 0.8);
        }
        .niche-pill.active {
            background: linear-gradient(135deg, #D4AF37 0%, #AA7C11 100%);
            color: #0b0f19;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        /* Hide scrollbars for slick mobile touch carousels */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .safe-bottom-pad {
            padding-bottom: max(1.25rem, env(safe-area-inset-bottom, 0px));
        }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>
<body class="bg-[#0b0f19] text-slate-100 antialiased selection:bg-[#D4AF37] selection:text-black min-h-screen flex flex-col pb-20 md:pb-0">

    <!-- Top Announcement Strip -->
    <div class="bg-gradient-to-r from-[#171e2e] via-[#241d10] to-[#171e2e] text-center py-1.5 sm:py-2 px-3 text-[11px] sm:text-xs font-semibold text-amber-200 border-b border-amber-900/30 flex items-center justify-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span class="truncate">✨ Islandwide Cash on Delivery (COD) via Koombiyo Express &bull; 2-3 Days Fast Dispatch</span>
    </div>

    <!-- Luxury Sticky Navbar -->
    <header class="sticky top-0 z-40 bg-[#0b0f19]/95 backdrop-blur-xl border-b border-slate-800/80 shadow-md">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Brand Logo (YDS THREADS) -->
                <a href="index.php" class="flex items-center gap-2 sm:gap-3 group">
                    <img src="assets/logo.png" alt="YDS Threads Logo" class="h-10 sm:h-14 w-auto object-contain transition-transform group-hover:scale-105 drop-shadow-[0_0_15px_rgba(212,175,55,0.25)]">
                </a>

                <!-- Desktop Category Nav -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-wider text-slate-300">
                    <button onclick="filterCategory('all')" class="hover:text-amber-300 transition">All Items</button>
                    <button onclick="filterCategory('Fashion')" class="hover:text-amber-300 transition">👗 Boutique & Abayas</button>
                    <button onclick="filterCategory('Shoes')" class="hover:text-amber-300 transition">👟 Footwear & Sneakers</button>
                    <button onclick="filterCategory('Perfumes')" class="hover:text-amber-300 transition">✨ Royal Oud & Fragrances</button>
                </nav>

                <!-- Actions: Cart & Admin -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="https://wa.me/94766968241?text=Hello%20YDS%20Threads!%20I%20have%20an%20inquiry%20about%20your%20collection." target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold transition">
                        <i class="fa-brands fa-whatsapp text-sm text-emerald-400"></i>
                        <span class="hidden xs:inline sm:inline">WhatsApp Help</span>
                    </a>

                    <button onclick="toggleCartDrawer()" class="relative p-2 sm:p-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-amber-300 rounded-full transition flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 shadow-inner" aria-label="Shopping Cart">
                        <i class="fa-solid fa-bag-shopping text-sm sm:text-base"></i>
                        <span id="cartCountBadge" class="absolute -top-1 -right-1 bg-amber-500 text-slate-950 text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow">0</span>
                    </button>

                    <a href="admin/" title="Merchant Admin Portal" class="p-2 sm:p-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white rounded-full transition flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 shadow-inner">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Banner Stage (Touch-optimized) -->
    <section class="relative pt-6 pb-8 sm:pt-12 sm:pb-14 px-4 overflow-hidden border-b border-slate-800/80 bg-gradient-to-b from-[#111625] via-[#0b0f19] to-[#0b0f19]">
        <div class="max-w-5xl mx-auto text-center relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold uppercase tracking-widest bg-amber-950/60 text-amber-300 border border-amber-600/40 mb-3 sm:mb-4">
                <i class="fa-solid fa-crown text-[9px]"></i> SIGNATURE LUXURY COLLECTION 2026
            </span>
            <h1 class="font-cinzel text-2xl sm:text-4xl lg:text-6xl font-black text-white tracking-tight leading-tight">
                Designed for <span class="gold-gradient">Elegance</span> &amp; Prestige
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl mx-auto mt-2.5 sm:mt-4 leading-relaxed font-normal">
                Discover bespoke Dubai-style Abayas, designer Chudithars, handcrafted Italian leather shoes, high-street sneakers, and aged Cambodian Oud perfumes.
            </p>

            <!-- Touch-swipeable Feature Badges -->
            <div class="flex items-center justify-start sm:justify-center gap-2 sm:gap-4 overflow-x-auto no-scrollbar mt-5 sm:mt-8 pb-1 text-[11px] sm:text-xs font-semibold text-slate-300">
                <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1.5 rounded-xl whitespace-nowrap shadow-sm">
                    <i class="fa-solid fa-hand-holding-dollar text-amber-400 text-xs"></i>
                    <span>Cash on Delivery (COD)</span>
                </div>
                <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1.5 rounded-xl whitespace-nowrap shadow-sm">
                    <i class="fa-solid fa-truck-fast text-emerald-400 text-xs"></i>
                    <span>Koombiyo Courier (2-3 Days)</span>
                </div>
                <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1.5 rounded-xl whitespace-nowrap shadow-sm">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i>
                    <span>1-Click WhatsApp Ordering</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Niche Filter Tabs -->
    <section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-4 sm:pt-6">
        <div class="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto no-scrollbar pb-3">
            <?php foreach ($categories as $catKey => $catLabel): ?>
                <button onclick="filterCategory('<?= $catKey ?>')" id="pill-<?= $catKey ?>" class="niche-pill <?= $catKey === 'all' ? 'active' : '' ?> px-3.5 sm:px-6 py-2 rounded-full text-xs font-bold whitespace-nowrap bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 transition">
                    <?= $catLabel ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Catalog Control Bar (Item count & View mode switch) -->
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-2 pb-1 flex items-center justify-between text-xs text-slate-400">
        <div>
            <span id="productCountLabel" class="font-bold text-slate-300"><?= count($allProducts) ?> Luxury Products</span>
        </div>
        <div class="flex items-center gap-1 bg-slate-900/90 border border-slate-800 rounded-xl p-0.5">
            <button id="viewGridBtn" onclick="setViewMode('grid')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition text-amber-300 bg-amber-500/20" title="2-Column Grid View">
                <i class="fa-solid fa-table-cells-large"></i> <span class="hidden sm:inline ml-1">Grid</span>
            </button>
            <button id="viewListBtn" onclick="setViewMode('list')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition text-slate-400 hover:text-white" title="List View">
                <i class="fa-solid fa-list"></i> <span class="hidden sm:inline ml-1">Card</span>
            </button>
        </div>
    </div>

    <!-- Main Products Grid -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 flex-1">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6" id="productsGrid">
            <?php foreach ($allProducts as $p): ?>
                <?php
                    $sizesList = array_map('trim', explode(',', $p['sizes']));
                    $colorsList = array_map('trim', explode(',', $p['colors']));
                    $discountPct = $p['original_price'] > $p['price'] ? round((($p['original_price'] - $p['price']) / $p['original_price']) * 100) : 0;
                ?>
                <div class="product-card bg-[#111726] rounded-xl sm:rounded-2xl border gold-border p-2.5 sm:p-4 flex flex-col justify-between shadow-lg transition-all duration-300 hover:shadow-[0_10px_25px_rgba(212,175,55,0.15)]" data-category="<?= htmlspecialchars($p['category']) ?>" data-id="<?= $p['id'] ?>">
                    
                    <div>
                        <!-- Product Image & Badges -->
                        <div class="aspect-square sm:h-56 w-full bg-slate-900 rounded-lg sm:rounded-xl overflow-hidden relative mb-2.5 sm:mb-4">
                            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                            
                            <span class="absolute top-1.5 left-1.5 sm:top-2.5 sm:left-2.5 px-1.5 sm:px-2.5 py-0.5 rounded text-[8px] sm:text-[10px] font-black uppercase tracking-wider bg-black/85 text-amber-300 border border-amber-500/30 backdrop-blur-xs">
                                <?= htmlspecialchars($p['badge'] ?: $p['category']) ?>
                            </span>

                            <?php if ($discountPct > 0): ?>
                                <span class="absolute top-1.5 right-1.5 sm:top-2.5 sm:right-2.5 px-1.5 sm:px-2.5 py-0.5 rounded text-[8px] sm:text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-md">
                                    -<?= $discountPct ?>%
                                </span>
                            <?php endif; ?>

                            <div class="absolute bottom-1.5 left-1.5 sm:bottom-2.5 sm:left-2.5 px-1.5 py-0.5 rounded bg-black/75 backdrop-blur-xs text-[9px] sm:text-[10px] text-amber-400 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-star text-[8px]"></i>
                                <span><?= $p['rating'] ?></span>
                                <span class="text-slate-400 hidden xs:inline">(<?= $p['reviews_count'] ?>)</span>
                            </div>
                        </div>

                        <!-- Category & Title -->
                        <div class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-400/80 mb-0.5">
                            <?= htmlspecialchars($p['subcategory'] ?: $p['category']) ?>
                        </div>
                        <h3 class="font-bold sm:font-extrabold text-white text-xs sm:text-sm line-clamp-2 leading-snug mb-1.5 sm:mb-2 min-h-[32px] sm:min-h-[40px]">
                            <?= htmlspecialchars($p['name']) ?>
                        </h3>

                        <!-- Price -->
                        <div class="flex items-baseline gap-1.5 sm:gap-2 mb-2 sm:mb-3">
                            <span class="text-sm sm:text-lg font-black text-amber-300">Rs. <?= number_format($p['price'], 2) ?></span>
                            <?php if ($p['original_price']): ?>
                                <span class="text-[10px] sm:text-xs text-slate-500 line-through">Rs. <?= number_format($p['original_price'], 2) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Color Swatches -->
                        <div class="mb-2 sm:mb-3">
                            <div class="text-[9px] sm:text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider truncate">
                                Color: <span class="text-amber-300 selected-color-label font-semibold"><?= htmlspecialchars($colorsList[0]) ?></span>
                            </div>
                            <div class="flex flex-wrap gap-1 sm:gap-1.5 color-swatches">
                                <?php foreach ($colorsList as $cIdx => $cName): ?>
                                    <button onclick="selectColor(this, '<?= htmlspecialchars($cName) ?>')" class="color-btn <?= $cIdx === 0 ? 'ring-2 ring-amber-400 text-amber-200' : 'text-slate-400' ?> px-1.5 sm:px-2 py-0.5 bg-slate-900 border border-slate-700/80 rounded text-[9px] sm:text-[10px] font-semibold transition">
                                        <?= htmlspecialchars($cName) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Size Swatches -->
                        <div class="mb-2.5 sm:mb-4">
                            <div class="text-[9px] sm:text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider truncate">
                                Size: <span class="text-amber-300 selected-size-label font-semibold"><?= htmlspecialchars($sizesList[0]) ?></span>
                            </div>
                            <div class="flex flex-wrap gap-1 sm:gap-1.5 size-swatches">
                                <?php foreach ($sizesList as $sIdx => $sName): ?>
                                    <button onclick="selectSize(this, '<?= htmlspecialchars($sName) ?>')" class="size-btn <?= $sIdx === 0 ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-900 text-slate-300' ?> px-2 sm:px-2.5 py-0.5 sm:py-1 border border-slate-700/80 rounded text-[9px] sm:text-[11px] font-bold transition">
                                        <?= htmlspecialchars($sName) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-1.5 sm:space-y-2 pt-2 border-t border-slate-800">
                        <div class="grid grid-cols-2 gap-1.5 sm:gap-2">
                            <!-- WhatsApp Order -->
                            <button onclick="orderViaWhatsApp(this)" 
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= $p['price'] ?>"
                                class="py-2 sm:py-2.5 px-1 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] sm:text-[11px] rounded-lg sm:rounded-xl transition flex items-center justify-center gap-1 shadow-sm">
                                <i class="fa-brands fa-whatsapp text-xs sm:text-sm"></i>
                                <span>WhatsApp</span>
                            </button>

                            <!-- Add to Bag -->
                            <button onclick="addToCart(this)" 
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= $p['price'] ?>"
                                data-image="<?= htmlspecialchars($p['image_url']) ?>"
                                class="py-2 sm:py-2.5 px-1 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700/80 font-bold text-[10px] sm:text-[11px] rounded-lg sm:rounded-xl transition flex items-center justify-center gap-1">
                                <i class="fa-solid fa-cart-plus text-xs"></i>
                                <span>Add Bag</span>
                            </button>
                        </div>

                        <!-- 1-Click Fast Checkout (COD) -->
                        <button onclick="openFastCheckout(this)"
                            data-id="<?= $p['id'] ?>"
                            data-name="<?= htmlspecialchars($p['name']) ?>"
                            data-price="<?= $p['price'] ?>"
                            data-image="<?= htmlspecialchars($p['image_url']) ?>"
                            class="w-full py-2 sm:py-2.5 gold-btn rounded-lg sm:rounded-xl text-[10px] sm:text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5 shadow-md">
                            <i class="fa-solid fa-bolt text-xs"></i>
                            <span>Buy Now (COD)</span>
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Slide-Over Cart Drawer (Mobile Touch Friendly) -->
    <div id="cartDrawer" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs hidden justify-end transition-opacity">
        <div class="bg-[#111726] w-full max-w-md h-full shadow-2xl border-l border-slate-800 flex flex-col justify-between">
            <div class="p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bag-shopping text-amber-400"></i>
                    <h3 class="font-black text-white text-base">Your Shopping Bag</h3>
                </div>
                <button onclick="toggleCartDrawer()" class="w-8 h-8 rounded-full bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 sm:p-5">
                <div id="cartItemsList" class="divide-y divide-slate-800/80 space-y-3"></div>
            </div>

            <div class="p-4 sm:p-5 border-t border-slate-800 bg-[#0d121f] space-y-2.5 safe-bottom-pad">
                <div class="flex justify-between text-xs text-slate-400 font-semibold">
                    <span>Subtotal:</span>
                    <span id="cartSubtotalText" class="text-white font-bold">Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-xs text-slate-400 font-semibold">
                    <span>Islandwide Delivery (Koombiyo):</span>
                    <span class="text-emerald-400 font-bold">Rs. 350.00</span>
                </div>
                <div class="flex justify-between text-sm text-white font-black pt-2 border-t border-slate-800/80">
                    <span>Total (COD):</span>
                    <span id="cartTotalText" class="text-amber-300 font-black text-base">Rs. 350.00</span>
                </div>
                <button onclick="checkoutCart()" class="w-full py-3.5 gold-btn rounded-xl text-xs uppercase tracking-wider shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-shield-halved"></i> Proceed to Fast Checkout
                </button>
            </div>
        </div>
    </div>

    <!-- Fast 1-Click Checkout Modal -->
    <div id="checkoutModal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-[#111726] border sm:border gold-border rounded-t-3xl sm:rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl text-slate-200 max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-amber-400 text-lg">⚡</span>
                    <h3 class="font-black text-white text-base">Fast 1-Click Checkout (COD)</h3>
                </div>
                <button onclick="closeCheckoutModal()" class="w-8 h-8 rounded-full bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <div class="overflow-y-auto flex-1 pr-1 py-3 space-y-3">
                <div class="bg-slate-900/90 rounded-2xl p-3 border border-slate-800 flex items-center gap-3">
                    <img id="co_item_image" src="" class="w-14 h-14 rounded-xl object-cover bg-slate-800 border border-slate-700 shrink-0">
                    <div class="flex-1 min-w-0 text-xs">
                        <h4 id="co_item_name" class="font-extrabold text-white truncate"></h4>
                        <div id="co_item_variant" class="text-slate-400 text-[11px] mt-0.5 truncate"></div>
                        <div class="text-amber-300 font-black mt-1" id="co_item_price"></div>
                    </div>
                </div>

                <form id="fastCheckoutForm" onsubmit="submitFastOrder(event)" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Your Full Name *</label>
                        <input type="text" id="co_name" required placeholder="e.g. Kasun Perera / Fathima" class="w-full text-xs p-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Primary Mobile (WhatsApp) *</label>
                            <input type="tel" id="co_phone1" required placeholder="077xxxxxxx" class="w-full text-xs p-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Backup Phone (Optional)</label>
                            <input type="tel" id="co_phone2" placeholder="071xxxxxxx" class="w-full text-xs p-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Delivery Street Address *</label>
                        <input type="text" id="co_address" required placeholder="House No, Street name, Landmark..." class="w-full text-xs p-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">City / District *</label>
                            <input type="text" id="co_city" required placeholder="Colombo / Kandy / Galle" class="w-full text-xs p-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Payment Method</label>
                            <select id="co_payment" onchange="togglePaymentInstructions(this.value)" class="w-full text-xs p-3 rounded-xl bg-slate-900 border border-slate-700 text-amber-300 font-bold">
                                <option value="COD">🟢 Cash on Delivery</option>
                                <option value="BANK_TRANSFER">💳 Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div id="bankNoteBox" class="hidden p-3 bg-amber-950/40 rounded-xl border border-amber-600/30 text-[11px] text-amber-200 space-y-1">
                        <strong>Bank Account Details:</strong>
                        <div>Bank: Commercial Bank of Ceylon</div>
                        <div>Account: YDS Threads (Pvt) Ltd &bull; A/C: 1000847291</div>
                    </div>

                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Total with Delivery (Rs. 350):</span>
                        <strong class="text-amber-300 font-black text-sm" id="co_total_badge">Rs. 0.00</strong>
                    </div>

                    <button type="submit" id="co_submit_btn" class="w-full py-3.5 gold-btn rounded-xl text-xs uppercase tracking-wider shadow-xl flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock text-xs"></i> Place Order (Cash on Delivery)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Order Confirmed Celebration Screen -->
    <div id="successModal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-[#111726] border border-emerald-500/50 rounded-3xl max-w-md w-full p-6 text-center text-slate-200 shadow-2xl">
            <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 rounded-full flex items-center justify-center mx-auto text-2xl font-black mb-4">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 class="font-cinzel text-xl font-black text-white">Order Confirmed!</h3>
            <p class="text-xs text-slate-400 mt-1">Thank you for choosing YDS THREADS.</p>

            <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800 my-4 text-xs text-left space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Order ID:</span>
                    <strong class="text-amber-300 font-mono text-sm" id="succ_order_code"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Total to pay on Delivery:</span>
                    <strong class="text-white font-bold" id="succ_total"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Courier Waybill:</span>
                    <span class="text-emerald-400 font-mono" id="succ_tracking"></span>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 mb-5">
                Your order will be dispatched via Koombiyo within 2-3 working days. Cash will be collected at your doorstep.
            </p>

            <a id="succ_wa_link" href="#" target="_blank" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-2 mb-2">
                <i class="fa-brands fa-whatsapp text-sm"></i> Track on WhatsApp
            </a>
            <button onclick="location.reload()" class="w-full py-2.5 bg-slate-800 text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-700 transition">
                Continue Shopping
            </button>
        </div>
    </div>

    <!-- Mobile Bottom App Navigation Bar (Synchronized with Cart & iOS Safe Area) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#0b0f19]/95 backdrop-blur-xl border-t border-slate-800/80 py-2 px-4 flex justify-between items-center text-[10px] text-slate-400 font-bold shadow-2xl" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom, 0px));">
        <button onclick="window.scrollTo({top:0, behavior:'smooth'})" class="flex flex-col items-center gap-1 hover:text-amber-300">
            <i class="fa-solid fa-house text-sm"></i>
            <span>Home</span>
        </button>
        <button onclick="filterCategory('Fashion')" class="flex flex-col items-center gap-1 hover:text-amber-300">
            <i class="fa-solid fa-shirt text-sm"></i>
            <span>Fashion</span>
        </button>
        <button onclick="filterCategory('Shoes')" class="flex flex-col items-center gap-1 hover:text-amber-300">
            <i class="fa-solid fa-shoe-prints text-sm"></i>
            <span>Shoes</span>
        </button>
        <button onclick="toggleCartDrawer()" class="flex flex-col items-center gap-1 hover:text-amber-300 relative">
            <i class="fa-solid fa-bag-shopping text-sm text-amber-400"></i>
            <span>Cart</span>
            <span id="mobileCartCountBadge" class="absolute -top-1 -right-2 bg-amber-500 text-slate-950 text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
        </button>
        <a href="https://wa.me/94766968241" target="_blank" class="flex flex-col items-center gap-1 text-emerald-400">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>WhatsApp</span>
        </a>
    </nav>

    <!-- Footer -->
    <footer class="bg-black border-t border-slate-800 text-slate-400 py-8 sm:py-10 px-4 text-center text-xs">
        <div class="max-w-7xl mx-auto flex flex-col items-center">
            <img src="assets/logo.png" class="h-10 sm:h-12 w-auto mb-3 opacity-90">
            <p class="text-slate-500 max-w-md">YDS THREADS — Premium Fashion, Handcrafted Footwear &amp; Luxury Fragrances. Islandwide Sri Lanka Delivery.</p>
            <div class="mt-4 pt-4 border-t border-slate-900 text-[11px] text-slate-600">
                &copy; <?= date('Y') ?> YDS THREADS. Built for Client E-Commerce Excellence.
            </div>
        </div>
    </footer>

    <!-- Frontend Store Controller JS -->
    <script>
        let currentFastProduct = null;
        let viewMode = 'grid'; // 'grid' (2-col mobile) or 'list' (1-col card)
        let cart = JSON.parse(localStorage.getItem('yds_cart') || '[]');

        function updateCartBadge() {
            const count = cart.reduce((acc, item) => acc + item.qty, 0);
            const badge1 = document.getElementById('cartCountBadge');
            const badge2 = document.getElementById('mobileCartCountBadge');
            if (badge1) badge1.innerText = count;
            if (badge2) badge2.innerText = count;
        }
        updateCartBadge();

        function setViewMode(mode) {
            viewMode = mode;
            const grid = document.getElementById('productsGrid');
            const btnGrid = document.getElementById('viewGridBtn');
            const btnList = document.getElementById('viewListBtn');

            if (mode === 'grid') {
                grid.className = 'grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6';
                btnGrid.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition text-amber-300 bg-amber-500/20';
                btnList.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition text-slate-400 hover:text-white';
            } else {
                grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6';
                btnList.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition text-amber-300 bg-amber-500/20';
                btnGrid.className = 'px-2.5 py-1 rounded-lg text-xs font-bold transition text-slate-400 hover:text-white';
            }
        }

        // 1. Niche Filtering
        function filterCategory(cat) {
            document.querySelectorAll('.niche-pill').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('pill-' + cat);
            if (activeBtn) activeBtn.classList.add('active');

            let visibleCount = 0;
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                if (cat === 'all' || card.getAttribute('data-category') === cat) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('productCountLabel').innerText = `${visibleCount} Items in Collection`;
        }

        // 2. Swatch Selectors
        function selectColor(btn, colorName) {
            const parent = btn.closest('.product-card');
            parent.querySelectorAll('.color-btn').forEach(b => {
                b.classList.remove('ring-2', 'ring-amber-400', 'text-amber-200');
                b.classList.add('text-slate-400');
            });
            btn.classList.add('ring-2', 'ring-amber-400', 'text-amber-200');
            btn.classList.remove('text-slate-400');
            parent.querySelector('.selected-color-label').innerText = colorName;
        }

        function selectSize(btn, sizeName) {
            const parent = btn.closest('.product-card');
            parent.querySelectorAll('.size-btn').forEach(b => {
                b.classList.remove('bg-amber-500', 'text-slate-950', 'font-black');
                b.classList.add('bg-slate-900', 'text-slate-300');
            });
            btn.classList.add('bg-amber-500', 'text-slate-950', 'font-black');
            btn.classList.remove('bg-slate-900', 'text-slate-300');
            parent.querySelector('.selected-size-label').innerText = sizeName;
        }

        // 3. 1-Click WhatsApp Quick Order
        function orderViaWhatsApp(btn) {
            const card = btn.closest('.product-card');
            const name = btn.getAttribute('data-name');
            const price = parseFloat(btn.getAttribute('data-price')).toLocaleString('en-US', {minimumFractionDigits: 2});
            const color = card.querySelector('.selected-color-label').innerText;
            const size = card.querySelector('.selected-size-label').innerText;

            const msg = `Hello YDS Threads! 👑%0A%0AI would like to order:%0A👗 *${encodeURIComponent(name)}*%0A🏷️ *Size / Vol:* ${encodeURIComponent(size)}%0A🎨 *Color / Variant:* ${encodeURIComponent(color)}%0A💵 *Price:* Rs. ${price}%0A🚚 *Payment:* Cash on Delivery (COD)%0A%0APlease let me know the delivery details!`;

            window.open(`https://wa.me/94766968241?text=${msg}`, '_blank');
        }

        // 4. Fast 1-Click Checkout Modal
        function openFastCheckout(btn) {
            let id, name, price, image, color, size;

            if (btn === null) {
                // Triggered from Cart Bundle
                id = currentFastProduct.id;
                name = currentFastProduct.name;
                price = currentFastProduct.price;
                image = currentFastProduct.image;
                color = currentFastProduct.color;
                size = currentFastProduct.size;
            } else {
                const card = btn.closest('.product-card');
                id = btn.getAttribute('data-id');
                name = btn.getAttribute('data-name');
                price = parseFloat(btn.getAttribute('data-price'));
                image = btn.getAttribute('data-image');
                color = card.querySelector('.selected-color-label').innerText;
                size = card.querySelector('.selected-size-label').innerText;

                currentFastProduct = {
                    id, name, price, image, color, size,
                    total: price + 350.00
                };
            }

            document.getElementById('co_item_image').src = image;
            document.getElementById('co_item_name').innerText = name;
            document.getElementById('co_item_variant').innerText = `Size: ${size} | Color: ${color}`;
            document.getElementById('co_item_price').innerText = `Rs. ${price.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('co_total_badge').innerText = `Rs. ${(price + 350).toLocaleString('en-US', {minimumFractionDigits: 2})}`;

            document.getElementById('checkoutModal').classList.remove('hidden');
            document.getElementById('checkoutModal').classList.add('flex');
        }

        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('hidden');
            document.getElementById('checkoutModal').classList.remove('flex');
        }

        function togglePaymentInstructions(m) {
            const b = document.getElementById('bankNoteBox');
            if (m === 'BANK_TRANSFER') b.classList.remove('hidden');
            else b.classList.add('hidden');
        }

        async function submitFastOrder(e) {
            e.preventDefault();
            const btn = document.getElementById('co_submit_btn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Order...';
            btn.disabled = true;

            const name = document.getElementById('co_name').value.trim();
            const phone1 = document.getElementById('co_phone1').value.trim();
            const phone2 = document.getElementById('co_phone2').value.trim();
            const address = document.getElementById('co_address').value.trim();
            const city = document.getElementById('co_city').value.trim();
            const payment = document.getElementById('co_payment').value;

            try {
                const res = await fetch('api/place_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name, phone1, phone2, address, city,
                        product_id: currentFastProduct.id,
                        product_name: currentFastProduct.name,
                        variant: `Size: ${currentFastProduct.size} | Color: ${currentFastProduct.color}`,
                        subtotal: currentFastProduct.price,
                        payment_method: payment
                    })
                });

                const data = await res.json();
                if (data.status === 'success') {
                    closeCheckoutModal();
                    document.getElementById('succ_order_code').innerText = '#' + data.order_code;
                    document.getElementById('succ_total').innerText = 'Rs. ' + Number(data.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
                    document.getElementById('succ_tracking').innerText = data.tracking_number;
                    
                    if (data.wa_link) {
                        document.getElementById('succ_wa_link').href = data.wa_link;
                    }

                    document.getElementById('successModal').classList.remove('hidden');
                    document.getElementById('successModal').classList.add('flex');
                } else {
                    alert(data.message || 'Error processing order');
                    btn.innerHTML = '<i class="fa-solid fa-lock text-xs"></i> Place Order (Cash on Delivery)';
                    btn.disabled = false;
                }
            } catch (err) {
                alert('Connection error. Please try again.');
                btn.innerHTML = '<i class="fa-solid fa-lock text-xs"></i> Place Order (Cash on Delivery)';
                btn.disabled = false;
            }
        }

        // 5. Cart Operations
        function toggleCartDrawer() {
            const d = document.getElementById('cartDrawer');
            if (d.classList.contains('hidden')) {
                renderCart();
                d.classList.remove('hidden');
                d.classList.add('flex');
            } else {
                d.classList.add('hidden');
                d.classList.remove('flex');
            }
        }

        function addToCart(btn) {
            const card = btn.closest('.product-card');
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const price = parseFloat(btn.getAttribute('data-price'));
            const image = btn.getAttribute('data-image');
            const color = card.querySelector('.selected-color-label').innerText;
            const size = card.querySelector('.selected-size-label').innerText;

            const existing = cart.find(i => i.id === id && i.color === color && i.size === size);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ id, name, price, image, color, size, qty: 1 });
            }

            localStorage.setItem('yds_cart', JSON.stringify(cart));
            updateCartBadge();

            btn.innerHTML = '✅ Added!';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i> Add Bag';
            }, 1000);
        }

        function renderCart() {
            const list = document.getElementById('cartItemsList');
            if (cart.length === 0) {
                list.innerHTML = `<div class="py-10 text-center text-slate-500 text-xs">Your bag is empty. Explore our signature collection!</div>`;
                document.getElementById('cartSubtotalText').innerText = 'Rs. 0.00';
                document.getElementById('cartTotalText').innerText = 'Rs. 350.00';
                return;
            }

            let subtotal = 0;
            let html = '';
            cart.forEach((i, idx) => {
                subtotal += (i.price * i.qty);
                html += `
                    <div class="py-3 flex items-center justify-between text-xs gap-3">
                        <img src="${i.image}" class="w-12 h-12 rounded-xl object-cover bg-slate-900 border border-slate-800 shrink-0">
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-white truncate">${i.name}</h5>
                            <div class="text-[10px] text-slate-400 truncate">${i.size} &bull; ${i.color}</div>
                            <div class="text-amber-300 font-bold mt-0.5">Rs. ${i.price.toLocaleString('en-US', {minimumFractionDigits: 2})} x ${i.qty}</div>
                        </div>
                        <button onclick="removeCartItem(${idx})" class="text-rose-400 hover:text-rose-300 p-2"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                `;
            });
            list.innerHTML = html;
            document.getElementById('cartSubtotalText').innerText = 'Rs. ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('cartTotalText').innerText = 'Rs. ' + (subtotal + 350).toLocaleString('en-US', {minimumFractionDigits: 2});
        }

        function removeCartItem(idx) {
            cart.splice(idx, 1);
            localStorage.setItem('yds_cart', JSON.stringify(cart));
            updateCartBadge();
            renderCart();
        }

        function checkoutCart() {
            if (cart.length === 0) return alert('Your cart is empty!');
            toggleCartDrawer();
            const first = cart[0];
            const subtotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
            const bundleName = cart.map(i => `${i.name} (${i.size}/${i.color}) x${i.qty}`).join(', ');

            currentFastProduct = {
                id: 'cart_bundle',
                name: bundleName,
                price: subtotal,
                image: first.image,
                color: 'Cart Bundle',
                size: `${cart.length} item(s)`,
                total: subtotal + 350.00
            };

            openFastCheckout(null);
        }
    </script>
</body>
</html>
