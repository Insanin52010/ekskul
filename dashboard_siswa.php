<?php
require 'koneksi.php';
require 'kelas_options.php';
require_once 'backup_helper.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'siswa' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}

$is_admin_preview = ($_SESSION['role'] == 'admin');
$user_id = $_SESSION['user_id'];

if (!$is_admin_preview && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['update_profil']) || isset($_POST['auto_upload_foto']))) {
    $kelas_baru = mysqli_real_escape_string($conn, $_POST['kelas']);
    $jk_baru = !empty($_POST['jenis_kelamin']) ? "'" . mysqli_real_escape_string($conn, $_POST['jenis_kelamin']) . "'" : "NULL";
    $alamat_baru = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));
    $password_baru = trim($_POST['password_baru'] ?? '');

    $sql_foto = "";
    $sql_pass = "";
    $valid = true;

    if (!empty($password_baru)) {
        if (!ctype_digit($password_baru)) {
            $_SESSION['pesan_error'] = "⚠️ Password baru hanya boleh berisi angka saja (0-9)!";
            $valid = false;
        } else {
            $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            $plain_baru = mysqli_real_escape_string($conn, $password_baru);
            $sql_pass = ", password = '$hash_baru', password_plain = '$plain_baru'";
        }
    }

    if ($valid) {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir('assets/img')) {
                mkdir('assets/img', 0777, true);
            }

            $ekstensi = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
            $nama_file = time() . '_siswa_' . $user_id . '.' . strtolower($ekstensi);
            $tujuan = 'assets/img/' . $nama_file;

            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $tujuan)) {
                $sql_foto = ", foto_profil = '$nama_file'";
            }
        }

        mysqli_query($conn, "UPDATE users SET 
            kelas = '$kelas_baru', 
            jenis_kelamin = $jk_baru, 
            alamat = '$alamat_baru' 
            $sql_foto 
            $sql_pass 
            WHERE id = '$user_id'");

        exportDatabaseKeFile($conn);

        $_SESSION['pesan_sukses'] = !empty($password_baru) 
            ? "✨ Profil dan Password baru berhasil diperbarui!" 
            : "✨ Profil berhasil diperbarui!";
    }

    header("Location: dashboard_siswa.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['baca_notif'])) {
    $nid = mysqli_real_escape_string($conn, $_GET['baca_notif']);
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id = '$nid' AND user_id = '$user_id'");
    header("Location: dashboard_siswa.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['baca_semua_notif'])) {
    mysqli_query($conn, "DELETE FROM notifikasi WHERE user_id = '$user_id'");
    header("Location: dashboard_siswa.php");
    exit;
}

$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'"));

$ekskul_saya = mysqli_query($conn, "
    SELECT p.status, e.nama_ekskul, e.jadwal 
    FROM pendaftaran_ekskul p 
    JOIN ekskul e ON p.ekskul_id = e.id 
    WHERE p.siswa_id = '$user_id'
");

$semua_ekskul = mysqli_query($conn, "SELECT * FROM ekskul ORDER BY nama_ekskul ASC");

$foto_pp = (!empty($siswa['foto_profil']) && file_exists('assets/img/' . $siswa['foto_profil'])) 
    ? 'assets/img/' . htmlspecialchars($siswa['foto_profil']) 
    : 'https://ui-avatars.com/api/?name=' . urlencode($siswa['nama']) . '&background=4f46e5&color=fff&size=150';

$param_preview = $is_admin_preview ? '&preview_as=siswa' : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>

<div class="navbar">
    <div class="nav-brand">
        <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" class="nav-school-logo">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role"><?= $is_admin_preview ? 'Mode Pratinjau: Siswa' : 'Portal Siswa'; ?></span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <?php if ($is_admin_preview) : ?>
            <a href="dashboard_admin.php" style="background: #7c3aed; box-shadow: none;">← Kembali ke Admin</a>
        <?php endif; ?>
        <?php include 'notif_bell.php'; ?>
        <a href="logout.php" class="btn-logout">Keluar</a>
    </div>
</div>

<div class="container">

    <?php if ($is_admin_preview) : ?>
        <div class="alert" style="background: #fef9c3; color: #854d0e; border: 1.5px solid #fde047;">
            👀 <strong>Mode Pratinjau Admin:</strong> Kamu sedang melihat tampilan halaman dari sudut pandang <b>Siswa</b>.
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['pesan_sukses'])) : ?>
        <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['pesan_error'])) : ?>
        <div class="alert alert-danger"><?= $_SESSION['pesan_error']; ?></div>
        <?php unset($_SESSION['pesan_error']); ?>
    <?php endif; ?>

    <div class="dashboard-top-grid">
        <div class="card-box profile-card">
            <h2>Profil Siswa</h2>
            <form method="POST" action="" enctype="multipart/form-data" id="formProfilSiswa" class="profile-layout">
                <input type="hidden" name="auto_upload_foto" value="1">
                
                <div class="profile-avatar-section">
                    <img src="<?= $foto_pp; ?>" alt="Foto Profil" class="avatar-img">
                    <?php if (!$is_admin_preview) : ?>
                        <label class="file-upload-label">
                            📷 Ubah Foto
                            <input type="file" name="foto_profil" accept="image/*" hidden onchange="document.getElementById('formProfilSiswa').submit();">
                        </label>
                        <small class="text-muted" style="font-size: 11px; margin-top: 4px;">Otomatis tersimpan saat dipilih</small>
                    <?php endif; ?>
                </div>

                <div class="profile-fields">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" value="<?= htmlspecialchars($siswa['nama']); ?>" disabled class="input-locked">
                    </div>

                    <div class="form-group">
                        <label>Nomor Induk Siswa (NIS)</label>
                        <input type="text" value="<?= htmlspecialchars($siswa['nomor_induk']); ?>" disabled class="input-locked">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas" <?= $is_admin_preview ? 'disabled' : 'required'; ?>>
                                <?php renderOpsiKelas($siswa['kelas'] ?? ''); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" <?= $is_admin_preview ? 'disabled' : 'required'; ?>>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" <?= (($siswa['jenis_kelamin'] ?? '') == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= (($siswa['jenis_kelamin'] ?? '') == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" placeholder="Masukkan alamat rumah / tempat tinggal..." <?= $is_admin_preview ? 'disabled' : ''; ?>><?= htmlspecialchars($siswa['alamat'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>🔒 Password (Rahasia — Kosongkan jika tidak ingin mengganti)</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" id="passSiswa" name="password_baru" placeholder="•••••••• (Isi angka baru untuk ganti password)" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" <?= $is_admin_preview ? 'disabled' : ''; ?> style="padding-right: 44px;">
                            <button type="button" onclick="togglePassSiswa()" title="Lihat/Sembunyikan Password" style="position: absolute; right: 6px; padding: 6px 10px; background: transparent; color: #64748b; box-shadow: none; font-size: 16px;">👁️</button>
                        </div>
                    </div>

                    <?php if (!$is_admin_preview) : ?>
                        <button type="submit" name="update_profil" class="btn btn-daftar btn-block">💾 Simpan Perubahan Profil</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card-box">
            <h2>Ekstrakurikuler Saya</h2>
            <?php if (mysqli_num_rows($ekskul_saya) > 0) : ?>
                <div class="my-ekskul-list">
                    <?php while ($row = mysqli_fetch_assoc($ekskul_saya)) : ?>
                        <div class="my-ekskul-item">
                            <div>
                                <div class="my-ekskul-title"><?= htmlspecialchars($row['nama_ekskul']); ?></div>
                                <div class="my-ekskul-schedule">🕒 <?= $row['jadwal'] ? htmlspecialchars($row['jadwal']) : 'Jadwal belum diatur'; ?></div>
                            </div>
                            <span class="badge <?= $row['status']; ?>"><?= strtoupper($row['status']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <p>Kamu belum mendaftar ekstrakurikuler apa pun.</p>
                    <small class="text-muted">Pilih salah satu ekstrakurikuler di bawah untuk melihat detail dan mendaftar.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="section-title-bar">
        <h2>Daftar Ekstrakurikuler SMKN 5 Malang</h2>
        <p>Klik kartu ekstrakurikuler untuk melihat informasi lengkap, jadwal, pembina, dan prestasi.</p>
    </div>

    <div class="grid-ekskul">
        <?php while ($ekskul = mysqli_fetch_assoc($semua_ekskul)) : ?>
            <a href="detail_ekskul.php?id=<?= $ekskul['id'] . $param_preview; ?>" class="ekskul-card-link">
                <div class="ekskul-item">
                    <div class="ekskul-img-wrapper">
                        <img src="assets/img/<?= htmlspecialchars($ekskul['foto']); ?>" alt="<?= htmlspecialchars($ekskul['nama_ekskul']); ?>">
                        <span class="ekskul-tag">Lihat Detail →</span>
                    </div>
                    <div class="ekskul-info">
                        <h3><?= htmlspecialchars($ekskul['nama_ekskul']); ?></h3>
                        <p class="ekskul-meta">
                            <?= !empty($ekskul['jadwal']) ? '🗓️ ' . htmlspecialchars($ekskul['jadwal']) : '🗓️ Jadwal menyusul'; ?>
                        </p>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<script>
function togglePassSiswa() {
    const input = document.getElementById('passSiswa');
    input.type = (input.type === 'password') ? 'text' : 'password';
}
</script>
<script src="assets/animasi.js?v=2"></script>
</body>
</html>