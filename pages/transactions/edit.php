<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

$id = $_GET['id_transaksi'] ?? '';
$query = "SELECT * FROM transaksi WHERE id_transaksi = '" . mysqli_real_escape_string($koneksi, $id) . "'";
$result = mysqli_query($koneksi, $query);
$row = $result ? mysqli_fetch_assoc($result) : null;

if (!$row) {
    echo "<script>alert('Data transaksi tidak ditemukan!'); window.location.href='list.php';</script>";
    exit;
}

// Cek detail_transaksi jika ada
$id_produk_selected = '';
$check_dt = mysqli_query($koneksi, "SHOW TABLES LIKE 'detail_transaksi'");
if ($check_dt && mysqli_num_rows($check_dt) > 0) {
    $q_dt = mysqli_query($koneksi, "SELECT * FROM detail_transaksi WHERE id_transaksi = '$id' LIMIT 1");
    if ($q_dt && $row_dt = mysqli_fetch_assoc($q_dt)) {
        $id_produk_selected = $row_dt['id_produk'] ?? '';
        $dt_qty = $row_dt['jumlah'] ?? ($row_dt['qty'] ?? ($row_dt['banyak'] ?? ''));
        if (empty($row['jumlah']) && !empty($dt_qty)) {
            $row['jumlah'] = $dt_qty;
        }
        if (empty($row['harga']) && !empty($row_dt['harga'])) {
            $row['harga'] = $row_dt['harga'];
        }
        if (empty($row['nama_produk']) && !empty($row_dt['nama_produk'])) {
            $row['nama_produk'] = $row_dt['nama_produk'];
        }
    }
}

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

// Cocokkan id_produk jika belum terpilih
if (empty($id_produk_selected) && !empty($row['nama_produk'])) {
    foreach ($produk_list as $p) {
        if ($p['nama_produk'] == $row['nama_produk']) {
            $id_produk_selected = $p['id_produk'];
            if (empty($row['harga'])) {
                $row['harga'] = $p['harga'];
            }
            break;
        }
    }
}

// Hitung fallback jumlah jika belum ada
if (empty($row['jumlah']) && !empty($row['harga']) && (float)$row['harga'] > 0 && !empty($row['subtotal'])) {
    $row['jumlah'] = round((float)$row['subtotal'] / (float)$row['harga']);
}
if (empty($row['jumlah'])) {
    $row['jumlah'] = 1;
}
?>

