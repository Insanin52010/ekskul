<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/backup_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

if (isset($_GET['baca_notif'])) {
    $nid = mysqli_real_escape_string($conn, $_GET['baca_notif']);
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id = '$nid' AND user_id = '$admin_id'");
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['baca_semua_notif'])) {
    mysqli_query($conn, "DELETE FROM notifikasi WHERE user_id = '$admin_id'");
    header("Location: dashboard_admin.php");
    exit;
}

// ACC Pengajuan Pelatih untuk Ekskul Tertentu
if (isset($_GET['acc_ekskul_pelatih'])) {
    $eid = mysqli_real_escape_string($conn, $_GET['acc_ekskul_pelatih']);
    $eks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_ekskul, pelatih_id FROM ekskul WHERE id = '$eid' AND status_pelatih_ekskul = 'pending'"));

    if ($eks && !empty($eks['pelatih_id'])) {
        $pid = (int)$eks['pelatih_id'];
        $nama_eks = $eks['nama_ekskul'];
        mysqli_query($conn, "UPDATE ekskul SET status_pelatih_ekskul = 'aktif' WHERE id = '$eid'");

        $pesan_pelatih = mysqli_real_escape_string($conn, "🎉 Selamat! Pengajuan Anda sebagai Pelatih ekstrakurikuler $nama_eks telah DITERIMA (di-ACC) oleh Admin.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$pid', '$pesan_pelatih')");

        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "✅ Pengajuan pelatih untuk ekstrakurikuler <b>$nama_eks</b> berhasil di-ACC!";
    }
    header("Location: dashboard_admin.php");
    exit;
}

// Tolak Pengajuan Pelatih untuk Ekskul Tertentu
if (isset($_GET['tolak_ekskul_pelatih'])) {
    $eid = mysqli_real_escape_string($conn, $_GET['tolak_ekskul_pelatih']);
    $eks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_ekskul, pelatih_id FROM ekskul WHERE id = '$eid' AND status_pelatih_ekskul = 'pending'"));

    if ($eks && !empty($eks['pelatih_id'])) {
        $pid = (int)$eks['pelatih_id'];
        $nama_eks = $eks['nama_ekskul'];
        mysqli_query($conn, "UPDATE ekskul SET pelatih_id = NULL, status_pelatih_ekskul = 'aktif' WHERE id = '$eid'");

        $pesan_pelatih = mysqli_real_escape_string($conn, "Mohon maaf, pengajuan Anda sebagai Pelatih ekstrakurikuler $nama_eks DITOLAK oleh Admin.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$pid', '$pesan_pelatih')");

        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "Pengajuan pelatih untuk ekstrakurikuler <b>$nama_eks</b> telah ditolak.";
    }
    header("Location: dashboard_admin.php");
    exit;
}

// Admin ACC Pendaftaran Siswa ke Ekskul
if (isset($_GET['acc_siswa'])) {
    $daftar_id = mysqli_real_escape_string($conn, $_GET['acc_siswa']);
    $info = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.siswa_id, e.nama_ekskul, u.nama AS nama_siswa 
        FROM pendaftaran_ekskul p 
        JOIN ekskul e ON p.ekskul_id = e.id 
        JOIN users u ON p.siswa_id = u.id 
        WHERE p.id = '$daftar_id'
    "));

    if ($info) {
        mysqli_query($conn, "UPDATE pendaftaran_ekskul SET status = 'diterima' WHERE id = '$daftar_id'");
        $sid = (int)$info['siswa_id'];
        $pesan = mysqli_real_escape_string($conn, "🎉 Selamat! Pendaftaran kamu di ekstrakurikuler " . $info['nama_ekskul'] . " telah DITERIMA oleh Admin.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$sid', '$pesan')");
        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "✅ Siswa <b>" . htmlspecialchars($info['nama_siswa']) . "</b> berhasil diterima di ekstrakurikuler <b>" . htmlspecialchars($info['nama_ekskul']) . "</b>!";
    }
    header("Location: dashboard_admin.php");
    exit;
}

