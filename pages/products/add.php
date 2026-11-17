<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
?>

<div class="container" style="max-width: 680px;">

    <!-- BREADCRUMB & TITLE -->
    <div class="sb-page-header mb-3">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; 
                <a href="/starbuck/pages/products/list.php">Produk Menu</a> &bull; 
                <span>Tambah Menu</span>
            </div>
            <h1 class="sb-page-title">
                Tambah Menu Starbucks
            </h1>
            <p class="sb-page-subtitle">Daftarkan varian minuman atau produk baru ke dalam sistem kasir</p>
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
                <input type="hidden" name="action" value="add">

                <div class="sb-form-group">
                    <label class="sb-form-label" for="nama_produk">
                        Nama Minuman / Produk <span class="required">*</span>
                    </label>
                    <input type="text" name="nama_produk" id="nama_produk" class="sb-form-control" 
                           placeholder="Contoh: Caffe Latte / Caramel Macchiato" required autofocus>
                    <div class="sb-form-help">Nama menu resmi yang akan muncul di layar kasir dan struk</div>
                </div>

                <div class="sb-form-group">
                    <label class="sb-form-label" for="harga">
                        Harga Jual Satuan <span class="required">*</span>
                    </label>
                    <div class="sb-input-wrap">
                        <span class="sb-input-prefix">Rp</span>
                        <input type="number" name="harga" id="harga" class="sb-form-control has-prefix" 
                               placeholder="55000" min="0" step="500" required>
                    </div>
                    <div class="sb-form-help">Harga standar dalam Rupiah (sebelum pajak)</div>
                </div>

                <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="list.php" class="sb-btn sb-btn-secondary">Batal</a>
                    <button type="submit" class="sb-btn sb-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        Simpan Menu Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include ROOTPATH . '/include/footer.php'; ?>
