<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

// Cek apakah tabel detail_transaksi ada di database
$has_detail_table = false;
$dt_cols = [];
$check_dt = mysqli_query($koneksi, "SHOW TABLES LIKE 'detail_transaksi'");
if ($check_dt && mysqli_num_rows($check_dt) > 0) {
    $has_detail_table = true;
    $res_dt = mysqli_query($koneksi, "SHOW COLUMNS FROM detail_transaksi");
    if ($res_dt) {
        while ($col = mysqli_fetch_assoc($res_dt)) {
            $dt_cols[] = $col['Field'];
        }
    }
}

if ($has_detail_table) {
    $col_jumlah_select = "NULL AS dt_jumlah";
    if (in_array('jumlah', $dt_cols)) {
        $col_jumlah_select = "dt.jumlah AS dt_jumlah";
    } else if (in_array('qty', $dt_cols)) {
        $col_jumlah_select = "dt.qty AS dt_jumlah";
    } else if (in_array('banyak', $dt_cols)) {
        $col_jumlah_select = "dt.banyak AS dt_jumlah";
    } else if (in_array('kuantitas', $dt_cols)) {
        $col_jumlah_select = "dt.kuantitas AS dt_jumlah";
    }

    $col_harga_select = "NULL AS dt_harga";
    if (in_array('harga', $dt_cols)) {
        $col_harga_select = "dt.harga AS dt_harga";
    }

    $query = "SELECT t.*, $col_jumlah_select, $col_harga_select, p.nama_produk AS prod_nama, p.harga AS prod_harga
              FROM transaksi t
              LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
              LEFT JOIN produk p ON dt.id_produk = p.id_produk
              ORDER BY t.id_transaksi DESC";
} else {
    $query = "SELECT * FROM transaksi ORDER BY id_transaksi DESC";
}

$result = mysqli_query($koneksi, $query);
$total_trx = $result ? mysqli_num_rows($result) : 0;

// Hitung total omset
$total_omset = 0;
$res_omset = mysqli_query($koneksi, "SELECT SUM(total) AS omset FROM transaksi");
if ($res_omset && $row_o = mysqli_fetch_assoc($res_omset)) {
    $total_omset = (float)($row_o['omset'] ?? 0);
}
?>

