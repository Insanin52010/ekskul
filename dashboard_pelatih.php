<?php
require 'koneksi.php';
require_once 'backup_helper.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'pelatih' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}

$is_admin_preview = ($_SESSION['role'] == 'admin');
$pelatih_id = $_SESSION['user_id'];

$pelatih = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$pelatih_id'"));

if (!$is_admin_preview && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['update_profil_pelatih']) || isset($_POST['auto_upload_foto']))) {
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
            $sql_pass = "password = '$hash_baru', password_plain = '$plain_baru'";
        }
    }

    if ($valid) {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir('assets/img')) mkdir('assets/img', 0777, true);
            $ekstensi = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
            $nama_file = time() . '_pelatih_' . $pelatih_id . '.' . strtolower($ekstensi);
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], 'assets/img/' . $nama_file)) {
                $sql_foto = "foto_profil = '$nama_file'";
            }
        }

        $updates = array_filter([$sql_foto, $sql_pass]);
        if (!empty($updates)) {
            $set_clause = implode(', ', $updates);
            mysqli_query($conn, "UPDATE users SET $set_clause WHERE id = '$pelatih_id'");
            exportDatabaseKeFile($conn);
            $_SESSION['pesan_sukses'] = !empty($password_baru) 
                ? "✨ Password baru pelatih berhasil disimpan!" 
                : "✨ Foto profil pelatih berhasil diperbarui!";
        }
    }

    header("Location: dashboard_pelatih.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['baca_notif'])) {
    $nid = mysqli_real_escape_string($conn, $_GET['baca_notif']);
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id = '$nid' AND user_id = '$pelatih_id'");
    header("Location: dashboard_pelatih.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['baca_semua_notif'])) {
    mysqli_query($conn, "DELETE FROM notifikasi WHERE user_id = '$pelatih_id'");
    header("Location: dashboard_pelatih.php");
    exit;
}

// Ajukan diri menjadi pelatih pada ekskul tertentu (status pending menunggu ACC Admin)
if (!$is_admin_preview && isset($_POST['pilih_ekskul'])) {
    $ekskul_id = mysqli_real_escape_string($conn, $_POST['ekskul_id']);
    $cek_eks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_ekskul FROM ekskul WHERE id = '$ekskul_id' AND pelatih_id IS NULL"));

    if ($cek_eks) {
        mysqli_query($conn, "UPDATE ekskul SET pelatih_id = '$pelatih_id', status_pelatih_ekskul = 'pending' WHERE id = '$ekskul_id' AND pelatih_id IS NULL");

        $nama_pelatih = $pelatih['nama'];
        $nip_pelatih = $pelatih['nomor_induk'];
        $nama_ekskul = $cek_eks['nama_ekskul'];
        $pesan_admin = mysqli_real_escape_string($conn, "Pelatih $nama_pelatih ($nip_pelatih) mengajukan diri melatih ekskul $nama_ekskul dan menunggu ACC Admin.");
        $admins = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin'");
        while ($adm = mysqli_fetch_assoc($admins)) {
            $aid = $adm['id'];
            mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$aid', '$pesan_admin')");
        }

        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "⏳ Pengajuan sebagai pelatih <b>$nama_ekskul</b> berhasil dikirim! Menunggu persetujuan Admin.";
    }
    header("Location: dashboard_pelatih.php");
    exit;
}

// Batalkan pengajuan yang masih pending
if (!$is_admin_preview && isset($_GET['batal_pengajuan'])) {
    $ekskul_id = mysqli_real_escape_string($conn, $_GET['batal_pengajuan']);
    mysqli_query($conn, "UPDATE ekskul SET pelatih_id = NULL, status_pelatih_ekskul = 'aktif' WHERE id = '$ekskul_id' AND pelatih_id = '$pelatih_id' AND status_pelatih_ekskul = 'pending'");
    exportDatabaseKeFile($conn);
    $_SESSION['pesan_sukses'] = "Pengajuan menjadi pelatih telah dibatalkan.";
    header("Location: dashboard_pelatih.php");
    exit;
}

// Berhenti melatih ekskul yang sudah aktif
if (!$is_admin_preview && isset($_GET['berhenti_melatih'])) {
    $ekskul_id = mysqli_real_escape_string($conn, $_GET['berhenti_melatih']);
    $cek_ekskul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_ekskul FROM ekskul WHERE id = '$ekskul_id' AND pelatih_id = '$pelatih_id' AND status_pelatih_ekskul = 'aktif'"));

    if ($cek_ekskul) {
        mysqli_query($conn, "UPDATE ekskul SET pelatih_id = NULL, status_pelatih_ekskul = 'aktif' WHERE id = '$ekskul_id' AND pelatih_id = '$pelatih_id'");

        $nama_pelatih = $pelatih['nama'];
        $nama_ekskul = $cek_ekskul['nama_ekskul'];
        $pesan_admin = mysqli_real_escape_string($conn, "Pelatih $nama_pelatih telah berhenti melatih ekstrakurikuler $nama_ekskul.");
        $admins = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin'");
        while ($adm = mysqli_fetch_assoc($admins)) {
            $aid = $adm['id'];
            mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$aid', '$pesan_admin')");
        }

        exportDatabaseKeFile($conn);
        $_SESSION['pesan_sukses'] = "🚪 Anda telah berhenti melatih ekstrakurikuler <b>$nama_ekskul</b>.";
    }
    header("Location: dashboard_pelatih.php");
    exit;
}

// ACC / Tolak / Hapus Siswa (hanya untuk ekskul yang status pelatihnya sudah 'aktif')
if (!$is_admin_preview && isset($_GET['terima'])) {
    $pendaftaran_id = mysqli_real_escape_string($conn, $_GET['terima']);
    $info = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.siswa_id, e.nama_ekskul 
        FROM pendaftaran_ekskul p 
        JOIN ekskul e ON p.ekskul_id = e.id 
        WHERE p.id = '$pendaftaran_id' AND e.pelatih_id = '$pelatih_id' AND e.status_pelatih_ekskul = 'aktif'
    "));

    if ($info) {
        mysqli_query($conn, "UPDATE pendaftaran_ekskul SET status = 'diterima' WHERE id = '$pendaftaran_id'");
        $sid = $info['siswa_id'];
        $pesan = mysqli_real_escape_string($conn, "🎉 Selamat! Pendaftaran kamu di ekstrakurikuler " . $info['nama_ekskul'] . " telah DITERIMA oleh pelatih.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$sid', '$pesan')");
        exportDatabaseKeFile($conn);
    }
    $_SESSION['pesan_sukses'] = "✅ Siswa berhasil di-ACC dan notifikasi telah dikirim!";
    header("Location: dashboard_pelatih.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['tolak'])) {
    $pendaftaran_id = mysqli_real_escape_string($conn, $_GET['tolak']);
    $info = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.siswa_id, e.nama_ekskul 
        FROM pendaftaran_ekskul p 
        JOIN ekskul e ON p.ekskul_id = e.id 
        WHERE p.id = '$pendaftaran_id' AND e.pelatih_id = '$pelatih_id' AND e.status_pelatih_ekskul = 'aktif'
    "));

    if ($info) {
        mysqli_query($conn, "UPDATE pendaftaran_ekskul SET status = 'ditolak' WHERE id = '$pendaftaran_id'");
        $sid = $info['siswa_id'];
        $pesan = mysqli_real_escape_string($conn, "Mohon maaf, pendaftaran kamu di ekstrakurikuler " . $info['nama_ekskul'] . " DITOLAK oleh pelatih.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$sid', '$pesan')");
        exportDatabaseKeFile($conn);
    }
    $_SESSION['pesan_sukses'] = "Pendaftaran siswa telah ditolak.";
    header("Location: dashboard_pelatih.php");
    exit;
}

if (!$is_admin_preview && isset($_GET['hapus'])) {
    $pendaftaran_id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE p FROM pendaftaran_ekskul p JOIN ekskul e ON p.ekskul_id = e.id WHERE p.id = '$pendaftaran_id' AND e.pelatih_id = '$pelatih_id' AND e.status_pelatih_ekskul = 'aktif'");
    exportDatabaseKeFile($conn);
    $_SESSION['pesan_sukses'] = "Anggota berhasil dihapus dari daftar ekskul.";
    header("Location: dashboard_pelatih.php");
    exit;
}

$ekskul_pending = mysqli_query($conn, "SELECT * FROM ekskul WHERE pelatih_id = '$pelatih_id' AND status_pelatih_ekskul = 'pending'");
$ekskul_dilatih = mysqli_query($conn, "SELECT * FROM ekskul WHERE pelatih_id = '$pelatih_id' AND status_pelatih_ekskul = 'aktif'");
$ekskul_kosong  = mysqli_query($conn, "SELECT * FROM ekskul WHERE pelatih_id IS NULL ORDER BY nama_ekskul ASC");
$katalog_ekskul = mysqli_query($conn, "SELECT * FROM ekskul ORDER BY nama_ekskul ASC");

$foto_pp = (!empty($pelatih['foto_profil']) && file_exists('assets/img/' . $pelatih['foto_profil'])) 
    ? 'assets/img/' . htmlspecialchars($pelatih['foto_profil']) 
    : 'https://ui-avatars.com/api/?name=' . urlencode($pelatih['nama']) . '&background=2563eb&color=fff&size=150';

$param_preview = $is_admin_preview ? '&preview_as=pelatih' : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelatih - SMKN 5 Malang</title>
    <link rel="icon" type="image/jpeg" href="assets/img/vohisma.jpg">
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>

<div class="navbar pelatih">
    <div class="nav-brand">
        <img src="assets/img/vohisma.jpg" alt="Logo SMKN 5 Malang" class="nav-school-logo">
        <span class="nav-logo">SMKN 5 MALANG</span>
        <span class="nav-role"><?= $is_admin_preview ? 'Mode Pratinjau: Pelatih' : 'Portal Pelatih'; ?></span>
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
            👀 <strong>Mode Pratinjau Admin:</strong> Kamu sedang melihat tampilan halaman dari sudut pandang <b>Pelatih</b>.
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
        <div class="card-box">
            <h2>Profil Pelatih</h2>
            <form method="POST" action="" enctype="multipart/form-data" id="formProfilPelatih" class="profile-layout">
                <input type="hidden" name="auto_upload_foto" value="1">
                <div class="profile-avatar-section">
                    <img src="<?= $foto_pp; ?>" alt="Foto Profil" class="avatar-img">
                    <?php if (!$is_admin_preview) : ?>
                        <label class="file-upload-label">
                            📷 Ubah Foto
                            <input type="file" name="foto_profil" accept="image/*" hidden onchange="document.getElementById('formProfilPelatih').submit();">
                        </label>
                    <?php endif; ?>
                </div>

                <div class="profile-fields">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" value="<?= htmlspecialchars($pelatih['nama']); ?>" disabled class="input-locked">
                    </div>

                    <div class="form-group">
                        <label>NIP / ID Pelatih</label>
                        <input type="text" value="<?= htmlspecialchars($pelatih['nomor_induk']); ?>" disabled class="input-locked">
                    </div>

                    <div class="form-group">
                        <label>🔒 Password (Rahasia — Kosongkan jika tidak ingin mengganti)</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" id="passPelatih" name="password_baru" placeholder="•••••••• (Isi angka baru untuk ganti password)" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" <?= $is_admin_preview ? 'disabled' : ''; ?> style="padding-right: 44px;">
                            <button type="button" onclick="togglePassPelatih()" title="Lihat/Sembunyikan Password" style="position: absolute; right: 6px; padding: 6px 10px; background: transparent; color: #64748b; box-shadow: none; font-size: 16px;">👁️</button>
                        </div>
                    </div>

                    <?php if (!$is_admin_preview) : ?>
                        <button type="submit" name="update_profil_pelatih" class="btn btn-daftar btn-block">💾 Simpan Password Baru</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card-box">
            <h2>Pilih Ekskul yang Dilatih</h2>
            <p class="text-muted" style="font-size: 13px; margin-bottom: 14px;">
                Pilih ekstrakurikuler yang ingin Anda bimbing. Pengajuan akan aktif setelah disetujui (di-ACC) oleh Admin.
            </p>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px;">
                <select name="ekskul_id" <?= $is_admin_preview ? 'disabled' : 'required'; ?>>
                    <option value="">-- Pilih Ekskul yang Belum Ada Pelatih --</option>
                    <?php while ($e = mysqli_fetch_assoc($ekskul_kosong)) : ?>
                        <option value="<?= $e['id']; ?>"><?= htmlspecialchars($e['nama_ekskul']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="pilih_ekskul" class="btn btn-daftar" <?= $is_admin_preview ? 'disabled' : ''; ?>>✓ Ajukan Sebagai Pelatih</button>
            </form>

            <?php if (mysqli_num_rows($ekskul_pending) > 0) : ?>
                <div style="margin-top: 18px; border-top: 1px dashed #cbd5e1; padding-top: 14px;">
                    <strong style="font-size: 13px; color: #854d0e; display: block; margin-bottom: 8px;">⏳ Pengajuan Menunggu ACC Admin:</strong>
                    <?php while ($ep = mysqli_fetch_assoc($ekskul_pending)) : ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #fef9c3; border: 1px solid #fde047; padding: 9px 12px; border-radius: 12px; margin-bottom: 8px;">
                            <div>
                                <div style="font-weight: 700; font-size: 13.5px; color: #713f12;"><?= htmlspecialchars($ep['nama_ekskul']); ?></div>
                                <small style="color: #854d0e; font-size: 11.5px;">Menunggu persetujuan Admin</small>
                            </div>
                            <?php if (!$is_admin_preview) : ?>
                                <a href="?batal_pengajuan=<?= $ep['id']; ?>" class="btn btn-del" style="padding: 5px 10px; font-size: 11px;" onclick="return confirm('Batalkan pengajuan melatih <?= htmlspecialchars($ep['nama_ekskul']); ?>?')">Batalkan</a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php while ($ed = mysqli_fetch_assoc($ekskul_dilatih)) : ?>
        <div class="card-box">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                <h2 style="margin: 0; border: none; padding: 0;">Anggota Ekskul: <?= htmlspecialchars($ed['nama_ekskul']); ?></h2>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="detail_ekskul.php?id=<?= $ed['id'] . $param_preview; ?>" class="btn" style="padding: 7px 14px; font-size: 12px;">Lihat Info Ekskul →</a>
                    <?php if (!$is_admin_preview) : ?>
                        <a href="?berhenti_melatih=<?= $ed['id']; ?>" class="btn btn-del" style="padding: 7px 14px; font-size: 12px;" onclick="return confirm('Yakin ingin berhenti melatih ekstrakurikuler <?= htmlspecialchars($ed['nama_ekskul']); ?>?')">🚪 Berhenti Melatih</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $eid = $ed['id'];
            $anggota = mysqli_query($conn, "
                SELECT p.id AS daftar_id, p.status, u.nama, u.nomor_induk, u.kelas, u.foto_profil 
                FROM pendaftaran_ekskul p 
                JOIN users u ON p.siswa_id = u.id 
                WHERE p.ekskul_id = '$eid'
            ");
            ?>
            <div style="overflow-x: auto;">
                <table>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    <?php if (mysqli_num_rows($anggota) > 0) : ?>
                        <?php while ($a = mysqli_fetch_assoc($anggota)) : ?>
                            <?php 
                            $foto_a = (!empty($a['foto_profil']) && file_exists('assets/img/' . $a['foto_profil'])) 
                                ? 'assets/img/' . htmlspecialchars($a['foto_profil']) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($a['nama']) . '&background=4f46e5&color=fff';
                            ?>
                            <tr>
                                <td width="50"><img src="<?= $foto_a; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                                <td><strong><?= htmlspecialchars($a['nama']); ?></strong></td>
                                <td><?= htmlspecialchars($a['nomor_induk']); ?></td>
                                <td><?= htmlspecialchars($a['kelas']); ?></td>
                                <td><span class="badge <?= $a['status']; ?>"><?= strtoupper($a['status']); ?></span></td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <?php if ($a['status'] == 'pending') : ?>
                                            <a href="?terima=<?= $a['daftar_id']; ?>" class="btn btn-daftar" style="padding: 5px 12px; font-size: 12px; border-radius: 50px;">ACC</a>
                                            <a href="?tolak=<?= $a['daftar_id']; ?>" class="btn btn-kembali" style="padding: 5px 12px; font-size: 12px; border-radius: 50px;">Tolak</a>
                                        <?php endif; ?>
                                        <a href="?hapus=<?= $a['daftar_id']; ?>" class="btn btn-del" onclick="return confirm('Keluarkan anggota ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="6" style="text-align:center;" class="text-muted">Belum ada siswa yang mendaftar.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    <?php endwhile; ?>

    <div class="section-title-bar">
        <h2>Jelajahi Ekstrakurikuler SMKN 5 Malang</h2>
        <p>Klik kartu ekstrakurikuler di bawah ini untuk melihat penjelasan, jadwal, pembina, dan prestasi.</p>
    </div>

    <div class="grid-ekskul">
        <?php while ($ekskul = mysqli_fetch_assoc($katalog_ekskul)) : ?>
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
function togglePassPelatih() {
    const input = document.getElementById('passPelatih');
    input.type = (input.type === 'password') ? 'text' : 'password';
}
</script>
<script src="assets/animasi.js?v=2"></script>
</body>
</html>