// Admin Tolak Pendaftaran Siswa ke Ekskul
if (isset($_GET['tolak_siswa'])) {
    $daftar_id = mysqli_real_escape_string($conn, $_GET['tolak_siswa']);
    $info = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.siswa_id, e.nama_ekskul, u.nama AS nama_siswa 
        FROM pendaftaran_ekskul p 
        JOIN ekskul e ON p.ekskul_id = e.id 
        JOIN users u ON p.siswa_id = u.id 
        WHERE p.id = '$daftar_id'
    "));

    if ($info) {
        mysqli_query($conn, "UPDATE pendaftaran_ekskul SET status = 'ditolak' WHERE id = '$daftar_id'");
        $sid = (int)$info['siswa_id'];
        $pesan = mysqli_real_escape_string($conn, "Mohon maaf, pendaftaran kamu di ekstrakurikuler " . $info['nama_ekskul'] . " DITOLAK oleh Admin.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$sid', '$pesan')");
        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "Pendaftaran siswa <b>" . htmlspecialchars($info['nama_siswa']) . "</b> telah ditolak.";
    }
    header("Location: dashboard_admin.php");
    exit;
}

// Admin Hapus Data Pendaftaran Siswa
if (isset($_GET['hapus_pendaftaran'])) {
    $daftar_id = mysqli_real_escape_string($conn, $_GET['hapus_pendaftaran']);
    mysqli_query($conn, "DELETE FROM pendaftaran_ekskul WHERE id = '$daftar_id'");
    exportDatabaseKeFile($conn);
    $_SESSION['pesan_sukses'] = "🗑️ Data pendaftaran siswa berhasil dihapus.";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_POST['tambah_ekskul'])) {
    $nama_ekskul = mysqli_real_escape_string($conn, strtoupper(trim($_POST['nama_ekskul'])));
    $nama_pembina = mysqli_real_escape_string($conn, trim($_POST['nama_pembina'] ?: 'Belum diatur'));
    $jadwal = mysqli_real_escape_string($conn, trim($_POST['jadwal']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $perlengkapan = mysqli_real_escape_string($conn, trim($_POST['perlengkapan']));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));
    $foto = 'vohisma.jpg';

    if (!empty($_FILES['file_foto_baru']['name'])) {
        $nama_file = time() . '_' . $_FILES['file_foto_baru']['name'];
        move_uploaded_file($_FILES['file_foto_baru']['tmp_name'], 'assets/img/' . $nama_file);
        $foto = $nama_file;
    }

    mysqli_query($conn, "INSERT INTO ekskul (nama_ekskul, nama_pembina, jadwal, deskripsi, perlengkapan, kontak, foto) 
                         VALUES ('$nama_ekskul', '$nama_pembina', '$jadwal', '$deskripsi', '$perlengkapan', '$kontak', '$foto')");
    exportDatabaseKeFile($conn);

    $_SESSION['pesan_sukses'] = "🎉 Ekstrakurikuler baru <b>$nama_ekskul</b> berhasil ditambahkan dan disimpan permanen!";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['hapus_ekskul'])) {
    $eid = mysqli_real_escape_string($conn, $_GET['hapus_ekskul']);
    mysqli_query($conn, "DELETE FROM prestasi_foto WHERE ekskul_id = '$eid'");
    mysqli_query($conn, "DELETE FROM pendaftaran_ekskul WHERE ekskul_id = '$eid'");
    mysqli_query($conn, "DELETE FROM ekskul WHERE id = '$eid'");
    exportDatabaseKeFile($conn);

    $_SESSION['pesan_sukses'] = "🗑️ Ekstrakurikuler berhasil dihapus.";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_POST['update_ekskul'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $jadwal_baru_raw = trim($_POST['jadwal']);
    $perlengkapan_baru_raw = trim($_POST['perlengkapan']);
    $prestasi_baru_raw = trim($_POST['prestasi']);
    $kontak_baru_raw = trim($_POST['kontak'] ?? '');
    $jadwal = mysqli_real_escape_string($conn, $jadwal_baru_raw);
    $prestasi = mysqli_real_escape_string($conn, $prestasi_baru_raw);
    $perlengkapan = mysqli_real_escape_string($conn, $perlengkapan_baru_raw);
    $kontak = mysqli_real_escape_string($conn, $kontak_baru_raw);
    $foto = mysqli_real_escape_string($conn, $_POST['foto']);

    $data_lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_ekskul, jadwal, perlengkapan, prestasi, pelatih_id, status_pelatih_ekskul FROM ekskul WHERE id = '$id'"));

    if (!empty($_FILES['file_foto']['name'])) {
        $nama_file = time() . '_' . $_FILES['file_foto']['name'];
        move_uploaded_file($_FILES['file_foto']['tmp_name'], 'assets/img/' . $nama_file);
        $foto = $nama_file;
    }

    $jumlah_sertif_baru = 0;
    if (!empty($_FILES['foto_prestasi']['name'][0])) {
        $jumlah_file = count($_FILES['foto_prestasi']['name']);
        for ($i = 0; $i < $jumlah_file; $i++) {
            $nama_asli = $_FILES['foto_prestasi']['name'][$i];
            $tmp_name = $_FILES['foto_prestasi']['tmp_name'][$i];
            
            if (!empty($nama_asli)) {
                $nama_baru = time() . '_prestasi_' . $i . '_' . $nama_asli;
                move_uploaded_file($tmp_name, 'assets/img/' . $nama_baru);
                mysqli_query($conn, "INSERT INTO prestasi_foto (ekskul_id, nama_file) VALUES ('$id', '$nama_baru')");
                $jumlah_sertif_baru++;
            }
        }
    }

    $query = "UPDATE ekskul SET 
              deskripsi = '$deskripsi', 
              jadwal = '$jadwal', 
              prestasi = '$prestasi', 
              perlengkapan = '$perlengkapan', 
              kontak = '$kontak', 
              foto = '$foto' 
              WHERE id = '$id'";
    mysqli_query($conn, $query);

    if ($data_lama) {
        $nama_ekskul = $data_lama['nama_ekskul'];
        $jadwal_berubah = (trim((string)$data_lama['jadwal']) !== $jadwal_baru_raw);
        $barang_berubah = (trim((string)$data_lama['perlengkapan']) !== $perlengkapan_baru_raw);
        $prestasi_teks_berubah = (!empty($prestasi_baru_raw) && trim((string)$data_lama['prestasi']) !== $prestasi_baru_raw);

        $daftar_pesan_anggota = [];
        if ($jadwal_berubah) {
            $daftar_pesan_anggota[] = "🗓️ Perubahan Jadwal ekskul $nama_ekskul menjadi: $jadwal_baru_raw";
        }
        if ($barang_berubah) {
            $daftar_pesan_anggota[] = "🎒 Perubahan Perlengkapan / Barang yang Perlu Dibawa pada ekskul $nama_ekskul: $perlengkapan_baru_raw";
        }

        if (!empty($daftar_pesan_anggota)) {
            $penerima_ids = [];
            if (!empty($data_lama['pelatih_id']) && ($data_lama['status_pelatih_ekskul'] ?? 'aktif') === 'aktif') {
                $penerima_ids[] = (int)$data_lama['pelatih_id'];
            }
            $q_siswa = mysqli_query($conn, "SELECT DISTINCT siswa_id FROM pendaftaran_ekskul WHERE ekskul_id = '$id' AND status IN ('diterima', 'pending')");
            while ($sw = mysqli_fetch_assoc($q_siswa)) {
                $penerima_ids[] = (int)$sw['siswa_id'];
            }
            $penerima_ids = array_unique($penerima_ids);

            foreach ($penerima_ids as $uid_target) {
                foreach ($daftar_pesan_anggota as $teks_notif) {
                    $teks_safe = mysqli_real_escape_string($conn, $teks_notif);
                    mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$uid_target', '$teks_safe')");
                }
            }
        }

        if ($prestasi_teks_berubah || $jumlah_sertif_baru > 0) {
            if ($prestasi_teks_berubah && $jumlah_sertif_baru > 0) {
                $pesan_prestasi = "🏆 Selamat! Ekstrakurikuler $nama_ekskul baru saja meraih prestasi/penghargaan ($prestasi_baru_raw) dan mengunggah $jumlah_sertif_baru sertifikat/piala baru!";
            } elseif ($jumlah_sertif_baru > 0) {
                $pesan_prestasi = "🏆 Selamat! Ekstrakurikuler $nama_ekskul baru saja menambahkan $jumlah_sertif_baru foto sertifikat/piala penghargaan baru!";
            } else {
                $pesan_prestasi = "🏆 Prestasi Baru! Ekstrakurikuler $nama_ekskul meraih penghargaan: $prestasi_baru_raw";
            }

            $pesan_prestasi_safe = mysqli_real_escape_string($conn, $pesan_prestasi);
            $semua_user_target = mysqli_query($conn, "SELECT id FROM users WHERE role IN ('siswa', 'pelatih')");
            while ($usr = mysqli_fetch_assoc($semua_user_target)) {
                $uid_all = (int)$usr['id'];
                mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$uid_all', '$pesan_prestasi_safe')");
            }
        }
    }

    exportDatabaseKeFile($conn);

    $_SESSION['pesan_sukses'] = "✨ Mantap! Perubahan data ekstrakurikuler berhasil disimpan permanen & notifikasi terkirim.";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['hapus_foto_prestasi'])) {
    $fid = mysqli_real_escape_string($conn, $_GET['hapus_foto_prestasi']);
    $data_foto = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_file FROM prestasi_foto WHERE id = '$fid'"));
    if ($data_foto) {
        $path = 'assets/img/' . $data_foto['nama_file'];
        if (file_exists($path)) {
            unlink($path);
        }
        mysqli_query($conn, "DELETE FROM prestasi_foto WHERE id = '$fid'");
    }

    exportDatabaseKeFile($conn);

    $_SESSION['pesan_sukses'] = "🗑️ Foto sertifikat berhasil dihapus.";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['hapus_user'])) {
    $uid = mysqli_real_escape_string($conn, $_GET['hapus_user']);
    mysqli_query($conn, "UPDATE ekskul SET pelatih_id = NULL, status_pelatih_ekskul = 'aktif' WHERE pelatih_id = '$uid'");
    mysqli_query($conn, "DELETE FROM pendaftaran_ekskul WHERE siswa_id = '$uid'");
    mysqli_query($conn, "DELETE FROM users WHERE id = '$uid'");
    exportDatabaseKeFile($conn);
    $_SESSION['pesan_sukses'] = "🗑️ Akun pengguna berhasil dihapus.";
    header("Location: dashboard_admin.php");
    exit;
}

