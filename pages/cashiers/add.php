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
                <a href="/starbuck/pages/cashiers/list.php">Kasir</a> &bull; 
                <span>Tambah Baru</span>
            </div>
            <h1 class="sb-page-title">
                Tambah Kasir Baru
            </h1>
            <p class="sb-page-subtitle">Isi rincian informasi di bawah untuk mendaftarkan staf kasir baru</p>
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
                <input type="hidden" name="action" value="add">

                <div class="sb-form-group">
                    <label class="sb-form-label" for="nama_kasir">
                        Nama Lengkap Kasir <span class="required">*</span>
                    </label>
                    <input type="text" name="nama_kasir" id="nama_kasir" class="sb-form-control" 
                           placeholder="Contoh: Barista Andi Pratama" required autofocus>
                    <div class="sb-form-help">Nama resmi staf yang akan tercantum pada struk kasir</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 sb-form-group">
                        <label class="sb-form-label" for="no_telp">
                            Nomor Telepon / WhatsApp <span class="required">*</span>
                        </label>
                        <input type="number" name="no_telp" id="no_telp" class="sb-form-control" 
                               placeholder="Contoh: 081234567890" required>
                    </div>

                    <div class="col-md-6 sb-form-group">
                        <label class="sb-form-label" for="email">
                            Alamat Email <span class="required">*</span>
                        </label>
                        <input type="email" name="email" id="email" class="sb-form-control" 
                               placeholder="nama@starbucks.co.id" required>
                    </div>
                </div>

                <div class="sb-form-group">
                    <label class="sb-form-label" for="alamat">
                        Alamat Domisili <span class="required">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" class="sb-form-control" rows="3" 
                              placeholder="Masukkan alamat lengkap tempat tinggal staf kasir..." required></textarea>
                </div>

                <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="list.php" class="sb-btn sb-btn-secondary">Batal</a>
                    <button type="submit" class="sb-btn sb-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        Simpan Data Kasir
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include ROOTPATH . '/include/footer.php'; ?>
