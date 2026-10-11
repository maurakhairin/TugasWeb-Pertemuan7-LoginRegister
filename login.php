<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors   = array();
$isiEmail = '';
$pesanInfo = '';

if (isset($_GET['registered'])) {
    $pesanInfo = 'Pendaftaran berhasil! Silakan login dengan akun barumu.';

} elseif (isset($_GET['logout'])) {
    $pesanInfo = 'Kamu sudah berhasil logout.';

} elseif (isset($_GET['perlu_login'])) {
    $pesanInfo = 'Silakan login dulu untuk membuka halaman tersebut.';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $ingatSaya = isset($_POST['ingat_saya']);
    $isiEmail = $email;

    if ($email == '') {
        $errors[] = 'Email wajib diisi.';
    }

    if ($password == '') {
        $errors[] = 'Password wajib diisi.';
    }


    if (count($errors) == 0) {

        $users = getUsers();
        $user  = findUserByEmail($email, $users);

        if ($user == null || !password_verify($password, $user['password'])) {

            $errors[] = 'Email atau password salah.';

        } else {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];

            if ($ingatSaya) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);

                updateUser($user['id'], array('remember_token' => $tokenHash));
                $masaBerlaku = time() + (30 * 24 * 60 * 60);
                setcookie('remember_email', $user['email'], $masaBerlaku, '/', '', false, true);
                setcookie('remember_token', $token, $masaBerlaku, '/', '', false, true);
            }

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="halaman-auth">

    <div class="kotak-auth">

        <h1>Masuk</h1>
        <p class="sub-judul">Login untuk melanjutkan ke dashboard.</p>

        <?php?>
        <?php if ($pesanInfo != ''): ?>
            <div class="pesan pesan-sukses">
                <?php echo $pesanInfo; ?>
            </div>
        <?php endif; ?>

        <?php?>
        <?php if (count($errors) > 0): ?>
            <div class="pesan pesan-error">
                <ul>
                    <?php foreach ($errors as $pesan): ?>
                        <li><?php echo $pesan; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">

            <div class="baris-form">
                <label for="email">Email</label>
                <?php?>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo $isiEmail; ?>"
                    placeholder="nama@email.com"
                    required
                    autofocus
                >
            </div>

            <div class="baris-form">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <div class="baris-centang">
                <label>
                    <input type="checkbox" name="ingat_saya" value="1">
                    Ingat saya selama 30 hari
                </label>
            </div>

            <button type="submit" class="tombol tombol-penuh">Login</button>

        </form>

        <p class="teks-bawah">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>

    </div>

</body>
</html>
