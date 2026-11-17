<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

// 1. Total Kasir
$total_kasir = 0;
$res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kasir");
if ($res) {
    $r = mysqli_fetch_assoc($res);
    $total_kasir = (int) $r['total'];
}

// 2. Total Produk
$total_produk = 0;
$res = mysqli_query($koneksi, "SHOW TABLES LIKE 'produk'");
if ($res && mysqli_num_rows($res) > 0) {
    $rp = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
    if ($rp) {
        $r = mysqli_fetch_assoc($rp);
        $total_produk = (int) $r['total'];
    }
}

// 3. Total Transaksi & Pendapatan
$total_transaksi = 0;
$total_pendapatan = 0;
$res = mysqli_query($koneksi, "SHOW TABLES LIKE 'transaksi'");
if ($res && mysqli_num_rows($res) > 0) {
    $rt = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi");
    if ($rt) {
        $r = mysqli_fetch_assoc($rt);
        $total_transaksi = (int) $r['total'];
    }
    $rp = mysqli_query($koneksi, "SELECT SUM(total) AS total FROM transaksi");
    if ($rp) {
        $r = mysqli_fetch_assoc($rp);
        $total_pendapatan = (float) ($r['total'] ?? 0);
    }
}

// 4. Data Transaksi Terbaru
$transaksi_terbaru = [];
if ($res && mysqli_num_rows($res) > 0) {
    $q_trx = "SELECT * FROM transaksi ORDER BY id_transaksi DESC LIMIT 5";
    $res_trx = mysqli_query($koneksi, $q_trx);
    if ($res_trx) {
        while ($t = mysqli_fetch_assoc($res_trx)) {
            $transaksi_terbaru[] = $t;
        }
    }
}

// 5. Data Kasir Terbaru
$kasir_terbaru = [];
$res_k = mysqli_query($koneksi, "SELECT * FROM kasir ORDER BY id_kasir DESC LIMIT 5");
if ($res_k) {
    while ($k = mysqli_fetch_assoc($res_k)) {
        $kasir_terbaru[] = $k;
    }
}
?>

