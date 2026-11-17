<?php
include '../config/config.php';

// Deteksi nama kolom primary key (id atau id_produk)
$col_id = 'id';
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM produk LIKE 'id_produk'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $col_id = 'id_produk';
}

// Pastikan primary key memiliki AUTO_INCREMENT
@mysqli_query($koneksi, "ALTER TABLE produk MODIFY $col_id INT(11) NOT NULL AUTO_INCREMENT");

if (isset($_POST["action"]) && $_POST["action"] == "add") {
    $nama_produk = $_POST['nama_produk'] ?? '';
    $harga = $_POST['harga'] ?? '';

    // Validasi agar tidak bisa submit jika data masih kosong
    if (empty(trim($nama_produk)) || empty(trim($harga))) {
        echo "<script>
        alert('Semua data wajib diisi! Mohon lengkapi formulir.');
        window.location.href='../pages/products/add.php';
        </script>";
        exit;
    }

    // Perbaiki jika sebelumnya ada data yang tersimpan dengan id = 0
    $cek_nol = mysqli_query($koneksi, "SELECT * FROM produk WHERE $col_id = 0");
    if ($cek_nol && mysqli_num_rows($cek_nol) > 0) {
        $m_res = mysqli_query($koneksi, "SELECT IFNULL(MAX($col_id), 0) + 1 AS new_id FROM produk WHERE $col_id > 0");
        $m_row = mysqli_fetch_assoc($m_res);
        $new_id = (int) ($m_row['new_id'] ?? 1);
        @mysqli_query($koneksi, "UPDATE produk SET $col_id = $new_id WHERE $col_id = 0 LIMIT 1");
    }

    // Dapatkan ID berikutnya secara eksplisit
    $max_result = mysqli_query($koneksi, "SELECT IFNULL(MAX($col_id), 0) + 1 AS next_id FROM produk");
    $max_row = mysqli_fetch_assoc($max_result);
    $next_id = (int) ($max_row['next_id'] ?? 1);
    @mysqli_query($koneksi, "ALTER TABLE produk AUTO_INCREMENT = $next_id");

    // Menambahkan data produk ke tabel produk dengan ID eksplisit
    $hasil = mysqli_query($koneksi, "INSERT INTO produk ($col_id, nama_produk, harga) VALUES ('$next_id', '$nama_produk', '$harga')");

    if ($hasil) {
        echo "<script>
        alert('Data berhasil ditambahkan!');
        window.location.href='../pages/products/list.php';
        </script>";
    } else {
        echo "<script>
        alert('Data gagal ditambahkan: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/products/add.php';
        </script>";
    }
} else if (isset($_POST["action"]) && $_POST["action"] == "edit") {
    $id = $_POST["id"] ?? $_POST["id_produk"] ?? '';
    $nama_produk = $_POST['nama_produk'] ?? '';
    $harga = $_POST['harga'] ?? '';

    // Validasi agar tidak bisa submit jika data masih kosong
    if (empty(trim($id)) || empty(trim($nama_produk)) || empty(trim($harga))) {
        echo "<script>
        alert('Semua data wajib diisi! Mohon lengkapi formulir.');
        window.location.href='../pages/products/edit.php?id=$id&id_produk=$id';
        </script>";
        exit;
    }

    // Mengupdate data produk di tabel produk
    $hasil = mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama_produk', harga='$harga' WHERE $col_id='$id'");

    if (!$hasil) {
        echo "<script>
        alert('Gagal mengupdate data produk: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/products/edit.php?id=$id&id_produk=$id';
        </script>";
    } else {
        echo "<script>
        alert('Data produk berhasil diupdate.');
        window.location.href='../pages/products/list.php';
        </script>";
    }

} else if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    $id = $_GET["id"] ?? $_GET["id_produk"] ?? '';

    // Menghapus data produk di tabel produk
    $hasil = mysqli_query($koneksi, "DELETE FROM produk WHERE $col_id='$id'");

    if (!$hasil) {
        echo "<script>
        alert('Gagal menghapus data produk: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/products/list.php';
        </script>";
    } else {
        // Reset AUTO_INCREMENT agar ID berikutnya mengikuti data terakhir yang masih ada
        $max_result = mysqli_query($koneksi, "SELECT IFNULL(MAX($col_id), 0) + 1 AS next_id FROM produk");
        $max_row = mysqli_fetch_assoc($max_result);
        $next_id = (int) ($max_row['next_id'] ?? 1);
        @mysqli_query($koneksi, "ALTER TABLE produk AUTO_INCREMENT = $next_id");

        echo "<script>
        alert('Data produk berhasil dihapus.');
        window.location.href='../pages/products/list.php';
        </script>";
    }
}
?>