$jml_ekskul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ekskul"))['total'];
$jml_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'siswa'"))['total'];
$jml_pelatih = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'pelatih'"))['total'];
$jml_daftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pendaftaran_ekskul"))['total'];

$pengajuan_pelatih = mysqli_query($conn, "
    SELECT e.id AS ekskul_id, e.nama_ekskul, u.nama AS nama_pelatih, u.nomor_induk, u.foto_profil 
    FROM ekskul e 
    JOIN users u ON e.pelatih_id = u.id 
    WHERE e.status_pelatih_ekskul = 'pending' 
    ORDER BY e.nama_ekskul ASC
");

$semua_pendaftaran = mysqli_query($conn, "
    SELECT p.id AS daftar_id, p.status, u.nama AS nama_siswa, u.nomor_induk, u.kelas, u.foto_profil, e.nama_ekskul 
    FROM pendaftaran_ekskul p 
    JOIN users u ON p.siswa_id = u.id 
    JOIN ekskul e ON p.ekskul_id = e.id 
    ORDER BY FIELD(p.status, 'pending', 'diterima', 'ditolak'), p.id DESC
");

$semua_ekskul = mysqli_query($conn, "SELECT e.*, u.nama AS nama_pelatih FROM ekskul e LEFT JOIN users u ON e.pelatih_id = u.id AND e.status_pelatih_ekskul = 'aktif' ORDER BY e.nama_ekskul ASC");
$semua_user = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Space - SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>

<div class="navbar admin">
    <div class="nav-brand">
        <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" class="nav-school-logo">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role">Admin Command Center</span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="dashboard_siswa.php?preview=1" style="background: linear-gradient(135deg, #60a5fa, #8b5cf6); box-shadow: none;">👀 Pratinjau Siswa</a>
        <a href="dashboard_pelatih.php?preview=1" style="background: linear-gradient(135deg, #fde047, #facc15); color: #422006; box-shadow: none;">👀 Pratinjau Pelatih</a>
        <?php include 'notif_bell.php'; ?>
        <a href="logout.php" class="btn-logout">Keluar</a>
    </div>
</div>

<div class="container">

    <?php if (isset($_SESSION['pesan_sukses'])) : ?>
        <div class="alert alert-success"><?= $_SESSION['pesan_sukses']; ?></div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <div class="admin-stats-grid">
        <div class="stat-bento">
            <div class="stat-icon purple">🎯</div>
            <div class="stat-info">
                <h3><?= $jml_ekskul; ?></h3>
                <p>Total Ekskul</p>
            </div>
        </div>
        <div class="stat-bento">
            <div class="stat-icon cyan">🎓</div>
            <div class="stat-info">
                <h3><?= $jml_siswa; ?></h3>
                <p>Siswa Terdaftar</p>
            </div>
        </div>
        <div class="stat-bento">
            <div class="stat-icon pink">⚡</div>
            <div class="stat-info">
                <h3><?= $jml_pelatih; ?></h3>
                <p>Pelatih / Pembina</p>
            </div>
        </div>
        <div class="stat-bento">
            <div class="stat-icon amber">🔥</div>
            <div class="stat-info">
                <h3><?= $jml_daftar; ?></h3>
                <p>Total Pendaftar</p>
            </div>
        </div>
    </div>

    <?php if (mysqli_num_rows($pengajuan_pelatih) > 0) : ?>
        <div class="card-box" style="border: 2px solid #fde047; background: #fffbeb;">
            <h2 style="color: #92400e;">⏳ Pengajuan Tugas Melatih Ekstrakurikuler (<?= mysqli_num_rows($pengajuan_pelatih); ?>)</h2>
            <div style="overflow-x: auto;">
                <table>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Pelatih</th>
                        <th>NIP / ID</th>
                        <th>Ekskul yang Diajukan</th>
                        <th>Aksi</th>
                    </tr>
                    <?php while ($pp = mysqli_fetch_assoc($pengajuan_pelatih)) : ?>
                        <?php 
                        $foto_pp_adm = (!empty($pp['foto_profil']) && file_exists('assets/img/' . $pp['foto_profil'])) 
                            ? 'assets/img/' . htmlspecialchars($pp['foto_profil']) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($pp['nama_pelatih']) . '&background=d97706&color=fff';
                        ?>
                        <tr>
                            <td width="56">
                                <img src="<?= $foto_pp_adm; ?>" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td><strong><?= htmlspecialchars($pp['nama_pelatih']); ?></strong></td>
                            <td><?= htmlspecialchars($pp['nomor_induk']); ?></td>
                            <td><span class="badge pending">🎯 <?= htmlspecialchars($pp['nama_ekskul']); ?></span></td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <a href="?acc_ekskul_pelatih=<?= $pp['ekskul_id']; ?>" class="btn btn-daftar" style="padding: 6px 14px; font-size: 12px; border-radius: 50px;">✓ ACC Melatih</a>
                                    <a href="?tolak_ekskul_pelatih=<?= $pp['ekskul_id']; ?>" class="btn btn-del" style="padding: 6px 14px; font-size: 12px; border-radius: 50px;" onclick="return confirm('Tolak pengajuan pelatih ini?')">✕ Tolak</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 14px;">
            <div>
                <h2 style="margin-bottom: 4px; border: none; padding: 0;">Kelola Pendaftaran Siswa ke Ekskul (<?= mysqli_num_rows($semua_pendaftaran); ?>)</h2>
                <p class="text-muted" style="font-size: 13px;">Admin dapat langsung menerima (ACC) atau menolak siswa yang mendaftar ekstrakurikuler.</p>
            </div>
            <input type="text" id="searchPendaftar" onkeyup="filterPendaftar()" placeholder="🔍 Cari nama siswa / ekskul..." style="max-width: 260px; margin: 0; border-radius: 50px; padding: 9px 16px;">
        </div>
        <div style="overflow-x: auto;">
            <table id="tabelPendaftar">
                <tr>
                    <th>Foto</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Ekskul Tujuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php if (mysqli_num_rows($semua_pendaftaran) > 0) : ?>
                    <?php while ($pd = mysqli_fetch_assoc($semua_pendaftaran)) : ?>
                        <?php 
                        $foto_pd = (!empty($pd['foto_profil']) && file_exists('assets/img/' . $pd['foto_profil'])) 
                            ? 'assets/img/' . htmlspecialchars($pd['foto_profil']) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($pd['nama_siswa']) . '&background=4f46e5&color=fff';
                        ?>
                        <tr class="row-pendaftar" data-cari="<?= strtolower(htmlspecialchars($pd['nama_siswa'] . ' ' . $pd['nama_ekskul'] . ' ' . $pd['kelas'])); ?>">
                            <td width="56">
                                <img src="<?= $foto_pd; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td><strong><?= htmlspecialchars($pd['nama_siswa']); ?></strong></td>
                            <td><?= htmlspecialchars($pd['nomor_induk']); ?></td>
                            <td><?= htmlspecialchars($pd['kelas'] ?? '-'); ?></td>
                            <td><span class="badge diterima" style="background: #ede9fe; color: #5b21b6;"><?= htmlspecialchars($pd['nama_ekskul']); ?></span></td>
                            <td><span class="badge <?= $pd['status']; ?>"><?= strtoupper($pd['status']); ?></span></td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <?php if ($pd['status'] === 'pending') : ?>
                                        <a href="?acc_siswa=<?= $pd['daftar_id']; ?>" class="btn btn-daftar" style="padding: 5px 12px; font-size: 12px; border-radius: 50px;">✓ ACC</a>
                                        <a href="?tolak_siswa=<?= $pd['daftar_id']; ?>" class="btn btn-kembali" style="padding: 5px 12px; font-size: 12px; border-radius: 50px;">✕ Tolak</a>
                                    <?php elseif ($pd['status'] === 'ditolak') : ?>
                                        <a href="?acc_siswa=<?= $pd['daftar_id']; ?>" class="btn btn-daftar" style="padding: 5px 12px; font-size: 12px; border-radius: 50px;">✓ Terima Ulang</a>
                                    <?php endif; ?>
                                    <a href="?hapus_pendaftaran=<?= $pd['daftar_id']; ?>" class="btn btn-del" onclick="return confirm('Hapus data pendaftaran siswa ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr><td colspan="7" style="text-align: center;" class="text-muted">Belum ada siswa yang mendaftar ekstrakurikuler.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="card-box">
        <h2>Kelola Pengguna</h2>
        <div style="overflow-x: auto;">
            <table>
                <tr>
                    <th>Foto</th>
                    <th>Nama Lengkap</th>
                    <th>NIS / NIP</th>
                    <th>Password</th>
                    <th>Kelas</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($u = mysqli_fetch_assoc($semua_user)) : ?>
                    <?php 
                    $foto_u = (!empty($u['foto_profil']) && file_exists('assets/img/' . $u['foto_profil'])) 
                        ? 'assets/img/' . htmlspecialchars($u['foto_profil']) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($u['nama']) . '&background=7c3aed&color=fff';
                    ?>
                    <tr>
                        <td width="56">
                            <img src="<?= $foto_u; ?>" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #c7d2fe;">
                        </td>
                        <td><strong><?= htmlspecialchars($u['nama']); ?></strong></td>
                        <td><?= htmlspecialchars($u['nomor_induk']); ?></td>
                        <td>
                            <?php if ($u['role'] == 'admin') : ?>
                                <span class="text-muted" style="font-size: 12px;">🔒 Rahasia</span>
                            <?php elseif (!empty($u['password_plain'])) : ?>
                                <div style="display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 50px;">
                                    <span id="pass_txt_<?= $u['id']; ?>" data-pass="<?= htmlspecialchars($u['password_plain']); ?>" data-shown="0" style="font-family: monospace; font-weight: 700; color: #334155;">••••••</span>
                                    <button type="button" onclick="toggleAdminPass(<?= $u['id']; ?>)" title="Lihat/Sembunyikan Password" style="padding: 2px 5px; background: transparent; color: #64748b; box-shadow: none; font-size: 13px;">👁️</button>
                                </div>
                            <?php else : ?>
                                <span class="text-muted" style="font-size: 11px;">(Belum tersimpan)</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $u['kelas'] ? htmlspecialchars($u['kelas']) : '-'; ?></td>
                        <td>
                            <span class="badge <?= $u['role'] == 'admin' ? 'ditolak' : ($u['role'] == 'pelatih' ? 'pending' : 'diterima'); ?>">
                                <?= strtoupper($u['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['id'] != $_SESSION['user_id']) : ?>
                                <a href="?hapus_user=<?= $u['id']; ?>" class="btn btn-del" onclick="return confirm('Hapus akun ini secara permanen?')">Hapus</a>
                            <?php else : ?>
                                <span class="text-muted" style="font-size: 12px; font-weight: 700;">● Akun Kamu</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div class="card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 18px;">
            <div>
                <h2 style="margin-bottom: 4px; border: none; padding: 0;">Kelola Data & Konten Ekstrakurikuler</h2>
                <p class="text-muted" style="font-size: 13px;">Update foto banner, deskripsi, jadwal, perlengkapan, kontak pengurus ekskul, dan sertifikat.</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="button" onclick="toggleFormTambahEkskul()" class="btn btn-daftar" style="border-radius: 50px; padding: 10px 18px;">+ Tambah Ekskul Baru</button>
                <input type="text" id="searchEkskul" onkeyup="filterEkskul()" placeholder="🔍 Cari nama ekskul..." style="max-width: 260px; margin: 0; border-radius: 50px; padding: 10px 18px;">
            </div>
        </div>

        <form method="POST" action="" enctype="multipart/form-data" id="formTambahEkskulBox" style="display: none; background: #f8fafc; border: 2px dashed #c4b5fd; padding: 20px; border-radius: 20px; margin-bottom: 22px;">
            <h3 style="margin-bottom: 14px; color: #5b21b6;">➕ Tambah Ekstrakurikuler Baru</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                <div class="form-group">
                    <label>Nama Ekstrakurikuler</label>
                    <input type="text" name="nama_ekskul" placeholder="Contoh: ROBOTIKA" required>
                </div>
                <div class="form-group">
                    <label>Nama Pembina / Pelatih</label>
                    <input type="text" name="nama_pembina" placeholder="Contoh: Budi Santoso, S.Pd.">
                </div>
                <div class="form-group">
                    <label>Jadwal & Lokasi</label>
                    <input type="text" name="jadwal" placeholder="Contoh: Sabtu, 08.00 - 10.00 WIB (Lab)" required>
                </div>
                <div class="form-group">
                    <label>📞 Kontak Pengurus Ekskul</label>
                    <input type="text" name="kontak" placeholder="Contoh: 0812-3456-7890 (Ketua / Sekretaris Ekskul)">
                </div>
                <div class="form-group">
                    <label>Foto Utama Ekskul</label>
                    <input type="file" name="file_foto_baru" accept="image/*">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label>Deskripsi Penjelasan</label>
                    <textarea name="deskripsi" rows="2" placeholder="Penjelasan singkat tentang ekskul ini..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Perlengkapan yang Perlu Dibawa</label>
                    <textarea name="perlengkapan" rows="2" placeholder="Perlengkapan yang wajib dibawa siswa..."></textarea>
                </div>
            </div>
            <button type="submit" name="tambah_ekskul" class="btn">✓ Simpan Ekskul Baru</button>
        </form>

        <div class="admin-ekskul-grid" id="ekskulGrid">
            <?php while ($e = mysqli_fetch_assoc($semua_ekskul)) : ?>
                <?php 
                $eid = $e['id'];
                $foto_prestasi = mysqli_query($conn, "SELECT * FROM prestasi_foto WHERE ekskul_id = '$eid'");
                $nama_pembina = !empty($e['nama_pelatih']) ? $e['nama_pelatih'] : (!empty($e['nama_pembina']) ? $e['nama_pembina'] : 'Belum diatur');
                ?>
                <form method="POST" action="" enctype="multipart/form-data" class="admin-ekskul-card" data-nama="<?= strtolower(htmlspecialchars($e['nama_ekskul'])); ?>">
                    <input type="hidden" name="id" value="<?= $eid; ?>">
                    <input type="hidden" name="foto" value="<?= htmlspecialchars($e['foto']); ?>">

                    <div class="admin-card-banner">
                        <img src="assets/img/<?= htmlspecialchars($e['foto']); ?>" alt="<?= htmlspecialchars($e['nama_ekskul']); ?>">
                        <div class="admin-card-banner-overlay">
                            <h3><?= htmlspecialchars($e['nama_ekskul']); ?></h3>
                            <span>👨‍🏫 Pembina: <?= htmlspecialchars($nama_pembina); ?></span>
                        </div>
                    </div>

                    <div class="admin-card-body">
                        <div>
                            <span class="mini-label">📝 Deskripsi Penjelasan</span>
                            <textarea name="deskripsi" rows="3" placeholder="Tulis penjelasan menarik tentang ekskul ini..."><?= htmlspecialchars($e['deskripsi']); ?></textarea>
                        </div>

                        <div>
                            <span class="mini-label">🗓️ Jadwal & Lokasi</span>
                            <input type="text" name="jadwal" placeholder="Contoh: Jumat, 15.00 WIB (Lap Indor)" value="<?= htmlspecialchars($e['jadwal']); ?>">
                        </div>

                        <div>
                            <span class="mini-label">📞 Kontak Pengurus Ekskul</span>
                            <input type="text" name="kontak" placeholder="Contoh: 0812-3456-7890 (WA Ketua / Sekretaris Ekskul)" value="<?= htmlspecialchars($e['kontak'] ?? ''); ?>">
                        </div>

                        <div>
                            <span class="mini-label">🎒 Perlengkapan Dibawa</span>
                            <textarea name="perlengkapan" rows="2" placeholder="Apa saja yang harus dibawa siswa?"><?= htmlspecialchars($e['perlengkapan']); ?></textarea>
                        </div>

                        <div>
                            <span class="mini-label">🏆 Prestasi & Galeri Sertifikat</span>
                            <textarea name="prestasi" rows="2" placeholder="Daftar juara / penghargaan..."><?= htmlspecialchars($e['prestasi']); ?></textarea>
                            
                            <label style="font-size: 11px; color: var(--text-muted); display: block; margin: 6px 0 4px 0;">+ Upload Foto Sertifikat (Bisa pilih banyak):</label>
                            <input type="file" name="foto_prestasi[]" accept="image/*" multiple>
                            
                            <?php if (mysqli_num_rows($foto_prestasi) > 0) : ?>
                                <div class="cert-thumb-row">
                                    <?php while ($fp = mysqli_fetch_assoc($foto_prestasi)) : ?>
                                        <div class="cert-thumb">
                                            <img src="assets/img/<?= htmlspecialchars($fp['nama_file']); ?>">
                                            <a href="?hapus_foto_prestasi=<?= $fp['id']; ?>" onclick="return confirm('Hapus foto sertifikat ini?')">✕</a>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <span class="mini-label">🖼️ Ganti Foto Utama Ekskul</span>
                            <input type="file" name="file_foto" accept="image/*">
                        </div>

                        <div style="display: flex; gap: 8px; margin-top: 6px;">
                            <button type="submit" name="update_ekskul" class="btn" style="flex: 1;">💾 Simpan Perubahan</button>
                            <a href="?hapus_ekskul=<?= $eid; ?>" class="btn btn-del" style="border-radius: 16px; padding: 11px 14px;" onclick="return confirm('Hapus ekskul <?= htmlspecialchars($e['nama_ekskul']); ?> ini?')">🗑️</a>
                        </div>
                    </div>
                </form>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
function toggleFormTambahEkskul() {
    const box = document.getElementById('formTambahEkskulBox');
    box.style.display = (box.style.display === 'none') ? 'block' : 'none';
}

function toggleAdminPass(id) {
    const el = document.getElementById('pass_txt_' + id);
    if (el.getAttribute('data-shown') === '0') {
        el.textContent = el.getAttribute('data-pass');
        el.setAttribute('data-shown', '1');
    } else {
        el.textContent = '••••••';
        el.setAttribute('data-shown', '0');
    }
}

function filterEkskul() {
    let input = document.getElementById('searchEkskul').value.toLowerCase();
    let cards = document.querySelectorAll('.admin-ekskul-card');
    cards.forEach(card => {
        let nama = card.getAttribute('data-nama');
        card.style.display = nama.includes(input) ? 'flex' : 'none';
    });
}

function filterPendaftar() {
    let input = document.getElementById('searchPendaftar').value.toLowerCase();
    let rows = document.querySelectorAll('.row-pendaftar');
    rows.forEach(row => {
        let teks = row.getAttribute('data-cari');
        row.style.display = teks.includes(input) ? '' : 'none';
    });
}
</script>
<script src="assets/animasi.js?v=2"></script>
</body>
</html>