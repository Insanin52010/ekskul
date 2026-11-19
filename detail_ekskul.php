<?php
require 'koneksi.php';
require_once 'backup_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$ekskul_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$preview_as = $_GET['preview_as'] ?? '';

if (isset($_GET['baca_notif'])) {
    $nid = mysqli_real_escape_string($conn, $_GET['baca_notif']);
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id = '$nid' AND user_id = '$user_id'");
    header("Location: detail_ekskul.php?id=$ekskul_id" . ($preview_as ? "&preview_as=$preview_as" : ""));
    exit;
}

if (isset($_GET['baca_semua_notif'])) {
    mysqli_query($conn, "DELETE FROM notifikasi WHERE user_id = '$user_id'");
    header("Location: detail_ekskul.php?id=$ekskul_id" . ($preview_as ? "&preview_as=$preview_as" : ""));
    exit;
}

if (isset($_POST['daftar_ekskul']) && $role === 'siswa') {
    $cek_daftar = mysqli_query($conn, "SELECT id FROM pendaftaran_ekskul WHERE siswa_id = '$user_id' AND ekskul_id = '$ekskul_id'");
    if (mysqli_num_rows($cek_daftar) === 0) {
        mysqli_query($conn, "INSERT INTO pendaftaran_ekskul (siswa_id, ekskul_id, status) VALUES ('$user_id', '$ekskul_id', 'pending')");

        $siswa_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, kelas FROM users WHERE id = '$user_id'"));
        $ekskul_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_ekskul, pelatih_id, status_pelatih_ekskul FROM ekskul WHERE id = '$ekskul_id'"));
        $pesan = mysqli_real_escape_string($conn, "Siswa baru " . $siswa_info['nama'] . " (" . $siswa_info['kelas'] . ") mendaftar di ekskul " . $ekskul_info['nama_ekskul'] . ".");

        // Kirim notifikasi ke pelatih (jika sudah aktif)
        if (!empty($ekskul_info['pelatih_id']) && ($ekskul_info['status_pelatih_ekskul'] ?? 'aktif') === 'aktif') {
            $pid = (int)$ekskul_info['pelatih_id'];
            mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$pid', '$pesan')");
        }

        // Kirim notifikasi juga ke semua Admin
        $admins = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin'");
        while ($adm = mysqli_fetch_assoc($admins)) {
            $aid = (int)$adm['id'];
            mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$aid', '$pesan')");
        }

        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "🎉 Pendaftaran berhasil dikirim! Menunggu konfirmasi dari pelatih atau admin.";
    }
    header("Location: detail_ekskul.php?id=$ekskul_id");
    exit;
}

$ekskul = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT e.*, u.nama AS nama_pelatih 
    FROM ekskul e 
    LEFT JOIN users u ON e.pelatih_id = u.id AND e.status_pelatih_ekskul = 'aktif'
    WHERE e.id = '$ekskul_id'
"));

if (!$ekskul) {
    header("Location: index.php");
    exit;
}

$foto_prestasi = mysqli_query($conn, "SELECT * FROM prestasi_foto WHERE ekskul_id = '$ekskul_id'");
$nama_pembina = !empty($ekskul['nama_pelatih']) ? $ekskul['nama_pelatih'] : (!empty($ekskul['nama_pembina']) ? $ekskul['nama_pembina'] : 'Belum diatur');

// Deteksi nomor WhatsApp otomatis jika kolom kontak berisi nomor telepon pengurus ekskul
$kontak_raw = trim((string)($ekskul['kontak'] ?? ''));
$wa_link = '';
if (!empty($kontak_raw) && preg_match('/(\+62|62|08)[0-9\-\s]{7,15}/', $kontak_raw, $match)) {
    $nomor_bersih = preg_replace('/[^0-9]/', '', $match[0]);
    if (strpos($nomor_bersih, '0') === 0) {
        $nomor_bersih = '62' . substr($nomor_bersih, 1);
    }
    $wa_link = 'https://wa.me/' . $nomor_bersih;
}

$status_daftar = null;
if ($role === 'siswa') {
    $q_status = mysqli_query($conn, "SELECT status FROM pendaftaran_ekskul WHERE siswa_id = '$user_id' AND ekskul_id = '$ekskul_id'");
    if (mysqli_num_rows($q_status) > 0) {
        $status_daftar = mysqli_fetch_assoc($q_status)['status'];
    }
}

$link_kembali = 'dashboard_siswa.php';
if ($role === 'pelatih') {
    $link_kembali = 'dashboard_pelatih.php';
} elseif ($role === 'admin') {
    if ($preview_as === 'siswa') {
        $link_kembali = 'dashboard_siswa.php?preview=1';
    } elseif ($preview_as === 'pelatih') {
        $link_kembali = 'dashboard_pelatih.php?preview=1';
    } else {
        $link_kembali = 'dashboard_admin.php';
    }
}

