<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

$query = "SELECT * FROM kasir ORDER BY id_kasir DESC";
$result = mysqli_query($koneksi, $query);
$total_data = $result ? mysqli_num_rows($result) : 0;
?>

<div class="container">

    <!-- PAGE HEADER -->
    <div class="sb-page-header">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; <span>Kasir</span>
            </div>
            <h1 class="sb-page-title">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                Manajemen Kasir
                <span class="sb-badge sb-badge-neutral"><?php echo $total_data; ?> Terdaftar</span>
            </h1>
            <p class="sb-page-subtitle">Daftar akun staf kasir yang berwenang melayani transaksi pembayaran</p>
        </div>
        <div>
            <a href="/starbuck/pages/cashiers/add.php" class="sb-btn sb-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Kasir Baru
            </a>
        </div>
    </div>

    <!-- DATA CARD & TABLE -->
    <div class="sb-card">
        <div class="sb-card-header">
            <div class="d-flex align-items-center gap-2">
                <h2 class="sb-card-title">Daftar Kasir</h2>
            </div>
            <!-- Search Filter Box -->
            <div class="sb-search-box">
                <svg class="sb-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="searchKasir" placeholder="Cari nama, email, no telp...">
            </div>
        </div>

        <div class="sb-table-wrap">
            <table class="sb-table" id="tableKasir">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Profil Kasir</th>
                        <th>Kontak Telepon</th>
                        <th>Alamat Domisili</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 150px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_data == 0): ?>
                    <tr>
                        <td colspan="6">
                            <div class="sb-empty-state">
                                <div class="sb-empty-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="sb-empty-title">Belum Ada Data Kasir</div>
                                <p class="sb-empty-desc">Tambahkan kasir pertama Anda agar sistem POS dapat digunakan oleh operator.</p>
                                <a href="/starbuck/pages/cashiers/add.php" class="sb-btn sb-btn-primary sb-btn-sm">+ Tambah Kasir Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    <?php else: 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                            $initials = strtoupper(substr($row['nama_kasir'] ?? 'KS', 0, 2));
                    ?>
                    <tr>
                        <td class="text-muted fw-bold"><?php echo $no++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="sb-avatar">
                                    <?php echo $initials; ?>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color:var(--sb-dark-green); font-size:0.92rem;">
                                        <?php echo htmlspecialchars($row['nama_kasir']); ?>
                                    </div>
                                    <div class="text-muted" style="font-size:0.78rem;">
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size:0.85rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                                <?php echo htmlspecialchars($row['no_telp']); ?>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted" style="max-width:240px; font-size:0.85rem;">
                                <?php echo htmlspecialchars($row['alamat']); ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="sb-badge sb-badge-green">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                                Aktif
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="edit.php?id_kasir=<?php echo $row['id_kasir']; ?>" class="sb-btn sb-btn-sm sb-btn-edit" title="Ubah Data">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                    Edit
                                </a>
                                <a href="/starbuck/process/process_cashiers.php?action=delete&id_kasir=<?php echo $row['id_kasir']; ?>"
                                   class="sb-btn sb-btn-sm sb-btn-delete"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus kasir <?php echo addslashes($row['nama_kasir']); ?>?')"
                                   title="Hapus Data">
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
                        <td colspan="6" class="text-center py-4 text-muted">
                            Data kasir yang Anda cari tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('searchKasir', 'tableKasir');
    });
</script>

<?php include ROOTPATH . '/include/footer.php'; ?>
