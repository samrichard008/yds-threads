<?php
// config.php - YDS THREADS Luxury E-Commerce Database & Core Engine
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Ensure Logo is in Assets
$assetDir = __DIR__ . '/assets';
if (!is_dir($assetDir)) {
    mkdir($assetDir, 0777, true);
}
$logoDest = $assetDir . '/logo.png';
$logoSrc = 'C:/Users/ASUS/.gemini/antigravity/brain/711e4f43-a11f-4aa1-96ce-b24be0ed6992/.user_uploaded/media_1791958896872_7f81719d.png';
if (!file_exists($logoDest) && file_exists($logoSrc)) {
    copy($logoSrc, $logoDest);
}

// 2. Database Connection with Resilient SQLite Fallback
$dbHost = '127.0.0.1';
$dbName = 'yds_threads_db';
$dbUser = 'root';
$dbPass = '';

$pdo = null;
$dbType = 'mysql';

try {
    $pdo = new PDO("mysql:host={$dbHost};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");
    $dbType = 'mysql';
} catch (PDOException $e) {
    $sqliteFile = __DIR__ . '/database.sqlite';
    $pdo = new PDO("sqlite:" . $sqliteFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $dbType = 'sqlite';
}

// 3. Schema Initialization
initYdsDatabase($pdo, $dbType);

function initYdsDatabase($pdo, $dbType) {
    if ($dbType === 'mysql') {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `products` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(191) NOT NULL,
                `category` VARCHAR(100) NOT NULL,
                `subcategory` VARCHAR(100) DEFAULT NULL,
                `price` DECIMAL(10,2) NOT NULL,
                `original_price` DECIMAL(10,2) DEFAULT NULL,
                `sizes` VARCHAR(191) DEFAULT 'S, M, L, XL',
                `colors` TEXT DEFAULT NULL,
                `description` TEXT,
                `image_url` VARCHAR(500) DEFAULT NULL,
                `badge` VARCHAR(50) DEFAULT 'Luxury Collection',
                `rating` DECIMAL(2,1) DEFAULT 4.9,
                `reviews_count` INT DEFAULT 42,
                `in_stock` TINYINT DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_code` VARCHAR(50) NOT NULL UNIQUE,
                `customer_name` VARCHAR(191) NOT NULL,
                `customer_phone1` VARCHAR(50) NOT NULL,
                `customer_phone2` VARCHAR(50) DEFAULT NULL,
                `delivery_address` TEXT NOT NULL,
                `city_district` VARCHAR(100) DEFAULT NULL,
                `product_name` VARCHAR(191) NOT NULL,
                `variant_details` VARCHAR(191) DEFAULT NULL,
                `quantity` INT DEFAULT 1,
                `subtotal` DECIMAL(10,2) NOT NULL,
                `delivery_fee` DECIMAL(10,2) DEFAULT 350.00,
                `total_amount` DECIMAL(10,2) NOT NULL,
                `payment_method` VARCHAR(50) DEFAULT 'COD',
                `bank_slip_url` VARCHAR(500) DEFAULT NULL,
                `order_status` VARCHAR(50) DEFAULT 'PENDING',
                `courier_name` VARCHAR(50) DEFAULT 'Koombiyo',
                `tracking_number` VARCHAR(100) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `customers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(191) NOT NULL,
                `phone1` VARCHAR(50) NOT NULL,
                `phone2` VARCHAR(50) DEFAULT NULL,
                `address` TEXT DEFAULT NULL,
                `city_district` VARCHAR(100) DEFAULT NULL,
                `customer_type` VARCHAR(50) DEFAULT 'CUSTOMER',
                `total_orders` INT DEFAULT 1,
                `total_spent` DECIMAL(10,2) DEFAULT 0.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } else {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                subcategory TEXT,
                price REAL NOT NULL,
                original_price REAL,
                sizes TEXT DEFAULT 'S, M, L, XL',
                colors TEXT,
                description TEXT,
                image_url TEXT,
                badge TEXT DEFAULT 'Luxury Collection',
                rating REAL DEFAULT 4.9,
                reviews_count INTEGER DEFAULT 42,
                in_stock INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_code TEXT NOT NULL UNIQUE,
                customer_name TEXT NOT NULL,
                customer_phone1 TEXT NOT NULL,
                customer_phone2 TEXT,
                delivery_address TEXT NOT NULL,
                city_district TEXT,
                product_name TEXT NOT NULL,
                variant_details TEXT,
                quantity INTEGER DEFAULT 1,
                subtotal REAL NOT NULL,
                delivery_fee REAL DEFAULT 350.00,
                total_amount REAL NOT NULL,
                payment_method TEXT DEFAULT 'COD',
                bank_slip_url TEXT,
                order_status TEXT DEFAULT 'PENDING',
                courier_name TEXT DEFAULT 'Koombiyo',
                tracking_number TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS customers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                phone1 TEXT NOT NULL,
                phone2 TEXT,
                address TEXT,
                city_district TEXT,
                customer_type TEXT DEFAULT 'CUSTOMER',
                total_orders INTEGER DEFAULT 1,
                total_spent REAL DEFAULT 0.00,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    // Seed Starter Products (10 Luxury Demo Items across 3 Niches)
    $prodCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($prodCount == 0) {
        $pStmt = $pdo->prepare("
            INSERT INTO products (name, category, subcategory, price, original_price, sizes, colors, description, image_url, badge, rating, reviews_count, in_stock) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        // --- 1. FASHION & BOUTIQUE ---
        $pStmt->execute([
            'Royal Dubai Hand-Embroidered Velvet Abaya',
            'Fashion',
            'Abaya',
            8500.00,
            11500.00,
            'S, M, L, XL, XXL',
            'Midnight Black, Royal Emerald, Deep Navy, Burgundy',
            'Crafted from premium Korean velvet with intricate metallic gold needlework and stone detailing. Includes matching chiffon Sheila.',
            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600',
            'Bestseller',
            5.0,
            86
        ]);

        $pStmt->execute([
            'Pure Georgette Designer Chudithar / Salwar Suit',
            'Fashion',
            'Chudithar',
            6900.00,
            8500.00,
            'S, M, L, XL',
            'Rose Gold, Peacock Blue, Lavender, Ruby Red',
            'Heavy embroidery zari thread work on pure georgette kurti with santoon inner and sequence bordered dupatta.',
            'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600',
            'Trending',
            4.9,
            54
        ]);

        $pStmt->execute([
            'Italian Breathable Linen Casual & Office Shirt',
            'Fashion',
            'Shirts',
            3800.00,
            4900.00,
            'M, L, XL, XXL',
            'Crisp White, Pastel Sky, Jet Black, Olive Green',
            '100% pure organic European linen fabric with mandarin collar, tailored slim fit, and pearlized buttons.',
            'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=600',
            'Hot Deal',
            4.8,
            67
        ]);

        $pStmt->execute([
            'Elegance Floral Tiered Summer Maxi Dress',
            'Fashion',
            'Dresses',
            4500.00,
            5900.00,
            'S, M, L, XL',
            'Sage Floral, Midnight Rose, Sunshine Coral',
            'Ultra-soft lightweight crepe fabric with adjustable waist tie and flowy tiered silhouette for parties and casual wear.',
            'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600',
            'Popular',
            4.9,
            38
        ]);

        // --- 2. SHOE & SNEAKER STORE ---
        $pStmt->execute([
            'Handcrafted Oxford Italian Formal Leather Shoes',
            'Shoes',
            'Formal',
            7900.00,
            10500.00,
            '40, 41, 42, 43, 44, 45',
            'Classic Black, Walnut Tan, Cognac Brown',
            'Genuine full-grain calfskin leather, Goodyear-welted construction, memory foam cushioned insole, and non-slip rubber tread.',
            'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600',
            'Luxury Choice',
            5.0,
            112
        ]);

        $pStmt->execute([
            'YDS Urban Streetwear Chunky High-Top Sneakers',
            'Shoes',
            'Sneakers',
            6200.00,
            7800.00,
            '40, 41, 42, 43, 44, 45',
            'Triple White, Shadow Black/Gold, Ice Grey',
            'Breathable mesh and vegan leather upper with responsive air-cushioned midsole and high-grip street sole.',
            'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
            'Streetwear Top',
            4.9,
            94
        ]);

        $pStmt->execute([
            'Hand-Stitched Premium Leather Driving Loafers',
            'Shoes',
            'Loafers',
            5400.00,
            6800.00,
            '40, 41, 42, 43, 44',
            'Coffee Brown, Navy Suede, Jet Black',
            'Slip-on design with horse-bit gold buckle accent, breathable leather lining, and ultra-flexible pebble rubber driving sole.',
            'https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=600',
            'Comfort Pick',
            4.8,
            49
        ]);

        // --- 3. COSMETICS & LUXURY PERFUMES ---
        $pStmt->execute([
            'Imperial Royal Amber & Cambodian Oud (100ml EDP)',
            'Perfumes',
            'Oud',
            6800.00,
            8500.00,
            '100ml Eau De Parfum',
            'Gold Metallic Bottle',
            'Rich, deep intoxicating blend of aged Cambodian agarwood, golden amber, smoky frankincense, and Bulgarian taif rose. 24h longevity.',
            'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600',
            '24H Lasting',
            5.0,
            143
        ]);

        $pStmt->execute([
            'French Velvet Vanilla & Rose Perfume Twin Gift Set',
            'Perfumes',
            'Combo',
            8200.00,
            10900.00,
            '2 x 80ml EDP Set',
            'Velvet Presentation Box',
            'Set includes 1x Velvet Vanilla Noir (Day) and 1x Midnight Rose Petals (Night). Comes in magnetic embossed luxury gift box.',
            'https://images.unsplash.com/photo-1547887537-6158d64c35b3?w=600',
            'Combo Deal',
            4.9,
            76
        ]);

        $pStmt->execute([
            '24K Gold Luxury Skin Rejuvenation Bridal Glow Kit',
            'Perfumes',
            'Cosmetics',
            9500.00,
            12900.00,
            'Full 4-Piece Bridal Kit',
            '24K Gold Serum + Day Cream + Night Elixir + Cleanser',
            'Infused with 99.9% real gold flakes, hyaluronic acid, and collagen peptides to restore skin radiance and natural glass glow.',
            'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?w=600',
            'Bridal Glow',
            5.0,
            128
        ]);

        // Seed 1 Sample Order
        $oStmt = $pdo->prepare("
            INSERT INTO orders (
                order_code, customer_name, customer_phone1, customer_phone2,
                delivery_address, city_district, product_name, variant_details,
                quantity, subtotal, delivery_fee, total_amount, payment_method,
                order_status, courier_name, tracking_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $oStmt->execute([
            'YDS-1001',
            'Fathima Zeenath',
            '0778901234',
            '0712345678',
            'No. 28/A, Marine Drive, Bambalapitiya',
            'Colombo 04',
            'Royal Dubai Hand-Embroidered Velvet Abaya',
            'Size: L | Color: Midnight Black',
            1,
            8500.00,
            350.00,
            8850.00,
            'COD',
            'CONFIRMED',
            'Koombiyo',
            'KB9283710LK'
        ]);
    }
}
?>