<div class="container">

    <!-- PAGE HEADER -->
    <div class="sb-page-header">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; <span>Transaksi POS</span>
            </div>
            <h1 class="sb-page-title">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
                Riwayat Transaksi Penjualan
                <span class="sb-badge sb-badge-neutral"><?php echo $total_trx; ?> Order Selesai</span>
            </h1>
            <p class="sb-page-subtitle">Daftar rekaman struk penjualan dan pembayaran kasir Starbucks Coffee</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="d-none d-md-flex align-items-center gap-2 px-3 py-2 bg-white rounded border">
                <span class="text-muted" style="font-size:0.75rem; font-weight:700;">TOTAL OMSET:</span>
                <span class="fw-bold" style="color:var(--sb-primary); font-size:0.95rem;">
                    Rp <?php echo number_format($total_omset, 0, ',', '.'); ?>
                </span>
            </div>
            <a href="/starbuck/pages/transactions/add.php" class="sb-btn sb-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    <!-- DATA CARD & TABLE -->
    <div class="sb-card">
        <div class="sb-card-header">
            <div class="d-flex align-items-center gap-2">
                <h2 class="sb-card-title">Daftar Struk Transaksi</h2>
            </div>
            <!-- Search Filter Box -->
            <div class="sb-search-box">
                <svg class="sb-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="searchTransaksi" placeholder="Cari ID, nama menu, tanggal...">
            </div>
        </div>

        <div class="sb-table-wrap">
            <table class="sb-table" id="tableTransaksi">
                <thead>
                    <tr>
                        <th style="width: 110px;">ID Order</th>
                        <th>Waktu Transaksi</th>
                        <th>Menu Minuman Dipesan</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">PPN 10%</th>
                        <th class="text-end">Total Akhir</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 190px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_trx == 0): ?>
                    <tr>
                        <td colspan="8">
                            <div class="sb-empty-state">
                                <div class="sb-empty-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                </div>
                                <div class="sb-empty-title">Belum Ada Transaksi Tercatat</div>
                                <p class="sb-empty-desc">Buka modul POS dan lakukan transaksi penjualan pertama untuk mencatat pendapatan.</p>
                                <a href="/starbuck/pages/transactions/add.php" class="sb-btn sb-btn-primary sb-btn-sm">+ Mulai Transaksi POS</a>
                            </div>
                        </td>
                    </tr>
                    <?php else: 
                        while ($row = mysqli_fetch_assoc($result)): 
                            $nama_tampil   = !empty($row['prod_nama']) ? $row['prod_nama'] : (!empty($row['nama_produk']) ? $row['nama_produk'] : 'Pesanan Minuman');
                            $harga_tampil  = !empty($row['prod_harga']) ? (float)$row['prod_harga'] : (!empty($row['dt_harga']) ? (float)$row['dt_harga'] : (!empty($row['harga']) ? (float)$row['harga'] : 0));
                            $jumlah_tampil = !empty($row['dt_jumlah']) ? (int)$row['dt_jumlah'] : (!empty($row['jumlah']) ? (int)$row['jumlah'] : (!empty($row['qty']) ? (int)$row['qty'] : 0));

                            if ($jumlah_tampil <= 0) {
                                if ($harga_tampil > 0 && !empty($row['subtotal']) && (float)$row['subtotal'] > 0) {
                                    $jumlah_tampil = round((float)$row['subtotal'] / $harga_tampil);
                                } else {
                                    $jumlah_tampil = 1;
                                }
                            }
                            $trx_code = '#TRX-' . str_pad($row['id_transaksi'], 3, '0', STR_PAD_LEFT);
                    ?>
                    <tr>
                        <td>
                            <span class="fw-bold" style="color:var(--sb-primary); font-size:0.88rem;">
                                <?php echo $trx_code; ?>
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:0.85rem;">
                                <?php echo htmlspecialchars($row['tanggal'] ?? '-'); ?>
                            </div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                <?php echo htmlspecialchars($row['waktu'] ?? '-'); ?> WITA
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="sb-avatar sb-avatar-coffee" style="width:28px; height:28px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/>
                                    </svg>
                                </span>
                                <div>
                                    <div class="fw-bold" style="color:var(--sb-dark-green); font-size:0.9rem;">
                                        <?php echo htmlspecialchars($nama_tampil); ?>
                                    </div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        <?php echo $jumlah_tampil; ?> cup &times; Rp <?php echo number_format($harga_tampil, 0, ',', '.'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end text-muted">
                            Rp <?php echo number_format($row['subtotal'] ?? 0, 0, ',', '.'); ?>
                        </td>
                        <td class="text-end text-muted" style="font-size:0.82rem;">
                            Rp <?php echo number_format($row['pajak'] ?? 0, 0, ',', '.'); ?>
                        </td>
                        <td class="text-end fw-bold" style="color:var(--sb-primary); font-size:0.95rem;">
                            Rp <?php echo number_format($row['total'] ?? 0, 0, ',', '.'); ?>
                        </td>
                        <td class="text-center">
                            <span class="sb-badge sb-badge-green">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                                Lunas
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <!-- Tombol Cetak Struk -->
                                <button type="button" class="sb-btn sb-btn-sm sb-btn-secondary" 
                                        onclick="cetakStruk('<?php echo $trx_code; ?>', '<?php echo addslashes($nama_tampil); ?>', <?php echo $jumlah_tampil; ?>, <?php echo $harga_tampil; ?>, <?php echo (float)$row['subtotal']; ?>, <?php echo (float)$row['pajak']; ?>, <?php echo (float)$row['total']; ?>, '<?php echo $row['tanggal']; ?>', '<?php echo $row['waktu']; ?>')"
                                        title="Cetak Struk Pembayaran">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                                    </svg>
                                    Cetak
                                </button>
                                
                                <!-- Tombol Edit -->
                                <a href="edit.php?id_transaksi=<?php echo $row['id_transaksi']; ?>" class="sb-btn sb-btn-sm sb-btn-edit" title="Edit Transaksi">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                </a>

                                <!-- Tombol Hapus -->
                                <a href="/starbuck/process/process_transactions.php?action=delete&id_transaksi=<?php echo $row['id_transaksi']; ?>"
                                   class="sb-btn sb-btn-sm sb-btn-delete"
                                   onclick="return confirm('Hapus transaksi <?php echo $trx_code; ?>?')"
                                   title="Hapus Transaksi">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                    <tr id="searchEmptyRow" style="display:none;">
                        <td colspan="8" class="text-center py-4 text-muted">
                            Data transaksi yang Anda cari tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- DIALOG STRUK THERMAL MODAL -->
<dialog id="strukModal" style="max-width:400px; width:92%; background:#ffffff; color:#111111 !important; border:none; border-radius:12px; padding:0; box-shadow:0 25px 60px rgba(0,0,0,0.4);">
    <div style="background:#1e3932; padding:0.9rem 1.25rem; display:flex; justify-content:space-between; align-items:center; border-top-left-radius:12px; border-top-right-radius:12px;">
        <h5 style="margin:0; font-size:0.95rem; color:#ffffff; font-weight:700; display:flex; align-items:center; gap:0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            Preview Struk Pembayaran
        </h5>
        <button type="button" onclick="document.getElementById('strukModal').close()" style="background:none; border:none; color:#ffffff; font-size:24px; cursor:pointer; line-height:1; padding:0 4px;" title="Tutup Preview">&times;</button>
    </div>
    <div style="padding:1.25rem; background:#f4f6f5;">
        <div id="printArea" style="background:#ffffff !important; color:#111111 !important; padding:1.25rem; font-family:'Courier New', Courier, monospace; font-size:12px; border:1px solid #d1d5db; border-radius:8px; line-height:1.45; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="text-align:center; margin-bottom:10px; color:#111111;">
                <div style="font-weight:bold; font-size:15px; letter-spacing:1px; color:#111111;">STARBUCKS COFFEE</div>
                <div style="font-size:11px; color:#444444;">Coffeehouse POS Indonesia</div>
                <div style="font-size:10px; color:#666666;">Store #0821 - Express Terminal</div>
            </div>
            <div style="border-top:1px dashed #666666; margin:8px 0;"></div>
            <div style="display:flex; justify-content:space-between; color:#111111;">
                <span style="color:#555555;">No. Struk:</span>
                <strong id="printTrxId" style="color:#111111;">#TRX-001</strong>
            </div>
            <div style="display:flex; justify-content:space-between; color:#111111;">
                <span style="color:#555555;">Waktu:</span>
                <span id="printTime" style="color:#111111;">-</span>
            </div>
            <div style="border-top:1px dashed #666666; margin:8px 0;"></div>
            <div style="margin-bottom:6px; color:#111111;">
                <div style="font-weight:bold; color:#111111; font-size:13px;" id="printItem">-</div>
                <div style="display:flex; justify-content:space-between; color:#333333;">
                    <span id="printQtyPrice">1x @ 0</span>
                    <span id="printSubtotal" style="font-weight:bold; color:#111111;">Rp 0</span>
                </div>
            </div>
            <div style="border-top:1px dashed #666666; margin:8px 0;"></div>
            <div style="display:flex; justify-content:space-between; color:#444444;">
                <span>Subtotal:</span>
                <span id="printSubtotal2" style="color:#111111;">Rp 0</span>
            </div>
            <div style="display:flex; justify-content:space-between; color:#444444;">
                <span>PPN (10%):</span>
                <span id="printPajak" style="color:#111111;">Rp 0</span>
            </div>
            <div style="border-top:1px solid #111111; margin:8px 0;"></div>
            <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:14px; color:#111111;">
                <span>TOTAL:</span>
                <span id="printTotal" style="color:#111111;">Rp 0</span>
            </div>
            <div style="border-top:1px dashed #666666; margin:8px 0;"></div>
            <div style="text-align:center; font-size:11px; margin-top:12px; color:#444444;">
                *** TERIMA KASIH ***<br>
                Selamat Menikmati Kopi Anda!
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="button" class="sb-btn sb-btn-secondary w-50 justify-content-center" onclick="document.getElementById('strukModal').close()">
                Tutup
            </button>
            <button type="button" class="sb-btn sb-btn-primary w-50 justify-content-center" onclick="cetakStrukPrint()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                </svg>
                Cetak Struk
            </button>
        </div>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('searchTransaksi', 'tableTransaksi');
    });

    function cetakStruk(trxId, item, qty, harga, subtotal, pajak, total, tgl, waktu) {
        var fmt = function(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        };

        document.getElementById('printTrxId').textContent = trxId;
        document.getElementById('printTime').textContent = tgl + ' ' + waktu + ' WITA';
        document.getElementById('printItem').textContent = item;
        document.getElementById('printQtyPrice').textContent = qty + ' cup x ' + fmt(harga);
        document.getElementById('printSubtotal').textContent = fmt(subtotal);
        document.getElementById('printSubtotal2').textContent = fmt(subtotal);
        document.getElementById('printPajak').textContent = fmt(pajak);
        document.getElementById('printTotal').textContent = fmt(total);

        var modal = document.getElementById('strukModal');
        if (modal && typeof modal.showModal === 'function') {
            modal.showModal();
        } else {
            window.print();
        }
    }

    function cetakStrukPrint() {
        var receipt = document.getElementById('printArea');
        if (!receipt) return;

        var portal = document.createElement('div');
        portal.id = 'receiptPrintPortal';
        portal.appendChild(receipt.cloneNode(true));
        document.body.appendChild(portal);
        document.body.classList.add('sb-printing');

        var cleanup = function() {
            document.body.classList.remove('sb-printing');
            if (portal.parentNode) portal.parentNode.removeChild(portal);
            window.removeEventListener('afterprint', cleanup);
        };

        window.addEventListener('afterprint', cleanup, { once: true });
        window.requestAnimationFrame(function() {
            window.print();
            cleanup();
        });
    }
</script>

<?php include ROOTPATH . '/include/footer.php'; ?>
