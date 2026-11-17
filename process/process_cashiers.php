<?php
include '../config/config.php';

if (isset($_POST["action"]) && $_POST["action"] == "add") {
    $nama_kasir     = $_POST['nama_kasir'] ?? '';
    $no_telp        = $_POST['no_telp'] ?? '';
    $alamat         = $_POST['alamat'] ?? '';
    $email          = $_POST['email'] ?? '';

    // Validasi agar tidak bisa submit jika data masih kosong
    if (empty(trim($nama_kasir)) || empty(trim($no_telp)) || empty(trim($alamat)) || empty(trim($email))) {
        echo "<script>
        alert('Semua data wajib diisi! Mohon lengkapi formulir.');
        window.location.href='../pages/cashiers/add.php';
        </script>";
        exit;
    }

    // Reset AUTO_INCREMENT agar ID mengikuti data terakhir yang ada (bukan melanjutkan yang sudah dihapus)
    $max_result = mysqli_query($koneksi, "SELECT IFNULL(MAX(id_kasir), 0) + 1 AS next_id FROM kasir");
    $max_row    = mysqli_fetch_assoc($max_result);
    $next_id    = (int)$max_row['next_id'];
    mysqli_query($koneksi, "ALTER TABLE kasir AUTO_INCREMENT = $next_id");

    // menambahkan data kasir ke table kasir (id_kasir dibuat otomatis/auto increment)
    $hasil = mysqli_query($koneksi, "INSERT INTO kasir (nama_kasir, no_telp, alamat, email) VALUES ('$nama_kasir', '$no_telp', '$alamat', '$email')");

    if ($hasil) {
        echo "<script>
        alert('Data berhasil ditambahkan!');
        window.location.href='../pages/cashiers/list.php';
        </script>";
    } else {
        echo "<script>
        alert('Data gagal ditambahkan: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/cashiers/add.php';
        </script>";
    }
} else if (isset($_POST["action"]) && $_POST["action"] == "edit") {
    $id_kasir       = $_POST["id_kasir"] ?? '';
    $nama_kasir     = $_POST['nama_kasir'] ?? '';
    $no_telp        = $_POST['no_telp'] ?? '';
    $alamat         = $_POST['alamat'] ?? '';
    $email          = $_POST['email'] ?? '';

    // Validasi agar tidak bisa submit jika data masih kosong
    if (empty(trim($nama_kasir)) || empty(trim($no_telp)) || empty(trim($alamat)) || empty(trim($email))) {
        echo "<script>
        alert('Semua data wajib diisi! Mohon lengkapi formulir.');
        window.location.href='../pages/cashiers/edit.php?id_kasir=$id_kasir';
        </script>";
        exit;
    }

    // mengupdate data kasir di table kasir
    $hasil = mysqli_query($koneksi, "UPDATE kasir SET nama_kasir='$nama_kasir', no_telp='$no_telp', alamat='$alamat', email='$email' WHERE id_kasir='$id_kasir'");

    if (!$hasil) {
        echo "<script>
        alert('Gagal mengupdate data kasir: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/cashiers/edit.php?id_kasir=$id_kasir';
        </script>";
    } else {
        echo "<script>
        alert('Data kasir berhasil diupdate.');
        window.location.href='../pages/cashiers/list.php';
        </script>";
    }

} else if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    $id_kasir = $_GET["id_kasir"] ?? '';
    // menghapus data kasir di table kasir
    $hasil = mysqli_query($koneksi, "DELETE FROM kasir WHERE id_kasir='$id_kasir'");

    if (!$hasil) {
        echo "<script>
        alert('Gagal menghapus data kasir: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/cashiers/list.php';
        </script>";
    } else {
        // Reset AUTO_INCREMENT agar ID berikutnya mengikuti data terakhir yang masih ada
        $max_result = mysqli_query($koneksi, "SELECT IFNULL(MAX(id_kasir), 0) + 1 AS next_id FROM kasir");
        $max_row    = mysqli_fetch_assoc($max_result);
        $next_id    = (int)$max_row['next_id'];
        mysqli_query($koneksi, "ALTER TABLE kasir AUTO_INCREMENT = $next_id");

        echo "<script>
        alert('Data kasir berhasil dihapus.');
        window.location.href='../pages/cashiers/list.php';
        </script>";
    }
}
