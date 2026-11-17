<?php
// Deteksi halaman aktif untuk penandaan menu
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
$is_dashboard   = strpos($current_uri, 'dashboard.php') !== false;
$is_cashiers    = strpos($current_uri, '/cashiers/') !== false;
$is_products    = strpos($current_uri, '/products/') !== false;
$is_transactions= strpos($current_uri, '/transactions/') !== false;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Starbucks POS & Admin System</title>
    <link rel="stylesheet" href="/starbuck/assets/css/bootsrap.css">
    <link rel="stylesheet" href="/starbuck/assets/css/custom.css">
</head>
<body>

    <!-- NAVBAR UTAMA -->
    <header class="sb-navbar">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap gap-2">
            
            <!-- Brand Logo -->
            <a class="sb-brand" href="/starbuck/pages/dashboard.php">
                <div class="sb-brand-logo">
                    <!-- Siren Coffee Emblem SVG -->
                    <svg viewBox="0 0 24 24">
                        <path d="M18.5 3H6c-1.1 0-2 .9-2 2v5.71c0 3.83 2.95 7.18 6.78 7.29 3.96.12 7.22-3.06 7.22-7V8h.5c1.93 0 3.5-1.57 3.5-3.5S20.43 3 18.5 3zm-2 5h-11V5h11v3zm2 0h-.5V5h.5c.83 0 1.5.67 1.5 1.5S19.33 8 18.5 8zM4 19h16v2H4z"/>
                    </svg>
                </div>
                <div class="sb-brand-text">
                    STARBUCKS
                    <span class="sb-brand-sub">POS & Management</span>
                </div>
            </a>

            <!-- Navigasi Menu Langsung (Tanpa Hamburger) -->
            <ul class="sb-nav-links">
                <li class="sb-nav-item">
                    <a class="sb-nav-link <?php echo $is_dashboard ? 'active' : ''; ?>" href="/starbuck/pages/dashboard.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="sb-nav-item">
                    <a class="sb-nav-link <?php echo $is_cashiers ? 'active' : ''; ?>" href="/starbuck/pages/cashiers/list.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Kasir
                    </a>
                </li>
                <li class="sb-nav-item">
                    <a class="sb-nav-link <?php echo $is_products ? 'active' : ''; ?>" href="/starbuck/pages/products/list.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                        </svg>
                        Produk Menu
                    </a>
                </li>
                <li class="sb-nav-item">
                    <a class="sb-nav-link <?php echo $is_transactions ? 'active' : ''; ?>" href="/starbuck/pages/transactions/list.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                        </svg>
                        Transaksi POS
                    </a>
                </li>
            </ul>

            <!-- Right Controls: Live Clock & Quick POS Button -->
            <div class="sb-navbar-right">
                <div class="sb-live-clock" id="sbLiveClock">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                    <span id="sbClockText" role="timer" aria-label="Waktu saat ini dalam zona WITA">--:--:-- WITA</span>
                </div>
                <a href="/starbuck/pages/transactions/add.php" class="sb-btn sb-btn-gold sb-btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    POS Kasir
                </a>
            </div>
            
        </div>
    </header>

    <!-- Page Content Wrapper -->
    <main class="sb-wrapper">