<div class="container">

    <!-- BREADCRUMB & TITLE -->
    <div class="sb-page-header mb-4">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; 
                <a href="/starbuck/pages/transactions/list.php">Transaksi POS</a> &bull; 
                <span>Edit Order #TRX-<?php echo str_pad($row['id_transaksi'], 3, '0', STR_PAD_LEFT); ?></span>
            </div>
            <h1 class="sb-page-title">
                Edit Transaksi Kasir
            </h1>
            <p class="sb-page-subtitle">Ubah rincian pesanan, produk, atau kuantitas transaksi</p>
        </div>
        <div>
            <a href="list.php" class="sb-btn sb-btn-secondary sb-btn-sm">
                &larr; Riwayat Transaksi
            </a>
        </div>
    </div>

    <!-- DUAL LAYOUT: FORM PESANAN (KIRI) + LIVE STRUK PREVIEW (KANAN) -->
    <form action="../../process/process_transactions.php" method="post" id="formTransaksi">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id_transaksi" value="<?php echo htmlspecialchars($row['id_transaksi']); ?>">
        <input type="hidden" name="nama_produk" id="nama_produk" value="<?php echo htmlspecialchars($row['nama_produk'] ?? ''); ?>">

        <div class="row g-4">
            
            <!-- KOLOM KIRI: KONFIGURASI PESANAN -->
            <div class="col-lg-7">
                <div class="sb-card">
                    <div class="sb-card-header">
                        <h2 class="sb-card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                            </svg>
                            Perbarui Pesanan Pelanggan
                        </h2>
                    </div>
                    <div class="sb-card-body">
                        
                        <!-- Pilihan Produk -->
                        <div class="sb-form-group">
                            <label class="sb-form-label" for="id_produk">
                                Menu Kopi / Minuman <span class="required">*</span>
                            </label>
                            <select name="id_produk" id="id_produk" class="sb-form-control" required onchange="pilihProduk()" autofocus>
                                <option value="" data-nama="" data-harga="0">-- Silakan Pilih Minuman --</option>
                                <?php foreach ($produk_list as $p): 
                                    $is_selected = ($p['id_produk'] == $id_produk_selected || (isset($row['nama_produk']) && $row['nama_produk'] == $p['nama_produk']));
                                ?>
                                <option value="<?php echo $p['id_produk']; ?>"
                                        data-nama="<?php echo htmlspecialchars($p['nama_produk']); ?>"
                                        data-harga="<?php echo $p['harga']; ?>"
                                        <?php echo $is_selected ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['nama_produk']); ?> &mdash; Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
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
                                           value="<?php echo htmlspecialchars($row['harga'] ?? '0'); ?>" readonly required>
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
                                               min="1" value="<?php echo htmlspecialchars($row['jumlah'] ?? '1'); ?>" required oninput="hitungTransaksi()">
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
                                       value="<?php echo htmlspecialchars($row['tanggal'] ?? date('Y-m-d')); ?>" required>
                            </div>

                            <div class="col-md-6 sb-form-group">
                                <label class="sb-form-label" for="waktu">
                                    Waktu Transaksi (WITA) <span class="required">*</span>
                                </label>
                                <input type="time" step="1" name="waktu" id="waktu" class="sb-form-control" 
                                       value="<?php echo htmlspecialchars($row['waktu'] ?? date('H:i:s')); ?>" required>
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
                            Order #TRX-<?php echo str_pad($row['id_transaksi'], 3, '0', STR_PAD_LEFT); ?> &bull; Edit Mode
                        </div>
                    </div>

                    <!-- Item Breakdown -->
                    <div class="mb-3 pb-3 border-bottom border-dashed">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="fw-bold" id="receiptItemName" style="color:var(--sb-dark-green); font-size:0.95rem;">
                                <?php echo htmlspecialchars($row['nama_produk'] ?? 'Pesanan'); ?>
                            </div>
                            <div class="fw-bold" id="receiptItemSubtotal" style="color:var(--sb-primary);">
                                Rp <?php echo number_format($row['subtotal'] ?? 0, 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            <span id="receiptItemQty"><?php echo $row['jumlah'] ?? 1; ?></span> cup &times; <span id="receiptItemPrice">Rp <?php echo number_format($row['harga'] ?? 0, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <!-- Perhitungan Otomatis -->
                    <div class="sb-receipt-row">
                        <span class="text-muted">Subtotal Belanja:</span>
                        <span class="fw-semibold" id="labelSubtotal">Rp <?php echo number_format($row['subtotal'] ?? 0, 0, ',', '.'); ?></span>
                        <input type="hidden" name="subtotal" id="subtotal" value="<?php echo htmlspecialchars($row['subtotal'] ?? '0'); ?>">
                    </div>

                    <div class="sb-receipt-row">
                        <span class="text-muted">Pajak Restoran (PPN 10%):</span>
                        <span class="fw-semibold text-warning" id="labelPajak">Rp <?php echo number_format($row['pajak'] ?? 0, 0, ',', '.'); ?></span>
                        <input type="hidden" name="pajak" id="pajak" value="<?php echo htmlspecialchars($row['pajak'] ?? '0'); ?>">
                    </div>

                    <!-- Grand Total -->
                    <div class="sb-receipt-row total-row">
                        <span>TOTAL AKHIR:</span>
                        <span id="labelTotal">Rp <?php echo number_format($row['total'] ?? 0, 0, ',', '.'); ?></span>
                        <input type="hidden" name="total" id="total" value="<?php echo htmlspecialchars($row['total'] ?? '0'); ?>">
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-2">
                        <button type="submit" class="sb-btn sb-btn-primary w-100 justify-content-center py-3" style="font-size:1rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.81 9.94l-3.75-3.75L3 17.25V21h3.75L17.81 9.94zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                            Perbarui & Simpan Perubahan
                        </button>

                        <a href="list.php" class="sb-btn sb-btn-secondary w-100 justify-content-center mt-2">
                            Batal
                        </a>
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
    var pajak = Math.round(subtotal * 0.10);
    var total = subtotal + pajak;

    document.getElementById('subtotal').value = subtotal;
    document.getElementById('pajak').value = pajak;
    document.getElementById('total').value = total;

    var fmtRp = function(val) {
        return 'Rp ' + val.toLocaleString('id-ID');
    };

    document.getElementById('receiptItemPrice').textContent = fmtRp(harga);
    document.getElementById('receiptItemQty').textContent = jumlah;
    document.getElementById('receiptItemSubtotal').textContent = fmtRp(subtotal);
    document.getElementById('labelSubtotal').textContent = fmtRp(subtotal);
    document.getElementById('labelPajak').textContent = fmtRp(pajak);
    document.getElementById('labelTotal').textContent = fmtRp(total);
}

document.addEventListener('DOMContentLoaded', function() {
    hitungTransaksi();
});
</script>

<?php include ROOTPATH . '/include/footer.php'; ?>
