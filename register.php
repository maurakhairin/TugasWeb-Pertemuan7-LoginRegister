<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = array();
$isiNama  = '';
$isiEmail = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = clean($_POST['nama'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $konfirmasi      = $_POST['konfirmasi_password'] ?? '';
    $isiNama  = $nama;
    $isiEmail = $email;

    if ($nama == '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if ($email == '') {
        $errors[] = 'Email wajib diisi.';
    }

    if ($password == '') {
        $errors[] = 'Password wajib diisi.';
    }

    if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid. Contoh yang benar: nama@email.com';
    }

    if ($password != '' && strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if ($password != '' && $password != $konfirmasi) {
        $errors[] = 'Konfirmasi password tidak sama dengan password.';
    }

    $users = getUsers();

    if (count($errors) == 0) {
        $userLama = findUserByEmail($email, $users);

        if ($userLama != null) {
            $errors[] = 'Email ini sudah terdaftar. Silakan pakai email lain atau langsung login.';
        }
    }

    if (count($errors) == 0) {

        $userBaru = array(
            'id'    => generateNextId($users),
            'nama'  => $nama,
            'email' => $email,
            'password'       => password_hash($password, PASSWORD_DEFAULT),
            'remember_token' => null,
            'created_at'     => date('Y-m-d H:i:s'),
        );

        $users[] = $userBaru;
        if (saveUsers($users)) {
            header('Location: login.php?registered=1');
            exit;
        }
        $errors[] = 'Gagal menyimpan data. Periksa izin akses folder "data".';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="halaman-auth">

    <div class="kotak-auth">

        <h1>Buat Akun Baru</h1>
        <p class="sub-judul">Silakan isi data di bawah ini untuk mendaftar.</p>

        <?php?>
        <?php if (count($errors) > 0): ?>
            <div class="pesan pesan-error">
                <ul>
                    <?php foreach ($errors as $pesan): ?>
                        <?php?>
                        <li><?php echo $pesan; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">

            <div class="baris-form">
                <label for="nama">Nama Lengkap</label>
                <?php?>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="<?php echo $isiNama; ?>"
                    placeholder="Contoh: Budi Santoso"
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
                    placeholder="nama@email.com"
                    required
                >
            </div>

            <div class="baris-form">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    required
                >
            </div>

            <div class="baris-form">
                <label for="konfirmasi_password">Ulangi Password</label>
                <input
                    type="password"
                    id="konfirmasi_password"
                    name="konfirmasi_password"
                    placeholder="Ketik ulang password"
                    required
                >
            </div>

            <button type="submit" class="tombol tombol-penuh">Daftar</button>

        </form>

        <p class="teks-bawah">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>

    </div>

</body>
</html>
