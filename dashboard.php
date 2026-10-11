<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?perlu_login=1');
    exit;
}

$nama  = $_SESSION['nama'];
$email = $_SESSION['email'];
$id    = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="halaman-dashboard">

    <?php?>
    <div class="menu-atas">
        <span class="judul-menu">Dashboard</span>
        <div class="tautan-menu">
            <a href="profile.php">Edit Profil</a>
            <a href="logout.php" class="tombol tombol-keluar">Logout</a>
        </div>
    </div>

    <?php?>
    <div class="isi-dashboard">

        <div class="kartu kartu-sambutan">
            <h1>Selamat datang, <?php echo $nama; ?>!</h1>
            <p>Kamu berhasil masuk ke halaman yang diproteksi.</p>
        </div>

        <div class="kartu">
            <h2>Informasi Akun</h2>

            <table class="tabel-info">
                <tr>
                    <th>Nama</th>
                    <td><?php echo $nama; ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $email; ?></td>
                </tr>
                <tr>
                    <th>ID User</th>
                    <td><?php echo $id; ?></td>
                </tr>
            </table>
        </div>

        <div class="kartu">
            <h2>Keterangan</h2>
            <p class="teks-abu">
                Halaman ini hanya bisa dibuka setelah login.
                Coba tekan tombol Logout, lalu ketik alamat
                dashboard.php langsung di browser. Sistem akan
                otomatis mengembalikanmu ke halaman login.
            </p>
        </div>

    </div>

</body>
</html>
