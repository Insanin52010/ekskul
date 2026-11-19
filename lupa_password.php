<?php
require 'koneksi.php';
require 'kelas_options.php';

$error_msg = '';

if (isset($_POST['reset_password'])) {
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? 'siswa');
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis']));
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas'] ?? '');
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    if (!in_array($role, ['siswa', 'pelatih', 'admin'])) {
        $error_msg = "Role tidak valid!";
    } elseif (!ctype_digit($password_baru)) {
        $error_msg = "Password baru hanya boleh diisi dengan angka (0-9)!";
    } elseif ($password_baru !== $konfirmasi_password) {
        $error_msg = "Konfirmasi password tidak cocok dengan password baru!";
    } else {
        if ($role === 'siswa') {
            $query_cek = "SELECT id FROM users WHERE role = 'siswa' AND nomor_induk = '$nis' AND LOWER(nama) = LOWER('$nama') AND kelas = '$kelas'";
        } else {
            $query_cek = "SELECT id FROM users WHERE role = '$role' AND nomor_induk = '$nis' AND LOWER(nama) = LOWER('$nama')";
        }

        $cek = mysqli_query($conn, $query_cek);
        if (mysqli_num_rows($cek) === 1) {
            $user = mysqli_fetch_assoc($cek);
            $uid = $user['id'];
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $plain_sql = ($role === 'admin') ? "NULL" : "'" . mysqli_real_escape_string($conn, $password_baru) . "'";

            mysqli_query($conn, "UPDATE users SET password = '$hash', password_plain = $plain_sql WHERE id = '$uid'");

            $_SESSION['pesan_sukses'] = "✅ Password berhasil diatur ulang! Silakan login dengan password barumu.";
            header("Location: index.php");
            exit;
        } else {
            if ($role === 'siswa') {
                $error_msg = "Data tidak cocok! Pastikan NIS, Nama Lengkap, dan Kelas sesuai dengan data akunmu.";
            } elseif ($role === 'pelatih') {
                $error_msg = "Data tidak cocok! Pastikan NIP / ID Pelatih dan Nama Lengkap sesuai dengan data akunmu.";
            } else {
                $error_msg = "Data tidak cocok! Pastikan NIP / ID Administrator dan Nama Lengkap sesuai dengan data akunmu.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Ekskul SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body class="auth-body">
    <div class="auth-card" style="max-width: 460px;">
        <div class="auth-header">
            <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" style="width: 56px; height: 56px; object-fit: contain; border-radius: 50%; background: #fff; padding: 3px; box-shadow: 0 4px 10px rgba(124,58,237,0.16); margin-bottom: 8px;">
            <div><span class="school-badge">SMKN 5 MALANG</span></div>
            <h2>Reset Password Akun</h2>
            <p>Pilih role dan verifikasi identitas akunmu untuk membuat password baru</p>
        </div>

        <?php if (!empty($error_msg)) : ?>
            <div class="alert alert-danger"><?= $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Reset Password Sebagai</label>
                <select name="role" id="roleReset" onchange="toggleResetRole()" required>
                    <option value="siswa" <?= (($_POST['role'] ?? 'siswa') === 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                    <option value="pelatih" <?= (($_POST['role'] ?? '') === 'pelatih') ? 'selected' : ''; ?>>Pelatih / Pembina</option>
                    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label id="labelNisReset">Nomor Induk Siswa (NIS)</label>
                <input type="text" name="nis" id="inputNisReset" value="<?= htmlspecialchars($_POST['nis'] ?? ''); ?>" placeholder="Masukkan NIS kamu" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap (Sesuai Akun)</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" placeholder="Masukkan Nama Lengkap" required>
            </div>

            <div class="form-group" id="kelasResetWrapper">
                <label>Kelas</label>
                <select name="kelas" id="inputKelasReset" required>
                    <?php renderOpsiKelas($_POST['kelas'] ?? ''); ?>
                </select>
            </div>

            <div class="form-group">
                <label>Password Baru (Hanya Angka)</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="passBaruReset" name="password_baru" placeholder="Buat password angka baru" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required style="padding-right: 44px;">
                    <button type="button" onclick="togglePassField('passBaruReset')" title="Lihat/Sembunyikan Password" style="position: absolute; right: 6px; padding: 6px 10px; background: transparent; color: #64748b; box-shadow: none; font-size: 16px;">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="konfirmasiPassReset" name="konfirmasi_password" placeholder="Ketik ulang password baru" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required style="padding-right: 44px;">
                    <button type="button" onclick="togglePassField('konfirmasiPassReset')" title="Lihat/Sembunyikan Konfirmasi Password" style="position: absolute; right: 6px; padding: 6px 10px; background: transparent; color: #64748b; box-shadow: none; font-size: 16px;">👁️</button>
                </div>
            </div>

            <button type="submit" name="reset_password" class="btn btn-daftar btn-block">Simpan Password Baru</button>
        </form>

        <div class="auth-footer">
            Ingat password kamu? <a href="index.php">Kembali ke Login</a>
        </div>
    </div>

    <script>
    function toggleResetRole() {
        const role = document.getElementById('roleReset').value;
        const kelasWrapper = document.getElementById('kelasResetWrapper');
        const inputKelas = document.getElementById('inputKelasReset');
        const labelNis = document.getElementById('labelNisReset');
        const inputNis = document.getElementById('inputNisReset');

        if (role === 'siswa') {
            kelasWrapper.style.display = 'block';
            inputKelas.required = true;
            labelNis.textContent = 'Nomor Induk Siswa (NIS)';
            inputNis.placeholder = 'Masukkan NIS kamu';
        } else if (role === 'pelatih') {
            kelasWrapper.style.display = 'none';
            inputKelas.required = false;
            labelNis.textContent = 'NIP / ID Pelatih';
            inputNis.placeholder = 'Masukkan NIP atau ID Pelatih';
        } else {
            kelasWrapper.style.display = 'none';
            inputKelas.required = false;
            labelNis.textContent = 'NIP / ID Administrator';
            inputNis.placeholder = 'Masukkan NIP atau ID Admin';
        }
    }

    function togglePassField(id) {
        const input = document.getElementById(id);
        input.type = (input.type === 'password') ? 'text' : 'password';
    }

    toggleResetRole();
    </script>
    <script src="assets/animasi.js?v=2"></script>
</body>
</html>