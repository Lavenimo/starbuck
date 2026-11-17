<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

$id = $_GET['id'] ?? $_GET['id_produk'] ?? '';

// Deteksi nama kolom primary key (id atau id_produk)
$col_id = 'id_produk';
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM produk LIKE 'id_produk'");
if (!$check_col || mysqli_num_rows($check_col) == 0) {
    $col_id = 'id';
}

$query = "SELECT * FROM produk WHERE $col_id = '" . mysqli_real_escape_string($koneksi, $id) . "'";
$result = mysqli_query($koneksi, $query);
$row = $result ? mysqli_fetch_assoc($result) : null;

if (!$row) {
    echo "<script>alert('Data produk tidak ditemukan!'); window.location.href='list.php';</script>";
    exit;
}

$id_val = $row[$col_id] ?? ($row['id'] ?? ($row['id_produk'] ?? $id));
?>

<div class="container" style="max-width: 680px;">

    <!-- BREADCRUMB & TITLE -->
    <div class="sb-page-header mb-3">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; 
                <a href="/starbuck/pages/products/list.php">Produk Menu</a> &bull; 
                <span>Edit Menu</span>
            </div>
            <h1 class="sb-page-title">
                Edit Menu Produk
            </h1>
            <p class="sb-page-subtitle">Ubah informasi menu #PRD-<?php echo str_pad($id_val, 3, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div>
            <a href="list.php" class="sb-btn sb-btn-secondary sb-btn-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="sb-card">
        <div class="sb-card-body p-4">
            <form action="../../process/process_products.php" method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_val); ?>">
                <input type="hidden" name="id_produk" value="<?php echo htmlspecialchars($id_val); ?>">

                <div class="sb-form-group">
                    <label class="sb-form-label" for="nama_produk">
                        Nama Minuman / Produk <span class="required">*</span>
                    </label>
                    <input type="text" name="nama_produk" id="nama_produk" class="sb-form-control" 
                           value="<?php echo htmlspecialchars($row['nama_produk']); ?>" required autofocus>
                </div>

                <div class="sb-form-group">
                    <label class="sb-form-label" for="harga">
                        Harga Jual Satuan <span class="required">*</span>
                    </label>
                    <div class="sb-input-wrap">
                        <span class="sb-input-prefix">Rp</span>
                        <input type="number" name="harga" id="harga" class="sb-form-control has-prefix" 
                               value="<?php echo htmlspecialchars($row['harga']); ?>" min="0" step="500" required>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="list.php" class="sb-btn sb-btn-secondary">Batal</a>
                    <button type="submit" class="sb-btn sb-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.81 9.94l-3.75-3.75L3 17.25V21h3.75L17.81 9.94zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        Perbarui Menu Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include ROOTPATH . '/include/footer.php'; ?>