<div class="container">

    <!-- PAGE HEADER & GREETING -->
    <div class="sb-page-header">
        <div>
            <div class="sb-breadcrumb">
                <span>Admin Portal</span> &bull; <span>Overview</span>
            </div>
            <h1 class="sb-page-title">
                Dashboard Kasir
            </h1>
            <p class="sb-page-subtitle">Ringkasan operasional dan aktivitas penjualan Starbucks Coffee</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/starbuck/pages/transactions/add.php" class="sb-btn sb-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
                Buat Transaksi Baru
            </a>
        </div>
    </div>

    <!-- 4 KPI STAT METRIC CARDS -->
    <div class="row g-3 mb-4">
        <!-- Kasir -->
        <div class="col-6 col-lg-3">
            <div class="sb-stat-card">
                <div>
                    <div class="sb-stat-top">
                        <span class="sb-stat-label">Total Kasir</span>
                        <div class="sb-stat-icon-wrap" style="background:#ecfdf5; color:#059669;">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="sb-stat-value"><?php echo number_format($total_kasir); ?></div>
                    <div class="sb-stat-meta">Staf Kasir Terdaftar</div>
                </div>
                <a href="/starbuck/pages/cashiers/list.php" class="sb-stat-link">
                    Kelola Kasir &rarr;
                </a>
            </div>
        </div>

        <!-- Produk Menu -->
        <div class="col-6 col-lg-3">
            <div class="sb-stat-card">
                <div>
                    <div class="sb-stat-top">
                        <span class="sb-stat-label">Produk Menu</span>
                        <div class="sb-stat-icon-wrap" style="background:#eff6ff; color:#2563eb;">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="sb-stat-value"><?php echo number_format($total_produk); ?></div>
                    <div class="sb-stat-meta">Item Menu Aktif</div>
                </div>
                <a href="/starbuck/pages/products/list.php" class="sb-stat-link">
                    Lihat Menu &rarr;
                </a>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-6 col-lg-3">
            <div class="sb-stat-card">
                <div>
                    <div class="sb-stat-top">
                        <span class="sb-stat-label">Transaksi</span>
                        <div class="sb-stat-icon-wrap" style="background:#fffbeb; color:#d97706;">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="sb-stat-value"><?php echo number_format($total_transaksi); ?></div>
                    <div class="sb-stat-meta">Order Selesai</div>
                </div>
                <a href="/starbuck/pages/transactions/list.php" class="sb-stat-link">
                    Riwayat Transaksi &rarr;
                </a>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-6 col-lg-3">
            <div class="sb-stat-card">
                <div>
                    <div class="sb-stat-top">
                        <span class="sb-stat-label">Total Omset</span>
                        <div class="sb-stat-icon-wrap" style="background:#fdf2f2; color:#dc2626;">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="sb-stat-value" style="font-size:1.45rem;">Rp
                        <?php echo number_format($total_pendapatan, 0, ',', '.'); ?>
                    </div>
                    <div class="sb-stat-meta">Akumulasi Penjualan</div>
                </div>
                <a href="/starbuck/pages/transactions/list.php" class="sb-stat-link">
                    Rincian Penjualan &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- PINTASAN CEPAT (ACTION TILES) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="/starbuck/pages/transactions/add.php" class="sb-action-tile">
                <div class="sb-tile-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                    </svg>
                </div>
                <div class="sb-tile-title">Transaksi POS</div>
                <div class="sb-tile-sub">Input order kasir baru</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/starbuck/pages/products/add.php" class="sb-action-tile">
                <div class="sb-tile-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z" />
                    </svg>
                </div>
                <div class="sb-tile-title">Tambah Produk</div>
                <div class="sb-tile-sub">Input varian menu baru</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/starbuck/pages/cashiers/add.php" class="sb-action-tile">
                <div class="sb-tile-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <div class="sb-tile-title">Tambah Kasir</div>
                <div class="sb-tile-sub">Daftarkan operator kasir</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/starbuck/pages/transactions/list.php" class="sb-action-tile">
                <div class="sb-tile-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm0-4H7V7h10v2z" />
                    </svg>
                </div>
                <div class="sb-tile-title">Laporan Penjualan</div>
                <div class="sb-tile-sub">Riwayat struk transaksi</div>
            </a>
        </div>
    </div>

    <!-- DUAL WIDGET SECTION: TRANSAKSI TERBARU & KASIR AKTIF -->
    <div class="row g-4">

        <!-- Left: Transaksi Terkini -->
        <div class="col-lg-8">
            <div class="sb-card">
                <div class="sb-card-header">
                    <h2 class="sb-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                        </svg>
                        Transaksi Terbaru
                    </h2>
                    <a href="/starbuck/pages/transactions/list.php" class="sb-btn sb-btn-secondary sb-btn-sm">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="sb-table-wrap">
                    <table class="sb-table">
                        <thead>
                            <tr>
                                <th>ID Order</th>
                                <th>Item / Menu</th>
                                <th>Tanggal & Waktu</th>
                                <th class="text-end">Total Akhir</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transaksi_terbaru)): ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="sb-empty-state">
                                            <div class="sb-empty-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm0-4H7V7h10v2z" />
                                                </svg>
                                            </div>
                                            <div class="sb-empty-title">Belum Ada Transaksi</div>
                                            <p class="sb-empty-desc">Mulai layani pelanggan dan buat transaksi pertama
                                                melalui modul POS.</p>
                                            <a href="/starbuck/pages/transactions/add.php"
                                                class="sb-btn sb-btn-primary sb-btn-sm">+ Buat Transaksi</a>
                                        </div>
                                        <div class="sb-empty-title">Belum Ada Transaksi</div>
                                        <p class="sb-empty-desc">Mulai layani pelanggan dan buat transaksi pertama melalui
                                            modul POS.</p>
                                        <a href="/starbuck/pages/transactions/add.php"
                                            class="sb-btn sb-btn-primary sb-btn-sm">+ Buat Transaksi</a>
                    </div>
                    </td>
                    </tr>
                <?php else:
                                foreach ($transaksi_terbaru as $t): ?>
                        <tr>
                            <td>
                                <span class="fw-bold"
                                    style="color:var(--sb-primary);">#TRX-<?php echo str_pad($t['id_transaksi'], 3, '0', STR_PAD_LEFT); ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($t['nama_produk'] ?? 'Pesanan Kopi'); ?>
                                </div>
                                <?php if (!empty($t['jumlah'])): ?>
                                    <span class="text-muted" style="font-size:0.75rem;">Qty: <?php echo $t['jumlah']; ?> item</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo $t['tanggal'] ?? '-'; ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?php echo $t['waktu'] ?? '-'; ?> WITA</div>
                            </td>
                            <td class="text-end fw-bold" style="color:var(--sb-primary);">
                                Rp <?php echo number_format($t['total'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <span class="sb-badge sb-badge-green">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" />
                                    </svg>
                                    Lunas
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Daftar Kasir Bertugas -->
    <div class="col-lg-4">
        <div class="sb-card">
            <div class="sb-card-header">
                <h2 class="sb-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    Operator Kasir
                </h2>
                <a href="/starbuck/pages/cashiers/add.php" class="sb-btn sb-btn-secondary sb-btn-sm">+ Kasir</a>
            </div>
            <div style="background:#ffffff;">
                <?php if (empty($kasir_terbaru)): ?>
                    <div class="p-4 text-center text-muted" style="font-size:0.85rem; background:#ffffff;">
                        Belum ada kasir terdaftar.
                    </div>
                <?php else: ?>
                    <div style="background:#ffffff;">
                        <?php foreach ($kasir_terbaru as $k):
                            $initials = strtoupper(substr($k['nama_kasir'], 0, 2));
                            ?>
                            <div
                                style="display:flex; align-items:center; justify-content:space-between; padding:0.95rem 1.25rem; border-bottom:1px solid #edf2ef; background:#ffffff;">
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div class="sb-avatar">
                                        <?php echo $initials; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:700; font-size:0.88rem; color:#1e3932;">
                                            <?php echo htmlspecialchars($k['nama_kasir']); ?>
                                        </div>
                                        <div style="font-size:0.75rem; color:#64746d;">
                                            <?php echo htmlspecialchars($k['email']); ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="sb-badge sb-badge-green" style="font-size:0.7rem;">Aktif</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div style="padding:0.95rem 1rem; background:#ffffff; text-align:center;">
                    <a href="/starbuck/pages/cashiers/list.php"
                        style="text-decoration:none; font-weight:600; color:var(--sb-primary); font-size:0.82rem;">
                        Kelola Seluruh Kasir (<?php echo $total_kasir; ?>) &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

<?php include ROOTPATH . '/include/footer.php'; ?>