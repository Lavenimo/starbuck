<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

// Ambil daftar produk
$produk_list = [];
$res_p = mysqli_query($koneksi, "SELECT id_produk, nama_produk, harga FROM produk ORDER BY nama_produk ASC");
if (!$res_p) {
    $res_p = mysqli_query($koneksi, "SELECT id AS id_produk, nama_produk, harga FROM produk ORDER BY nama_produk ASC");
}
if ($res_p) {
    while ($p = mysqli_fetch_assoc($res_p)) {
        $produk_list[] = $p;
    }
}
?>

<div class="container">

    <!-- BREADCRUMB & TITLE -->
    <div class="sb-page-header mb-4">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; 
                <a href="/starbuck/pages/transactions/list.php">Transaksi POS</a> &bull; 
                <span>Kasir Baru</span>
            </div>
            <h1 class="sb-page-title">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
                POS Kasir - Order Baru
            </h1>
            <p class="sb-page-subtitle">Pilih menu, tentukan jumlah pesanan, dan sistem akan mengalkulasi total secara otomatis</p>
        </div>
        <div>
            <a href="list.php" class="sb-btn sb-btn-secondary sb-btn-sm">
                &larr; Riwayat Transaksi
            </a>
        </div>
    </div>

    <!-- DUAL LAYOUT: FORM PESANAN (KIRI) + LIVE STRUK PREVIEW (KANAN) -->
    <form action="../../process/process_transactions.php" method="post" id="formTransaksi">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="nama_produk" id="nama_produk" value="">

        <div class="row g-4">
            
            <!-- KOLOM KIRI: KONFIGURASI PESANAN -->
            <div class="col-lg-7">
                <div class="sb-card">
                    <div class="sb-card-header">
                        <h2 class="sb-card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                            </svg>
                            Item Pesanan Pelanggan
                        </h2>
                    </div>
                    <div class="sb-card-body">
                        
                        <!-- Pilihan Produk -->
                        <div class="sb-form-group">
                            <label class="sb-form-label" for="id_produk">
                                Pilih Menu Kopi / Minuman <span class="required">*</span>
                            </label>
                            <select name="id_produk" id="id_produk" class="sb-form-control" required onchange="pilihProduk()" autofocus>
                                <option value="" data-nama="" data-harga="0">-- Silakan Pilih Minuman --</option>
                                <?php foreach ($produk_list as $p): ?>
                                <option value="<?php echo $p['id_produk']; ?>"
                                        data-nama="<?php echo htmlspecialchars($p['nama_produk']); ?>"
                                        data-harga="<?php echo $p['harga']; ?>">
                                    <?php echo htmlspecialchars($p['nama_produk']); ?> &mdash; Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="sb-form-help">Pilih varian kopi dari daftar menu resmi Starbucks</div>
                        </div>

                        <!-- Harga Satuan & Jumlah Beli -->
                        <div class="row g-3">
                            <div class="col-md-6 sb-form-group">
                                <label class="sb-form-label" for="harga">
                                    Harga Satuan
                                </label>
                                <div class="sb-input-wrap">
                                    <span class="sb-input-prefix">Rp</span>
                                    <input type="number" name="harga" id="harga" class="sb-form-control has-prefix" 
                                           value="0" readonly required>
                                </div>
                            </div>

                            <div class="col-md-6 sb-form-group">
                                <label class="sb-form-label" for="jumlah">
                                    Jumlah Beli (Cup/Qty) <span class="required">*</span>
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="sb-qty-stepper">
                                        <button type="button" class="sb-qty-btn" onclick="stepQty(-1)">&minus;</button>
                                        <input type="number" name="jumlah" id="jumlah" class="sb-qty-input" 
                                               min="1" value="1" required oninput="hitungTransaksi()">
                                        <button type="button" class="sb-qty-btn" onclick="stepQty(1)">&plus;</button>
                                    </div>
                                    <span class="text-muted" style="font-size:0.82rem;">Porsi Cup</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal & Jam Transaksi -->
                        <div class="row g-3 pt-3 border-top">
                            <div class="col-md-6 sb-form-group">
                                <label class="sb-form-label" for="tanggal">
                                    Tanggal Transaksi <span class="required">*</span>
                                </label>
                                <input type="date" name="tanggal" id="tanggal" class="sb-form-control" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-md-6 sb-form-group">
                                <label class="sb-form-label" for="waktu">
                                    Waktu Transaksi (WITA) <span class="required">*</span>
                                </label>
                                <input type="time" step="1" name="waktu" id="waktu" class="sb-form-control" 
                                       value="<?php echo date('H:i:s'); ?>" required>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: DIGITAL THERMAL RECEIPT / STRUK LIVE PREVIEW -->
            <div class="col-lg-5">
                <div class="sb-receipt-card">
                    
                    <div class="sb-receipt-header">
                        <div class="sb-receipt-brand">STARBUCKS</div>
                        <div class="sb-receipt-sub">Coffeehouse POS Receipt Preview</div>
                        <div class="text-muted" style="font-size:0.7rem; margin-top:0.25rem;">
                            Order #AUTO &bull; <?php echo date('d M Y'); ?>
                        </div>
                    </div>

                    <!-- Item Breakdown -->
                    <div class="mb-3 pb-3 border-bottom border-dashed">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="fw-bold" id="receiptItemName" style="color:var(--sb-dark-green); font-size:0.95rem;">
                                Belum memilih menu
                            </div>
                            <div class="fw-bold" id="receiptItemSubtotal" style="color:var(--sb-primary);">
                                Rp 0
                            </div>
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            <span id="receiptItemQty">1</span> cup &times; <span id="receiptItemPrice">Rp 0</span>
                        </div>
                    </div>

                    <!-- Perhitungan Otomatis -->
                    <div class="sb-receipt-row">
                        <span class="text-muted">Subtotal Belanja:</span>
                        <span class="fw-semibold" id="labelSubtotal">Rp 0</span>
                        <input type="hidden" name="subtotal" id="subtotal" value="0">
                    </div>

                    <div class="sb-receipt-row">
                        <span class="text-muted">Pajak Restoran (PPN 10%):</span>
                        <span class="fw-semibold text-warning" id="labelPajak">Rp 0</span>
                        <input type="hidden" name="pajak" id="pajak" value="0">
                    </div>

                    <!-- Grand Total -->
                    <div class="sb-receipt-row total-row">
                        <span>TOTAL AKHIR:</span>
                        <span id="labelTotal">Rp 0</span>
                        <input type="hidden" name="total" id="total" value="0">
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-2">
                        <button type="submit" class="sb-btn sb-btn-primary w-100 justify-content-center py-3" style="font-size:1rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                            Selesaikan & Simpan Transaksi
                        </button>

                        <a href="list.php" class="sb-btn sb-btn-secondary w-100 justify-content-center mt-2">
                            Batal
                        </a>
                    </div>

                    <div class="text-center mt-3 text-muted" style="font-size:0.72rem;">
                        Data transaksi otomatis tersinkronisasi ke laporan keuangan
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>

