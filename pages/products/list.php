<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

$query = "SELECT * FROM produk ORDER BY id_produk DESC";
// Fallback jika nama kolomnya id
$result = mysqli_query($koneksi, $query);
if (!$result) {
    $result = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
}
$total_data = $result ? mysqli_num_rows($result) : 0;
?>

<div class="container">

    <!-- PAGE HEADER -->
    <div class="sb-page-header">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; <span>Produk Menu</span>
            </div>
            <h1 class="sb-page-title">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                </svg>
                Katalog Menu & Minuman
                <span class="sb-badge sb-badge-neutral"><?php echo $total_data; ?> Item Menu</span>
            </h1>
            <p class="sb-page-subtitle">Daftar minuman dan produk Starbucks yang tersedia untuk dijual di kasir</p>
        </div>
        <div>
            <a href="/starbuck/pages/products/add.php" class="sb-btn sb-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Produk Baru
            </a>
        </div>
    </div>

    <!-- DATA CARD & TABLE -->
    <div class="sb-card">
        <div class="sb-card-header">
            <div class="d-flex align-items-center gap-2">
                <h2 class="sb-card-title">Daftar Menu Aktif</h2>
            </div>
            <!-- Search Box -->
            <div class="sb-search-box">
                <svg class="sb-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="searchProduk" placeholder="Cari nama kopi / produk...">
            </div>
        </div>

        <div class="sb-table-wrap">
            <table class="sb-table" id="tableProduk">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Varian Minuman / Menu</th>
                        <th class="text-end" style="width: 200px;">Harga Satuan</th>
                        <th class="text-center" style="width: 130px;">Ketersediaan</th>
                        <th class="text-center" style="width: 150px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_data == 0): ?>
                    <tr>
                        <td colspan="5">
                            <div class="sb-empty-state">
                                <div class="sb-empty-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                                    </svg>
                                </div>
                                <div class="sb-empty-title">Belum Ada Menu Produk</div>
                                <p class="sb-empty-desc">Tambahkan produk minuman kopi pertama Anda untuk mulai mencatat transaksi.</p>
                                <a href="/starbuck/pages/products/add.php" class="sb-btn sb-btn-primary sb-btn-sm">+ Tambah Menu Kopi</a>
                            </div>
                        </td>
                    </tr>
                    <?php else: 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                            $id_val = $row['id_produk'] ?? ($row['id'] ?? '');
                    ?>
                    <tr>
                        <td class="text-muted fw-bold"><?php echo $no++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="sb-avatar sb-avatar-coffee">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color:var(--sb-dark-green); font-size:0.95rem;">
                                        <?php echo htmlspecialchars($row['nama_produk']); ?>
                                    </div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        Menu Starbucks Original &bull; Kode #PRD-<?php echo str_pad($id_val, 3, '0', STR_PAD_LEFT); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold" style="color:var(--sb-primary); font-size:1rem;">
                                Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="sb-badge sb-badge-green">
                                <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                                Tersedia
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="edit.php?id=<?php echo $id_val; ?>&id_produk=<?php echo $id_val; ?>" 
                                   class="sb-btn sb-btn-sm sb-btn-edit" title="Edit Produk">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                    Edit
                                </a>
                                <a href="/starbuck/process/process_products.php?action=delete&id=<?php echo $id_val; ?>&id_produk=<?php echo $id_val; ?>"
                                   class="sb-btn sb-btn-sm sb-btn-delete"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?php echo addslashes($row['nama_produk']); ?>?')"
                                   title="Hapus Produk">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                    <tr id="searchEmptyRow" style="display:none;">
                        <td colspan="5" class="text-center py-4 text-muted">
                            Menu yang Anda cari tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('searchProduk', 'tableProduk');
    });
</script>

<?php include ROOTPATH . '/include/footer.php'; ?>
