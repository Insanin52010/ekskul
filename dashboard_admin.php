<?php
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

if (isset($_GET['baca_notif'])) {
    $nid = $_GET['baca_notif'];
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id = '$nid' AND user_id = '$admin_id'");
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['acc_pelatih'])) {
    $uid = $_GET['acc_pelatih'];
    mysqli_query($conn, "UPDATE users SET status_akun = 'aktif' WHERE id = '$uid' AND role = 'pelatih'");
    $_SESSION['pesan_sukses'] = "✅ Akun Pelatih berhasil dikonfirmasi dan diaktifkan!";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_POST['update_ekskul'])) {
    $id = $_POST['id'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal']);
    $prestasi = mysqli_real_escape_string($conn, $_POST['prestasi']);
    $perlengkapan = mysqli_real_escape_string($conn, $_POST['perlengkapan']);
    $foto = mysqli_real_escape_string($conn, $_POST['foto']);

    if (!empty($_FILES['file_foto']['name'])) {
        $nama_file = time() . '_' . $_FILES['file_foto']['name'];
        move_uploaded_file($_FILES['file_foto']['tmp_name'], 'assets/img/' . $nama_file);
        $foto = $nama_file;
    }

    if (!empty($_FILES['foto_prestasi']['name'][0])) {
        $jumlah_file = count($_FILES['foto_prestasi']['name']);
        for ($i = 0; $i < $jumlah_file; $i++) {
            $nama_asli = $_FILES['foto_prestasi']['name'][$i];
            $tmp_name = $_FILES['foto_prestasi']['tmp_name'][$i];
            
            if (!empty($nama_asli)) {
                $nama_baru = time() . '_prestasi_' . $i . '_' . $nama_asli;
                move_uploaded_file($tmp_name, 'assets/img/' . $nama_baru);
                mysqli_query($conn, "INSERT INTO prestasi_foto (ekskul_id, nama_file) VALUES ('$id', '$nama_baru')");
            }
        }
    }

    $query = "UPDATE ekskul SET 
              deskripsi = '$deskripsi', 
              jadwal = '$jadwal', 
              prestasi = '$prestasi', 
              perlengkapan = '$perlengkapan', 
              foto = '$foto' 
              WHERE id = '$id'";
    mysqli_query($conn, $query);

    $_SESSION['pesan_sukses'] = "✨ Mantap! Perubahan data ekstrakurikuler berhasil disimpan.";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['hapus_foto_prestasi'])) {
    $fid = $_GET['hapus_foto_prestasi'];
    $data_foto = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_file FROM prestasi_foto WHERE id = '$fid'"));
    if ($data_foto) {
        $path = 'assets/img/' . $data_foto['nama_file'];
        if (file_exists($path)) {
            unlink($path);
        }
        mysqli_query($conn, "DELETE FROM prestasi_foto WHERE id = '$fid'");
    }
    $_SESSION['pesan_sukses'] = "🗑️ Foto sertifikat berhasil dihapus.";
    header("Location: dashboard_admin.php");
    exit;
}

