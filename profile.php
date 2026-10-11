<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?perlu_login=1');
    exit;
}

$errors      = array();
$pesanSukses = '';
$users    = getUsers();
$userAktif = null;

foreach ($users as $user) {
    if ($user['id'] == $_SESSION['user_id']) {
        $userAktif = $user;
        break;
    }
}

if ($userAktif == null) {
    header('Location: logout.php');
    exit;
}

$isiNama  = $userAktif['nama'];
$isiEmail = $userAktif['email'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama  = clean($_POST['nama'] ?? '');
    $email = clean($_POST['email'] ?? '');

    $passwordLama = $_POST['password_lama'] ?? '';
    $passwordBaru = $_POST['password_baru'] ?? '';
    $konfirmasi   = $_POST['konfirmasi_password'] ?? '';
    $isiNama  = $nama;
    $isiEmail = $email;
    if ($nama == '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if ($email == '') {
        $errors[] = 'Email wajib diisi.';
    }

    if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (count($errors) == 0) {
        $userLain = findUserByEmail($email, $users);

        if ($userLain != null && $userLain['id'] != $userAktif['id']) {
            $errors[] = 'Email tersebut sudah dipakai akun lain.';
        }
    }

    $passwordFinal = $userAktif['password'];

    if ($passwordBaru != '') {
        if ($passwordLama == '') {
            $errors[] = 'Untuk mengganti password, isi dulu password lama.';

        } elseif (!password_verify($passwordLama, $userAktif['password'])) {
            $errors[] = 'Password lama salah.';
        }

        if (strlen($passwordBaru) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        }

        if ($passwordBaru != $konfirmasi) {
            $errors[] = 'Konfirmasi password baru tidak sama.';
        }

        if (count($errors) == 0) {
            $passwordFinal = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }
    }

    if (count($errors) == 0) {

        $dataBaru = array(
            'nama'     => $nama,
            'email'    => $email,
            'password' => $passwordFinal,
        );

        if (updateUser($userAktif['id'], $dataBaru)) {
            $_SESSION['nama']  = $nama;
            $_SESSION['email'] = $email;

            $pesanSukses = 'Profil berhasil diperbarui.';
            $userAktif['nama']     = $nama;
            $userAktif['email']    = $email;
            $userAktif['password'] = $passwordFinal;

        } else {
            $errors[] = 'Gagal menyimpan perubahan. Periksa izin folder "data".';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="halaman-dashboard">

    <div class="menu-atas">
        <span class="judul-menu">Edit Profil</span>
        <div class="tautan-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php" class="tombol tombol-keluar">Logout</a>
        </div>
    </div>

    <div class="isi-dashboard">

        <div class="kartu">

            <h2>Ubah Data Akun</h2>

            <?php if ($pesanSukses != ''): ?>
                <div class="pesan pesan-sukses">
                    <?php echo $pesanSukses; ?>
                </div>
            <?php endif; ?>

            <?php if (count($errors) > 0): ?>
                <div class="pesan pesan-error">
                    <ul>
                        <?php foreach ($errors as $pesan): ?>
                            <li><?php echo $pesan; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="POST">

                <div class="baris-form">
                    <label for="nama">Nama Lengkap</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?php echo $isiNama; ?>"
                        required
                    >
                </div>

                <div class="baris-form">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo $isiEmail; ?>"
                        required
                    >
                </div>

                <hr class="garis-pemisah">

                <p class="teks-abu teks-kecil">
                    Bagian di bawah ini hanya diisi kalau ingin
                    mengganti password. Kalau tidak, biarkan kosong.
                </p>

                <div class="baris-form">
                    <label for="password_lama">Password Lama</label>
                    <input
                        type="password"
                        id="password_lama"
                        name="password_lama"
                        placeholder="Kosongkan jika tidak diubah"
                    >
                </div>

                <div class="baris-form">
                    <label for="password_baru">Password Baru</label>
                    <input
                        type="password"
                        id="password_baru"
                        name="password_baru"
                        placeholder="Minimal 6 karakter"
                    >
                </div>

                <div class="baris-form">
                    <label for="konfirmasi_password">Ulangi Password Baru</label>
                    <input
                        type="password"
                        id="konfirmasi_password"
                        name="konfirmasi_password"
                        placeholder="Ketik ulang password baru"
                    >
                </div>

                <button type="submit" class="tombol tombol-penuh">Simpan Perubahan</button>

            </form>

        </div>

    </div>

</body>
</html>
