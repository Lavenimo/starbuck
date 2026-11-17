<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/starbuck');
include ROOTPATH . '/include/header.php';
include ROOTPATH . '/config/config.php';

$id = $_GET['id_kasir'] ?? '';
$query = "SELECT * FROM kasir WHERE id_kasir = '" . mysqli_real_escape_string($koneksi, $id) . "'";
$result = mysqli_query($koneksi, $query);
$row = $result ? mysqli_fetch_assoc($result) : null;

if (!$row) {
    echo "<script>alert('Data kasir tidak ditemukan!'); window.location.href='list.php';</script>";
    exit;
}
?>

<div class="container" style="max-width: 680px;">

    <!-- BREADCRUMB & TITLE -->
    <div class="sb-page-header mb-3">
        <div>
            <div class="sb-breadcrumb">
                <a href="/starbuck/pages/dashboard.php">Dashboard</a> &bull; 
                <a href="/starbuck/pages/cashiers/list.php">Kasir</a> &bull; 
                <span>Edit Profil</span>
            </div>
            <h1 class="sb-page-title">
                Edit Data Kasir
            </h1>
            <p class="sb-page-subtitle">Perbarui data informasi akun kasir #<?php echo htmlspecialchars($row['id_kasir']); ?></p>
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
            <form action="../../process/process_cashiers.php" method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_kasir" value="<?php echo htmlspecialchars($row['id_kasir']); ?>">

                <div class="sb-form-group">
                    <label class="sb-form-label" for="nama_kasir">
                        Nama Lengkap Kasir <span class="required">*</span>
                    </label>
                    <input type="text" name="nama_kasir" id="nama_kasir" class="sb-form-control" 
                           value="<?php echo htmlspecialchars($row['nama_kasir']); ?>" required autofocus>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 sb-form-group">
                        <label class="sb-form-label" for="no_telp">
                            Nomor Telepon / WhatsApp <span class="required">*</span>
                        </label>
                        <input type="number" name="no_telp" id="no_telp" class="sb-form-control" 
                               value="<?php echo htmlspecialchars($row['no_telp']); ?>" required>
                    </div>

                    <div class="col-md-6 sb-form-group">
                        <label class="sb-form-label" for="email">
                            Alamat Email <span class="required">*</span>
                        </label>
                        <input type="email" name="email" id="email" class="sb-form-control" 
                               value="<?php echo htmlspecialchars($row['email']); ?>" required>
                    </div>
                </div>

                <div class="sb-form-group">
                    <label class="sb-form-label" for="alamat">
                        Alamat Domisili <span class="required">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" class="sb-form-control" rows="3" required><?php echo htmlspecialchars($row['alamat']); ?></textarea>
                </div>

                <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="list.php" class="sb-btn sb-btn-secondary">Batal</a>
                    <button type="submit" class="sb-btn sb-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.81 9.94l-3.75-3.75L3 17.25V21h3.75L17.81 9.94zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        Perbarui Data Kasir
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include ROOTPATH . '/include/footer.php'; ?>
