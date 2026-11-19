<?php
require 'koneksi.php';
require 'kelas_options.php';

$error_msg = '';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $kelas = ($role == 'siswa' && !empty($_POST['kelas'])) ? "'" . mysqli_real_escape_string($conn, $_POST['kelas']) . "'" : "NULL";
    $jenis_kelamin = ($role == 'siswa' && !empty($_POST['jenis_kelamin'])) ? "'" . mysqli_real_escape_string($conn, $_POST['jenis_kelamin']) . "'" : "NULL";
    $alamat = ($role == 'siswa' && !empty($_POST['alamat'])) ? "'" . mysqli_real_escape_string($conn, trim($_POST['alamat'])) . "'" : "NULL";
    $password_mentah = trim($_POST['password']);

    if (!in_array($role, ['siswa', 'pelatih', 'admin'])) {
        $error_msg = "Role tidak valid!";
    } elseif (!ctype_digit($password_mentah)) {
        $error_msg = "Password hanya boleh diisi dengan angka (0-9)!";
    } else {
        $cek_nis = mysqli_query($conn, "SELECT id FROM users WHERE nomor_induk = '$nis'");
        if (mysqli_num_rows($cek_nis) > 0) {
            $error_msg = "NIS / NIP tersebut sudah terdaftar! Silakan gunakan akun yang ada atau langsung masuk.";
        } else {
            $password_hash = password_hash($password_mentah, PASSWORD_DEFAULT);
            $password_plain = ($role === 'admin') ? "NULL" : "'" . mysqli_real_escape_string($conn, $password_mentah) . "'";
            $status_akun = 'aktif';

            $query = "INSERT INTO users (nama, nomor_induk, kelas, jenis_kelamin, alamat, role, status_akun, password, password_plain) 
                      VALUES ('$nama', '$nis', $kelas, $jenis_kelamin, $alamat, '$role', '$status_akun', '$password_hash', $password_plain)";

            if (mysqli_query($conn, $query)) {
                if ($role == 'pelatih') {
                    $_SESSION['pesan_sukses'] = "✅ Pendaftaran Pelatih berhasil! Silakan login lalu pilih ekstrakurikuler yang ingin Anda latih.";
                } elseif ($role == 'admin') {
                    $_SESSION['pesan_sukses'] = "✅ Pendaftaran Administrator berhasil! Silakan login menggunakan NIP/ID dan passwordmu.";
                } else {
                    $_SESSION['pesan_sukses'] = "✅ Pendaftaran Siswa berhasil! Silakan login dengan NIS dan passwordmu.";
                }
                header("Location: index.php");
                exit;
            } else {
                $error_msg = "Terjadi kesalahan saat menyimpan data: " . mysqli_error($conn);
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
    <title>Daftar Akun - Ekskul SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body class="auth-body">
    <div class="auth-card" style="max-width: 480px;">
        <div class="auth-header">
            <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" style="width: 56px; height: 56px; object-fit: contain; border-radius: 50%; background: #fff; padding: 3px; box-shadow: 0 4px 10px rgba(124,58,237,0.16); margin-bottom: 8px;">
            <div><span class="school-badge">SMKN 5 MALANG</span></div>
            <h2>Daftar Akun Baru</h2>
            <p>Buat akun untuk bergabung dan mengelola ekstrakurikuler</p>
        </div>

        <?php if (!empty($error_msg)) : ?>
            <div class="alert alert-danger"><?= $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Daftar Sebagai</label>
                <select name="role" id="roleSelect" onchange="toggleFieldSiswa()" required>
                    <option value="siswa">Siswa</option>
                    <option value="pelatih">Pelatih / Pembina</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label id="labelNis">Nomor Induk Siswa (NIS)</label>
                <input type="text" name="nis" id="inputNis" placeholder="Masukkan NIS" required>
            </div>

            <div id="siswaFields">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas" id="inputKelas" required>
                            <?php renderOpsiKelas(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="inputJk" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" id="inputAlamat" rows="2" placeholder="Masukkan alamat lengkap tempat tinggal..." required></textarea>
                </div>
            </div>

            <div class="form-group">
                <label>Password (Hanya Angka)</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="passReg" name="password" placeholder="Buat password angka (0-9)" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required style="padding-right: 44px;">
                    <button type="button" onclick="togglePassReg()" title="Lihat/Sembunyikan Password" style="position: absolute; right: 6px; padding: 6px 10px; background: transparent; color: #64748b; box-shadow: none; font-size: 16px;">👁️</button>
                </div>
            </div>

            <button type="submit" name="register" class="btn btn-daftar btn-block">Daftar Sekarang</button>
        </form>

        <div class="auth-footer">
            Sudah memiliki akun? <a href="index.php">Masuk di sini</a>
        </div>
    </div>

    <script>
    function toggleFieldSiswa() {
        const role = document.getElementById('roleSelect').value;
        const siswaFields = document.getElementById('siswaFields');
        const labelNis = document.getElementById('labelNis');
        const inputNis = document.getElementById('inputNis');
        const inputKelas = document.getElementById('inputKelas');
        const inputJk = document.getElementById('inputJk');
        const inputAlamat = document.getElementById('inputAlamat');

        if (role === 'siswa') {
            siswaFields.style.display = 'block';
            labelNis.textContent = 'Nomor Induk Siswa (NIS)';
            inputNis.placeholder = 'Masukkan NIS';
            inputKelas.required = true;
            inputJk.required = true;
            inputAlamat.required = true;
        } else if (role === 'pelatih') {
            siswaFields.style.display = 'none';
            labelNis.textContent = 'NIP / ID Pelatih';
            inputNis.placeholder = 'Masukkan NIP atau ID Pelatih';
            inputKelas.required = false;
            inputJk.required = false;
            inputAlamat.required = false;
        } else {
            siswaFields.style.display = 'none';
            labelNis.textContent = 'NIP / ID Administrator';
            inputNis.placeholder = 'Masukkan NIP atau ID Admin';
            inputKelas.required = false;
            inputJk.required = false;
            inputAlamat.required = false;
        }
    }

    function togglePassReg() {
        const input = document.getElementById('passReg');
        input.type = (input.type === 'password') ? 'text' : 'password';
    }
    </script>
    <script src="assets/animasi.js?v=2"></script>
</body>
</html>