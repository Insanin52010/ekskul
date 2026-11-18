<?php
require 'koneksi.php';
require 'kelas_options.php';

$error_msg = '';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis']));
    $role = $_POST['role'];
    $kelas = ($role == 'siswa') ? mysqli_real_escape_string($conn, $_POST['kelas']) : NULL;
    $password_mentah = $_POST['password'];

    $status_akun = ($role == 'pelatih') ? 'pending' : 'aktif';

    $cek_nis = mysqli_query($conn, "SELECT id FROM users WHERE nomor_induk = '$nis'");

    if (mysqli_num_rows($cek_nis) > 0) {
        $error_msg = "NIS / NIP <b>$nis</b> sudah terdaftar! Silakan langsung masuk ke akun Anda.";
    } elseif (!ctype_digit($password_mentah)) {
        $error_msg = "Password hanya boleh berisi angka saja (0-9).";
    } elseif ($role == 'siswa' && empty($kelas)) {
        $error_msg = "Siswa wajib memilih kelas!";
    } else {
        $password = password_hash($password_mentah, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, nomor_induk, kelas, password, role, status_akun) 
                  VALUES ('$nama', '$nis', " . ($kelas ? "'$kelas'" : "NULL") . ", '$password', '$role', '$status_akun')";
        
        if (mysqli_query($conn, $query)) {
            if ($role == 'pelatih') {
                $pesan_notif = mysqli_real_escape_string($conn, "Pendaftaran Pelatih baru: $nama (NIP/ID: $nis) menunggu konfirmasi persetujuan akun.");
                $admins = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin'");
                while ($adm = mysqli_fetch_assoc($admins)) {
                    $aid = $adm['id'];
                    mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$aid', '$pesan_notif')");
                }
                $_SESSION['pesan_sukses'] = "Pendaftaran pelatih berhasil! Akun Anda sedang menunggu konfirmasi dari Admin sebelum bisa login.";
            } else {
                $_SESSION['pesan_sukses'] = "Pendaftaran akun berhasil! Silakan login menggunakan NIS/NIP Anda.";
            }
            header("Location: index.php");
            exit;
        } else {
            $error_msg = "Terjadi kesalahan saat mendaftar. Coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Ekskul SMKN 5 Malang</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <div class="school-badge">SMKN 5 MALANG</div>
            <h2>Buat Akun Baru</h2>
            <p>Portal Sistem Informasi Ekstrakurikuler</p>
        </div>

        <?php if (!empty($error_msg)) : ?>
            <div class="alert alert-danger"><?= $error_msg; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Daftar Sebagai</label>
                <select name="role" id="roleSelect" required onchange="toggleKelas()">
                    <option value="siswa">Siswa</option>
                    <option value="pelatih">Pelatih / Pembina (Butuh Konfirmasi Admin)</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Contoh: Ahmad Budi Santoso" required>
            </div>

            <div class="form-group">
                <label>NIS (Siswa) / NIP (Pelatih & Admin)</label>
                <input type="text" name="nis" placeholder="Contoh: 14699/1801.063" required>
            </div>
            
            <div class="form-group" id="kelasWrapper">
                <label>Pilih Kelas</label>
                <select name="kelas" id="kelasSelect" required>
                    <?php renderOpsiKelas(); ?>
                </select>
            </div>

            <div class="form-group">
                <label>Password (Hanya Angka)</label>
                <input type="password" name="password" placeholder="Masukkan PIN / Password angka" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
            </div>
            
            <button type="submit" name="register" class="btn btn-daftar btn-block">Daftar Sekarang</button>
        </form>
        
        <div class="auth-footer">
            Sudah punya akun? <a href="index.php">Masuk di sini</a>
        </div>
    </div>

    <script>
        function toggleKelas() {
            var role = document.getElementById('roleSelect').value;
            var wrapper = document.getElementById('kelasWrapper');
            var select = document.getElementById('kelasSelect');
            if (role === 'siswa') {
                wrapper.style.display = 'block';
                select.required = true;
            } else {
                wrapper.style.display = 'none';
                select.required = false;
            }
        }
    </script>
    <script src="assets/animasi.js"></script>
</body>
</html>