<script>
function pilihProduk() {
    var select = document.getElementById('id_produk');
    var opt = select.options[select.selectedIndex];
    var nama = opt.getAttribute('data-nama') || '';
    var harga = parseFloat(opt.getAttribute('data-harga')) || 0;

    document.getElementById('nama_produk').value = nama;
    document.getElementById('harga').value = harga;
    
    // Update live receipt labels
    document.getElementById('receiptItemName').textContent = nama ? nama : 'Belum memilih menu';
    hitungTransaksi();
}

function stepQty(delta) {
    var qtyInput = document.getElementById('jumlah');
    var current = parseInt(qtyInput.value) || 1;
    var next = Math.max(1, current + delta);
    qtyInput.value = next;
    hitungTransaksi();
}

function hitungTransaksi() {
    var harga = parseFloat(document.getElementById('harga').value) || 0;
    var jumlah = parseInt(document.getElementById('jumlah').value) || 1;
    if (jumlah < 1) {
        jumlah = 1;
        document.getElementById('jumlah').value = 1;
    }

    var subtotal = harga * jumlah;
    var pajak = Math.round(subtotal * 0.10); // PPN 10%
    var total = subtotal + pajak;

    // Set hidden inputs
    document.getElementById('subtotal').value = subtotal;
    document.getElementById('pajak').value = pajak;
    document.getElementById('total').value = total;

    // Format Rupiah helper
    var fmtRp = function(val) {
        return 'Rp ' + val.toLocaleString('id-ID');
    };

    // Update receipt preview
    document.getElementById('receiptItemPrice').textContent = fmtRp(harga);
    document.getElementById('receiptItemQty').textContent = jumlah;
    document.getElementById('receiptItemSubtotal').textContent = fmtRp(subtotal);
    document.getElementById('labelSubtotal').textContent = fmtRp(subtotal);
    document.getElementById('labelPajak').textContent = fmtRp(pajak);
    document.getElementById('labelTotal').textContent = fmtRp(total);
}

// Initial calculation
document.addEventListener('DOMContentLoaded', function() {
    hitungTransaksi();
});
</script>

<?php include ROOTPATH . '/include/footer.php'; ?>
