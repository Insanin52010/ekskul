<?php
require 'koneksi.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard_admin.php");
        exit;
    } elseif ($_SESSION['role'] == 'pelatih') {
        header("Location: dashboard_pelatih.php");
        exit;
    } else {
        header("Location: dashboard_siswa.php");
        exit;
    }
}

if (isset($_POST['login'])) {
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis']));
    $password = $_POST['password'];

    if (!ctype_digit($password)) {
        $error = "Password hanya boleh diisi dengan angka!";
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE nomor_induk = '$nis'");
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] == 'admin') {
                    header("Location: dashboard_admin.php");
                } elseif ($row['role'] == 'pelatih') {
                    header("Location: dashboard_pelatih.php");
                } else {
                    header("Location: dashboard_siswa.php");
                }
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Akun dengan NIS / NIP tersebut tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ekskul SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" style="width: 64px; height: 64px; object-fit: contain; border-radius: 50%; background: #fff; padding: 4px; box-shadow: 0 4px 12px rgba(124,58,237,0.18); margin-bottom: 10px;">
            <div><span class="school-badge">SMKN 5 MALANG</span></div>
            <h2>Portal Ekstrakurikuler</h2>
            <p>Gali Potensi, Kembangkan Bakat, Raih Prestasi</p>
        </div>

        <?php if (isset($_SESSION['pesan_sukses'])) : ?>
            <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>
        
        <?php if(isset($error)) : ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>NIS / NIP / ID Pengguna</label>
                <input type="text" name="nis" placeholder="Masukkan NIS atau NIP" required>
            </div>

            <div class="form-group">
                <label>Password (Hanya Angka)</label>
                <input type="password" name="password" placeholder="Masukkan Password angka" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
            </div>

            <button type="submit" name="login" class="btn btn-block">Masuk ke Portal</button>
        </form>
        
        <div class="auth-footer">
            <div style="margin-bottom: 8px;">
                <a href="lupa_password.php" style="font-size: 13px; color: #e11d48;">Lupa password? Reset di sini</a>
            </div>
            Belum mempunyai akun? <a href="register.php">Daftar sekarang</a>
        </div>
    </div>

    <script src="assets/animasi.js?v=2"></script>
</body>
</html>