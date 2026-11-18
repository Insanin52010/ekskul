<?php
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role_aktif = $_SESSION['role'];
$is_admin_preview = ($role_aktif == 'admin');
$preview_as = $_GET['preview_as'] ?? '';

if (!isset($_GET['id'])) {
    header("Location: dashboard_siswa.php");
    exit;
}

$ekskul_id = mysqli_real_escape_string($conn, $_GET['id']);
$siswa_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT e.*, u.nama AS nama_pelatih 
    FROM ekskul e 
    LEFT JOIN users u ON e.pelatih_id = u.id 
    WHERE e.id = '$ekskul_id'
");
$ekskul = mysqli_fetch_assoc($query);

if (!$ekskul) {
    header("Location: dashboard_siswa.php");
    exit;
}

if ($role_aktif == 'siswa' && isset($_POST['daftar_ekskul'])) {
    $cek = mysqli_query($conn, "SELECT * FROM pendaftaran_ekskul WHERE siswa_id = '$siswa_id' AND ekskul_id = '$ekskul_id'");
    
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO pendaftaran_ekskul (siswa_id, ekskul_id, status) VALUES ('$siswa_id', '$ekskul_id', 'pending')");
        
        $data_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, kelas FROM users WHERE id = '$siswa_id'"));
        $nama_siswa = $data_siswa['nama'];
        $kelas_siswa = $data_siswa['kelas'];
        $nama_ekskul = $ekskul['nama_ekskul'];
        $pesan_notif = mysqli_real_escape_string($conn, "Siswa baru $nama_siswa ($kelas_siswa) mendaftar di ekskul $nama_ekskul.");

        if (!empty($ekskul['pelatih_id'])) {
            $pid = $ekskul['pelatih_id'];
            mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$pid', '$pesan_notif')");
        }

        $admins = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin'");
        while ($adm = mysqli_fetch_assoc($admins)) {
            $aid = $adm['id'];
            mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$aid', '$pesan_notif')");
        }

        $_SESSION['pesan_sukses'] = "🎉 Berhasil mendaftar di <b>$nama_ekskul</b>! Tunggu konfirmasi dari pelatih.";
    } else {
        $_SESSION['pesan_sukses'] = "⚠️ Kamu sudah terdaftar di ekstrakurikuler ini sebelumnya.";
    }
    header("Location: dashboard_siswa.php");
    exit;
}

$galeri_prestasi = mysqli_query($conn, "SELECT * FROM prestasi_foto WHERE ekskul_id = '$ekskul_id' ORDER BY id DESC");
$pembina_tampil = !empty($ekskul['nama_pelatih']) ? $ekskul['nama_pelatih'] : (!empty($ekskul['nama_pembina']) ? $ekskul['nama_pembina'] : 'Belum ditentukan');

// Tentukan tujuan tombol kembali sesuai mode pratinjau / role aktif
$link_kembali = 'dashboard_siswa.php';
$label_kembali = '← Kembali ke Dashboard';

if ($is_admin_preview) {
    if ($preview_as == 'pelatih') {
        $link_kembali = 'dashboard_pelatih.php?preview=1';
        $label_kembali = '← Kembali ke Pratinjau Pelatih';
    } else {
        $link_kembali = 'dashboard_siswa.php?preview=1';
        $label_kembali = '← Kembali ke Pratinjau Siswa';
    }
} elseif ($role_aktif == 'pelatih') {
    $link_kembali = 'dashboard_pelatih.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?= htmlspecialchars($ekskul['nama_ekskul']); ?> - SMKN 5 Malang</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="navbar">
    <div class="nav-brand">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role"><?= $is_admin_preview ? 'Mode Pratinjau: Detail Ekskul' : 'Detail Ekstrakurikuler'; ?></span>
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
            👀 <strong>Mode Pratinjau Admin:</strong> Kamu sedang melihat halaman detail ekstrakurikuler dari sudut pandang <b><?= ($preview_as == 'pelatih') ? 'Pelatih' : 'Siswa'; ?></b>.
        </div>
    <?php endif; ?>

    <div class="detail-card">
        <div class="detail-hero">
            <img src="assets/img/<?= htmlspecialchars($ekskul['foto']); ?>" alt="<?= htmlspecialchars($ekskul['nama_ekskul']); ?>">
            <div class="detail-hero-overlay">
                <span class="school-badge">EKSTRAKURIKULER SMKN 5 MALANG</span>
                <h1><?= htmlspecialchars($ekskul['nama_ekskul']); ?></h1>
            </div>
        </div>

        <div class="detail-body">
            <div class="info-pills">
                <div class="info-pill">
                    <span class="pill-icon">👨‍🏫</span>
                    <div>
                        <small>Pembina / Pelatih</small>
                        <strong><?= htmlspecialchars($pembina_tampil); ?></strong>
                    </div>
                </div>
                <div class="info-pill">
                    <span class="pill-icon">🗓️</span>
                    <div>
                        <small>Jadwal Latihan</small>
                        <strong><?= !empty($ekskul['jadwal']) ? htmlspecialchars($ekskul['jadwal']) : 'Menyesuaikan jadwal sekolah'; ?></strong>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3>📝 Tentang Ekstrakurikuler</h3>
                <p class="detail-desc"><?= nl2br(htmlspecialchars($ekskul['deskripsi'])); ?></p>
            </div>

            <div class="detail-section">
                <h3>🎒 Perlengkapan yang Perlu Dibawa</h3>
                <div class="detail-box-muted">
                    <?= !empty($ekskul['perlengkapan']) ? nl2br(htmlspecialchars($ekskul['perlengkapan'])) : 'Belum ada catatan perlengkapan khusus.'; ?>
                </div>
            </div>

            <div class="detail-section">
                <h3>🏆 Prestasi & Penghargaan</h3>
                <div class="detail-box-gold">
                    <?= !empty($ekskul['prestasi']) ? nl2br(htmlspecialchars($ekskul['prestasi'])) : 'Belum ada data prestasi yang ditambahkan.'; ?>
                </div>

                <?php if (mysqli_num_rows($galeri_prestasi) > 0) : ?>
                    <h4 style="margin: 18px 0 10px 0; font-size: 15px; color: #334155;">📜 Galeri Foto Sertifikat & Piala</h4>
                    <div class="cert-grid">
                        <?php while ($gp = mysqli_fetch_assoc($galeri_prestasi)) : ?>
                            <a href="assets/img/<?= htmlspecialchars($gp['nama_file']); ?>" target="_blank" class="cert-item">
                                <img src="assets/img/<?= htmlspecialchars($gp['nama_file']); ?>" alt="Sertifikat Prestasi">
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-actions">
                <a href="<?= $link_kembali; ?>" class="btn btn-kembali"><?= $label_kembali; ?></a>
                
                <?php if ($role_aktif == 'siswa') : ?>
                    <form method="POST" action="" style="margin: 0;">
                        <button type="submit" name="daftar_ekskul" class="btn btn-daftar">✓ Daftar Ekskul Ini</button>
                    </form>
                <?php elseif ($is_admin_preview && $preview_as != 'pelatih') : ?>
                    <button type="button" class="btn btn-daftar" onclick="alert('Ini adalah tombol Daftar Ekskul yang dilihat oleh Siswa (nonaktif di mode pratinjau).')">✓ Daftar Ekskul Ini (Pratinjau)</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/animasi.js"></script>
</body>
</html>