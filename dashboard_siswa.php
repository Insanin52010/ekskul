<?php
require 'koneksi.php';
require 'kelas_options.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'siswa' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}

$is_admin_preview = ($_SESSION['role'] == 'admin');
$user_id = $_SESSION['user_id'];

if (!$is_admin_preview && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['update_profil']) || isset($_POST['auto_upload_foto']))) {
    $kelas_baru = mysqli_real_escape_string($conn, $_POST['kelas']);
    $sql_foto = "";

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

    mysqli_query($conn, "UPDATE users SET kelas = '$kelas_baru' $sql_foto WHERE id = '$user_id'");
    $_SESSION['pesan_sukses'] = "✨ Profil berhasil diperbarui!";
    header("Location: dashboard_siswa.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['baca_notif'])) {
    $nid = $_GET['baca_notif'];
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id = '$nid' AND user_id = '$user_id'");
    header("Location: dashboard_siswa.php");
    exit;
}

$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'"));
$notifikasi = mysqli_query($conn, "SELECT * FROM notifikasi WHERE user_id = '$user_id' ORDER BY waktu DESC");

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
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="navbar">
    <div class="nav-brand">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role"><?= $is_admin_preview ? 'Mode Pratinjau: Siswa' : 'Portal Siswa'; ?></span>
    </div>
    <div style="display: flex; gap: 10px;">
        <?php if ($is_admin_preview) : ?>
            <a href="dashboard_admin.php" style="background: #7c3aed; box-shadow: none;">← Kembali ke Admin</a>
        <?php endif; ?>
        <a href="logout.php" class="btn-logout">Keluar</a>
    </div>
</div>

<div class="container">

    <?php if ($is_admin_preview) : ?>
        <div class="alert" style="background: #fef9c3; color: #854d0e; border: 1.5px solid #fde047;">
            👀 <strong>Mode Pratinjau Admin:</strong> Kamu sedang melihat tampilan halaman dari sudut pandang <b>Siswa</b>. Klik kartu ekskul di bawah untuk melihat detailnya.
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['pesan_sukses'])) : ?>
        <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <?php if (!$is_admin_preview && mysqli_num_rows($notifikasi) > 0) : ?>
        <div class="notif-box">
            <div class="notif-header">🔔 Notifikasi Terbaru (<?= mysqli_num_rows($notifikasi); ?>)</div>
            <?php while ($n = mysqli_fetch_assoc($notifikasi)) : ?>
                <div class="notif-item">
                    <span><?= htmlspecialchars($n['pesan']); ?> <small class="text-muted">(<?= $n['waktu']; ?>)</small></span>
                    <a href="?baca_notif=<?= $n['id']; ?>" class="notif-close">Tutup ✕</a>
                </div>
            <?php endwhile; ?>
        </div>
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

                    <div class="form-group">
                        <label>Kelas (Dapat diubah jika salah pilih)</label>
                        <div style="display: flex; gap: 10px;">
                            <select name="kelas" <?= $is_admin_preview ? 'disabled' : 'required'; ?> style="margin: 0;">
                                <?php renderOpsiKelas($siswa['kelas'] ?? ''); ?>
                            </select>
                            <?php if (!$is_admin_preview) : ?>
                                <button type="submit" name="update_profil" class="btn btn-daftar" style="white-space: nowrap;">Simpan</button>
                            <?php endif; ?>
                        </div>
                    </div>
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

<script src="assets/animasi.js"></script>
</body>
</html>