if (isset($_GET['hapus_user'])) {
    $uid = $_GET['hapus_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$uid'");
    $_SESSION['pesan_sukses'] = "🗑️ Akun pengguna berhasil dihapus.";
    header("Location: dashboard_admin.php");
    exit;
}

$jml_ekskul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ekskul"))['total'];
$jml_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'siswa'"))['total'];
$jml_pelatih = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'pelatih'"))['total'];
$jml_daftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pendaftaran_ekskul"))['total'];

$notifikasi = mysqli_query($conn, "SELECT * FROM notifikasi WHERE user_id = '$admin_id' ORDER BY waktu DESC");
$semua_ekskul = mysqli_query($conn, "SELECT e.*, u.nama AS nama_pelatih FROM ekskul e LEFT JOIN users u ON e.pelatih_id = u.id ORDER BY e.nama_ekskul ASC");
$semua_user = mysqli_query($conn, "SELECT * FROM users ORDER BY status_akun DESC, role ASC, nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Space - SMKN 5 Malang</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="navbar admin">
    <div class="nav-brand">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role">Admin Command Center</span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="dashboard_siswa.php?preview=1" style="background: linear-gradient(135deg, #60a5fa, #8b5cf6); box-shadow: none;">👀 Pratinjau Siswa</a>
        <a href="dashboard_pelatih.php?preview=1" style="background: linear-gradient(135deg, #fde047, #facc15); color: #422006; box-shadow: none;">👀 Pratinjau Pelatih</a>
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

    <?php if (mysqli_num_rows($notifikasi) > 0) : ?>
        <div class="notif-box">
            <div class="notif-header">🔔 Aktivitas Terbaru (<?= mysqli_num_rows($notifikasi); ?>)</div>
            <?php while ($n = mysqli_fetch_assoc($notifikasi)) : ?>
                <div class="notif-item">
                    <span><?= htmlspecialchars($n['pesan']); ?> <small class="text-muted">(<?= $n['waktu']; ?>)</small></span>
                    <a href="?baca_notif=<?= $n['id']; ?>" class="notif-close">Tutup ✕</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <div class="card-box">
        <h2>Kelola Pengguna & Konfirmasi Pelatih</h2>
        <div style="overflow-x: auto;">
            <table>
                <tr>
                    <th>Foto</th>
                    <th>Nama Lengkap</th>
                    <th>NIS / NIP</th>
                    <th>Kelas</th>
                    <th>Role</th>
                    <th>Status Akun</th>
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
                        <td><?= $u['kelas'] ? htmlspecialchars($u['kelas']) : '-'; ?></td>
                        <td>
                            <span class="badge <?= $u['role'] == 'admin' ? 'ditolak' : ($u['role'] == 'pelatih' ? 'pending' : 'diterima'); ?>">
                                <?= strtoupper($u['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['status_akun'] == 'pending') : ?>
                                <span class="badge pending">⏳ MENUNGGU ACC</span>
                            <?php else : ?>
                                <span class="badge diterima">✓ AKTIF</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <?php if ($u['role'] == 'pelatih' && $u['status_akun'] == 'pending') : ?>
                                    <a href="?acc_pelatih=<?= $u['id']; ?>" class="btn btn-daftar" style="padding: 6px 12px; font-size: 12px; border-radius: 50px;">✓ ACC Pelatih</a>
                                <?php endif; ?>
                                
                                <?php if ($u['id'] != $_SESSION['user_id']) : ?>
                                    <a href="?hapus_user=<?= $u['id']; ?>" class="btn btn-del" onclick="return confirm('Hapus akun ini secara permanen?')">Hapus</a>
                                <?php else : ?>
                                    <span class="text-muted" style="font-size: 12px; font-weight: 700;">● Akun Kamu</span>
                                <?php endif; ?>
                            </div>
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
                <p class="text-muted" style="font-size: 13px;">Update foto banner, deskripsi, jadwal, perlengkapan, dan upload banyak sertifikat sekaligus.</p>
            </div>
            <input type="text" id="searchEkskul" onkeyup="filterEkskul()" placeholder="🔍 Cari nama ekskul..." style="max-width: 280px; margin: 0; border-radius: 50px; padding: 10px 18px;">
        </div>

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

                        <button type="submit" name="update_ekskul" class="btn btn-block" style="margin-top: 6px;">💾 Simpan Perubahan</button>
                    </div>
                </form>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
function filterEkskul() {
    let input = document.getElementById('searchEkskul').value.toLowerCase();
    let cards = document.querySelectorAll('.admin-ekskul-card');
    cards.forEach(card => {
        let nama = card.getAttribute('data-nama');
        card.style.display = nama.includes(input) ? 'flex' : 'none';
    });
}
</script>
<script src="assets/animasi.js"></script>
</body>
</html>