$is_admin_preview = ($role === 'admin' && !empty($preview_as));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ekskul['nama_ekskul']); ?> - SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>

<div class="navbar <?= $role === 'pelatih' ? 'pelatih' : ($role === 'admin' ? 'admin' : ''); ?>">
    <div class="nav-brand">
        <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" class="nav-school-logo">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role">Detail Ekstrakurikuler</span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="<?= $link_kembali; ?>" style="background: #64748b; box-shadow: none;">← Kembali</a>
        <?php include 'notif_bell.php'; ?>
        <a href="logout.php" class="btn-logout">Keluar</a>
    </div>
</div>

<div class="container">
    <?php if (isset($_SESSION['pesan_sukses'])) : ?>
        <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <div class="detail-hero">
        <img src="assets/img/<?= htmlspecialchars($ekskul['foto']); ?>" alt="<?= htmlspecialchars($ekskul['nama_ekskul']); ?>">
        <div class="detail-hero-overlay">
            <span class="hero-badge">EKSTRAKURIKULER SMKN 5 MALANG</span>
            <h1><?= htmlspecialchars($ekskul['nama_ekskul']); ?></h1>
        </div>
    </div>

    <div class="detail-grid">
        <div>
            <div class="card-box">
                <h2>📝 Tentang Ekstrakurikuler</h2>
                <p class="detail-text"><?= nl2br(htmlspecialchars($ekskul['deskripsi'] ?: 'Deskripsi ekstrakurikuler belum ditambahkan.')); ?></p>
            </div>

            <div class="card-box">
                <h2>🏆 Prestasi & Galeri Sertifikat</h2>
                <p class="detail-text" style="margin-bottom: 16px;"><?= nl2br(htmlspecialchars($ekskul['prestasi'] ?: 'Belum ada data prestasi yang dicatat.')); ?></p>

                <?php if (mysqli_num_rows($foto_prestasi) > 0) : ?>
                    <div class="sertifikat-gallery">
                        <?php while ($fp = mysqli_fetch_assoc($foto_prestasi)) : ?>
                            <div class="sertifikat-card">
                                <img src="assets/img/<?= htmlspecialchars($fp['nama_file']); ?>" alt="Sertifikat Prestasi" onclick="window.open(this.src, '_blank')" style="cursor: zoom-in;">
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div class="card-box">
                <h2>ℹ️ Informasi Kegiatan</h2>
                <div class="info-list">
                    <div class="info-list-item">
                        <span class="info-label">👨‍🏫 Pembina / Pelatih</span>
                        <span class="info-value"><?= htmlspecialchars($nama_pembina); ?></span>
                    </div>
                    <div class="info-list-item">
                        <span class="info-label">🗓️ Jadwal & Lokasi Latihan</span>
                        <span class="info-value"><?= htmlspecialchars($ekskul['jadwal'] ?: 'Jadwal menyusul'); ?></span>
                    </div>
                    <div class="info-list-item">
                        <span class="info-label">📞 Kontak Pengurus Ekskul</span>
                        <span class="info-value">
                            <?php if (!empty($kontak_raw)) : ?>
                                <?= htmlspecialchars($kontak_raw); ?>
                                <?php if (!empty($wa_link)) : ?>
                                    <div style="margin-top: 8px;">
                                        <a href="<?= htmlspecialchars($wa_link); ?>" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 6px; background: #16a34a; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700;">
                                            💬 Hubungi Pengurus via WhatsApp
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                Belum diatur
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-list-item">
                        <span class="info-label">🎒 Perlengkapan Dibawa</span>
                        <span class="info-value"><?= nl2br(htmlspecialchars($ekskul['perlengkapan'] ?: 'Tidak ada perlengkapan khusus')); ?></span>
                    </div>
                </div>

                <?php if ($role === 'siswa') : ?>
                    <div style="margin-top: 22px;">
                        <?php if ($status_daftar === 'diterima') : ?>
                            <div class="alert alert-success" style="text-align: center; margin: 0;">✓ Kamu sudah terdaftar sebagai Anggota Aktif</div>
                        <?php elseif ($status_daftar === 'pending') : ?>
                            <div class="alert" style="background: #fef9c3; color: #854d0e; border: 1.5px solid #fde047; text-align: center; margin: 0;">⏳ Pendaftaran sedang ditinjau Pelatih atau Admin</div>
                        <?php elseif ($status_daftar === 'ditolak') : ?>
                            <div class="alert alert-danger" style="text-align: center; margin: 0;">✕ Pendaftaran sebelumnya ditolak</div>
                        <?php else : ?>
                            <form method="POST" action="">
                                <button type="submit" name="daftar_ekskul" class="btn btn-daftar btn-block">✨ Daftar Ekskul Ini Sekarang</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/animasi.js?v=2"></script>
</body>
</html>