<?php
include '../config/config.php';

// Cek kolom yang tersedia pada tabel transaksi
$check_cols = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi");
$existing_cols = [];
if ($check_cols) {
    while ($c = mysqli_fetch_assoc($check_cols)) {
        $existing_cols[] = $c['Field'];
    }
}

// Cek apakah tabel detail_transaksi ada di database
$has_detail_table = false;
$dt_cols = [];
$check_dt = mysqli_query($koneksi, "SHOW TABLES LIKE 'detail_transaksi'");
if ($check_dt && mysqli_num_rows($check_dt) > 0) {
    $has_detail_table = true;
    $res_dt_cols = mysqli_query($koneksi, "SHOW COLUMNS FROM detail_transaksi");
    if ($res_dt_cols) {
        while ($c = mysqli_fetch_assoc($res_dt_cols)) {
            $dt_cols[] = $c['Field'];
        }
    }
}

if (isset($_POST["action"]) && $_POST["action"] == "add") {
    $id_produk   = $_POST['id_produk'] ?? '';
    $nama_produk = $_POST['nama_produk'] ?? '';
    $jumlah      = (int)($_POST['jumlah'] ?? 1);
    $tanggal     = $_POST['tanggal'] ?? date('Y-m-d');
    $waktu       = $_POST['waktu'] ?? date('H:i:s');

    // Validasi data produk
    if (empty($id_produk) && empty(trim($nama_produk))) {
        echo "<script>
        alert('Silakan pilih produk terlebih dahulu!');
        window.location.href='../pages/transactions/add.php';
        </script>";
        exit;
    }

    if ($jumlah <= 0) {
        echo "<script>
        alert('Jumlah beli minimal 1!');
        window.location.href='../pages/transactions/add.php';
        </script>";
        exit;
    }

    // Ambil data produk valid langsung dari tabel produk untuk memastikan id_produk benar
    $prod_row = null;
    if (!empty($id_produk)) {
        $qp = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '" . mysqli_real_escape_string($koneksi, $id_produk) . "' LIMIT 1");
        if ($qp && mysqli_num_rows($qp) > 0) {
            $prod_row = mysqli_fetch_assoc($qp);
        }
    }

    if (!$prod_row && !empty($nama_produk)) {
        $qp = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk = '" . mysqli_real_escape_string($koneksi, $nama_produk) . "' LIMIT 1");
        if ($qp && mysqli_num_rows($qp) > 0) {
            $prod_row = mysqli_fetch_assoc($qp);
        }
    }

    if (!$prod_row) {
        echo "<script>
        alert('Produk tidak ditemukan di database! Silakan pilih produk dari daftar.');
        window.location.href='../pages/transactions/add.php';
        </script>";
        exit;
    }

    $id_produk   = $prod_row['id_produk'];
    $nama_produk = $prod_row['nama_produk'];
    $harga       = (float)$prod_row['harga'];

    // Perhitungan otomatis di sisi server
    $subtotal = $harga * $jumlah;
    $pajak    = round($subtotal * 0.10); // Pajak 10%
    $total    = $subtotal + $pajak;

    // Tentukan ID transaksi berikutnya secara eksplisit agar selalu unik dan tidak 0
    $max_result = mysqli_query($koneksi, "SELECT IFNULL(MAX(id_transaksi), 0) + 1 AS next_id FROM transaksi");
    $max_row    = mysqli_fetch_assoc($max_result);
    $next_id    = (int)($max_row['next_id'] ?? 1);

    // 1. Simpan ke tabel transaksi
    $cols = ['id_transaksi', 'subtotal', 'pajak', 'total', 'tanggal', 'waktu'];
    $vals = ["'$next_id'", "'$subtotal'", "'$pajak'", "'$total'", "'$tanggal'", "'$waktu'"];

    if (in_array('nama_produk', $existing_cols)) {
        $cols[] = 'nama_produk';
        $vals[] = "'" . mysqli_real_escape_string($koneksi, $nama_produk) . "'";
    }
    if (in_array('harga', $existing_cols)) {
        $cols[] = 'harga';
        $vals[] = "'$harga'";
    }
    if (in_array('jumlah', $existing_cols)) {
        $cols[] = 'jumlah';
        $vals[] = "'$jumlah'";
    }

    $sql = "INSERT INTO transaksi (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ")";
    $hasil = mysqli_query($koneksi, $sql);

    // 2. Jika tabel detail_transaksi ada, simpan juga rincian barangnya dengan id_produk yang valid
    if ($hasil && $has_detail_table) {
        $dt_insert_cols = [];
        $dt_insert_vals = [];

        // Deteksi primary key detail_transaksi jika bukan id_transaksi atau id_produk
        $pk_dt = $dt_cols[0] ?? '';
        if (!empty($pk_dt) && $pk_dt != 'id_transaksi' && $pk_dt != 'id_produk') {
            $m_dt = mysqli_query($koneksi, "SELECT IFNULL(MAX($pk_dt), 0) + 1 AS next_id FROM detail_transaksi");
            $m_dt_row = mysqli_fetch_assoc($m_dt);
            $next_dt_id = (int)($m_dt_row['next_id'] ?? 1);
            $dt_insert_cols[] = $pk_dt;
            $dt_insert_vals[] = "'$next_dt_id'";
        }

        if (in_array('id_transaksi', $dt_cols)) {
            $dt_insert_cols[] = 'id_transaksi';
            $dt_insert_vals[] = "'$next_id'";
        }
        if (in_array('id_produk', $dt_cols)) {
            $dt_insert_cols[] = 'id_produk';
            $dt_insert_vals[] = "'" . mysqli_real_escape_string($koneksi, $id_produk) . "'";
        }
        if (in_array('nama_produk', $dt_cols)) {
            $dt_insert_cols[] = 'nama_produk';
            $dt_insert_vals[] = "'" . mysqli_real_escape_string($koneksi, $nama_produk) . "'";
        }
        if (in_array('jumlah', $dt_cols)) {
            $dt_insert_cols[] = 'jumlah';
            $dt_insert_vals[] = "'$jumlah'";
        }
        if (in_array('qty', $dt_cols)) {
            $dt_insert_cols[] = 'qty';
            $dt_insert_vals[] = "'$jumlah'";
        }
        if (in_array('harga', $dt_cols)) {
            $dt_insert_cols[] = 'harga';
            $dt_insert_vals[] = "'$harga'";
        }
        if (in_array('subtotal', $dt_cols)) {
            $dt_insert_cols[] = 'subtotal';
            $dt_insert_vals[] = "'$subtotal'";
        }
        if (in_array('total', $dt_cols)) {
            $dt_insert_cols[] = 'total';
            $dt_insert_vals[] = "'$subtotal'";
        }

        if (!empty($dt_insert_cols)) {
            $sql_dt = "INSERT INTO detail_transaksi (" . implode(', ', $dt_insert_cols) . ") VALUES (" . implode(', ', $dt_insert_vals) . ")";
            mysqli_query($koneksi, $sql_dt);
        }
    }

    if ($hasil) {
        echo "<script>
        alert('Data transaksi berhasil ditambahkan!');
        window.location.href='../pages/transactions/list.php';
        </script>";
    } else {
        echo "<script>
        alert('Data gagal ditambahkan: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/transactions/add.php';
        </script>";
    }

} else if (isset($_POST["action"]) && $_POST["action"] == "edit") {
    $id_transaksi = $_POST["id_transaksi"] ?? '';
    $id_produk    = $_POST['id_produk'] ?? '';
    $nama_produk  = $_POST['nama_produk'] ?? '';
    $jumlah       = (int)($_POST['jumlah'] ?? 1);
    $tanggal      = $_POST['tanggal'] ?? date('Y-m-d');
    $waktu        = $_POST['waktu'] ?? date('H:i:s');

    if (empty(trim($id_transaksi))) {
        echo "<script>
        alert('ID Transaksi tidak valid!');
        window.location.href='../pages/transactions/list.php';
        </script>";
        exit;
    }

    // Ambil data produk valid dari tabel produk
    $prod_row = null;
    if (!empty($id_produk)) {
        $qp = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '" . mysqli_real_escape_string($koneksi, $id_produk) . "' LIMIT 1");
        if ($qp && mysqli_num_rows($qp) > 0) {
            $prod_row = mysqli_fetch_assoc($qp);
        }
    }
    if (!$prod_row && !empty($nama_produk)) {
        $qp = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk = '" . mysqli_real_escape_string($koneksi, $nama_produk) . "' LIMIT 1");
        if ($qp && mysqli_num_rows($qp) > 0) {
            $prod_row = mysqli_fetch_assoc($qp);
        }
    }

    if (!$prod_row) {
        echo "<script>
        alert('Produk tidak ditemukan di database!');
        window.location.href='../pages/transactions/edit.php?id_transaksi=$id_transaksi';
        </script>";
        exit;
    }

    $id_produk   = $prod_row['id_produk'];
    $nama_produk = $prod_row['nama_produk'];
    $harga       = (float)$prod_row['harga'];

    // Perhitungan otomatis
    $subtotal = $harga * $jumlah;
    $pajak    = round($subtotal * 0.10);
    $total    = $subtotal + $pajak;

    // 1. Update tabel transaksi
    $sets = [
        "subtotal='$subtotal'",
        "pajak='$pajak'",
        "total='$total'",
        "tanggal='$tanggal'",
        "waktu='$waktu'"
    ];

    if (in_array('nama_produk', $existing_cols)) {
        $sets[] = "nama_produk='" . mysqli_real_escape_string($koneksi, $nama_produk) . "'";
    }
    if (in_array('harga', $existing_cols)) {
        $sets[] = "harga='$harga'";
    }
    if (in_array('jumlah', $existing_cols)) {
        $sets[] = "jumlah='$jumlah'";
    }

    $sql = "UPDATE transaksi SET " . implode(', ', $sets) . " WHERE id_transaksi='$id_transaksi'";
    $hasil = mysqli_query($koneksi, $sql);

    // 2. Update atau tambahkan ke detail_transaksi jika ada
    if ($hasil && $has_detail_table) {
        $cek_dt = mysqli_query($koneksi, "SELECT * FROM detail_transaksi WHERE id_transaksi='$id_transaksi'");
        if ($cek_dt && mysqli_num_rows($cek_dt) > 0) {
            $dt_sets = [];
            if (in_array('id_produk', $dt_cols)) {
                $dt_sets[] = "id_produk='" . mysqli_real_escape_string($koneksi, $id_produk) . "'";
            }
            if (in_array('nama_produk', $dt_cols)) {
                $dt_sets[] = "nama_produk='" . mysqli_real_escape_string($koneksi, $nama_produk) . "'";
            }
            if (in_array('jumlah', $dt_cols)) {
                $dt_sets[] = "jumlah='$jumlah'";
            }
            if (in_array('qty', $dt_cols)) {
                $dt_sets[] = "qty='$jumlah'";
            }
            if (in_array('harga', $dt_cols)) {
                $dt_sets[] = "harga='$harga'";
            }
            if (in_array('subtotal', $dt_cols)) {
                $dt_sets[] = "subtotal='$subtotal'";
            }
            if (in_array('total', $dt_cols)) {
                $dt_sets[] = "total='$subtotal'";
            }
            if (!empty($dt_sets)) {
                mysqli_query($koneksi, "UPDATE detail_transaksi SET " . implode(', ', $dt_sets) . " WHERE id_transaksi='$id_transaksi'");
            }
        } else {
            // Jika sebelumnya belum ada detail, insert baru
            $dt_insert_cols = [];
            $dt_insert_vals = [];
            $pk_dt = $dt_cols[0] ?? '';
            if (!empty($pk_dt) && $pk_dt != 'id_transaksi' && $pk_dt != 'id_produk') {
                $m_dt = mysqli_query($koneksi, "SELECT IFNULL(MAX($pk_dt), 0) + 1 AS next_id FROM detail_transaksi");
                $m_dt_row = mysqli_fetch_assoc($m_dt);
                $next_dt_id = (int)($m_dt_row['next_id'] ?? 1);
                $dt_insert_cols[] = $pk_dt;
                $dt_insert_vals[] = "'$next_dt_id'";
            }
            if (in_array('id_transaksi', $dt_cols)) {
                $dt_insert_cols[] = 'id_transaksi';
                $dt_insert_vals[] = "'$id_transaksi'";
            }
            if (in_array('id_produk', $dt_cols)) {
                $dt_insert_cols[] = 'id_produk';
                $dt_insert_vals[] = "'" . mysqli_real_escape_string($koneksi, $id_produk) . "'";
            }
            if (in_array('nama_produk', $dt_cols)) {
                $dt_insert_cols[] = 'nama_produk';
                $dt_insert_vals[] = "'" . mysqli_real_escape_string($koneksi, $nama_produk) . "'";
            }
            if (in_array('jumlah', $dt_cols)) {
                $dt_insert_cols[] = 'jumlah';
                $dt_insert_vals[] = "'$jumlah'";
            }
            if (in_array('qty', $dt_cols)) {
                $dt_insert_cols[] = 'qty';
                $dt_insert_vals[] = "'$jumlah'";
            }
            if (in_array('harga', $dt_cols)) {
                $dt_insert_cols[] = 'harga';
                $dt_insert_vals[] = "'$harga'";
            }
            if (in_array('subtotal', $dt_cols)) {
                $dt_insert_cols[] = 'subtotal';
                $dt_insert_vals[] = "'$subtotal'";
            }
            if (in_array('total', $dt_cols)) {
                $dt_insert_cols[] = 'total';
                $dt_insert_vals[] = "'$subtotal'";
            }
            if (!empty($dt_insert_cols)) {
                mysqli_query($koneksi, "INSERT INTO detail_transaksi (" . implode(', ', $dt_insert_cols) . ") VALUES (" . implode(', ', $dt_insert_vals) . ")");
            }
        }
    }

    if (!$hasil) {
        echo "<script>
        alert('Gagal mengupdate data transaksi: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/transactions/edit.php?id_transaksi=$id_transaksi';
        </script>";
    } else {
        echo "<script>
        alert('Data transaksi berhasil diupdate.');
        window.location.href='../pages/transactions/list.php';
        </script>";
    }

} else if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    $id_transaksi = $_GET["id_transaksi"] ?? '';

    // Hapus dari detail_transaksi terlebih dahulu agar aman dari foreign key
    if ($has_detail_table) {
        mysqli_query($koneksi, "DELETE FROM detail_transaksi WHERE id_transaksi='$id_transaksi'");
    }

    // Menghapus data dari transaksi
    $hasil = mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_transaksi='$id_transaksi'");

    if (!$hasil) {
        echo "<script>
        alert('Gagal menghapus data transaksi: " . mysqli_error($koneksi) . "');
        window.location.href='../pages/transactions/list.php';
        </script>";
    } else {
        echo "<script>
        alert('Data transaksi berhasil dihapus.');
        window.location.href='../pages/transactions/list.php';
        </script>";
    }
}
?>
