<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ekskul_smkn5";

$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Koneksi ke MySQL gagal: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
mysqli_select_db($conn, $db);

$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'ekskul'");
$sql_file  = __DIR__ . '/database.sql';

if (mysqli_num_rows($cek_tabel) === 0 && file_exists($sql_file)) {
    $isi_sql = file_get_contents($sql_file);
    if (!empty(trim($isi_sql))) {
        mysqli_multi_query($conn, $isi_sql);
        while (mysqli_more_results($conn) && mysqli_next_result($conn)) {;}
    }
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `nomor_induk` VARCHAR(50) NOT NULL UNIQUE,
    `kelas` VARCHAR(50) NULL,
    `jenis_kelamin` ENUM('Laki-laki', 'Perempuan') NULL,
    `alamat` TEXT NULL,
    `password` VARCHAR(255) NOT NULL,
    `password_plain` VARCHAR(100) NULL,
    `role` ENUM('siswa', 'pelatih', 'admin') NOT NULL DEFAULT 'siswa',
    `status_akun` ENUM('pending', 'aktif') NOT NULL DEFAULT 'aktif',
    `foto_profil` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `ekskul` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_ekskul` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT NULL,
    `jadwal` VARCHAR(150) NULL,
    `prestasi` TEXT NULL,
    `perlengkapan` TEXT NULL,
    `kontak` VARCHAR(150) NULL,
    `foto` VARCHAR(255) DEFAULT 'vohisma.jpg',
    `pelatih_id` INT NULL,
    `status_pelatih_ekskul` ENUM('aktif', 'pending') NOT NULL DEFAULT 'aktif',
    `nama_pembina` VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Tambahkan kolom status_pelatih_ekskul pada tabel ekskul jika belum ada
$cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM `ekskul` LIKE 'status_pelatih_ekskul'");
if (mysqli_num_rows($cek_kolom) === 0) {
    mysqli_query($conn, "ALTER TABLE `ekskul` ADD COLUMN `status_pelatih_ekskul` ENUM('aktif', 'pending') NOT NULL DEFAULT 'aktif' AFTER `pelatih_id`");
}

// Tambahkan kolom kontak pada tabel ekskul jika belum ada
$cek_kolom_kontak = mysqli_query($conn, "SHOW COLUMNS FROM `ekskul` LIKE 'kontak'");
if (mysqli_num_rows($cek_kolom_kontak) === 0) {
    mysqli_query($conn, "ALTER TABLE `ekskul` ADD COLUMN `kontak` VARCHAR(150) NULL AFTER `perlengkapan`");
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `pendaftaran_ekskul` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `siswa_id` INT NOT NULL,
    `ekskul_id` INT NOT NULL,
    `status` ENUM('pending', 'diterima', 'ditolak') NOT NULL DEFAULT 'pending',
    `tanggal_daftar` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Tambahkan kolom tanggal_daftar pada tabel pendaftaran_ekskul jika belum ada
$cek_kolom_tgl = mysqli_query($conn, "SHOW COLUMNS FROM `pendaftaran_ekskul` LIKE 'tanggal_daftar'");
if (mysqli_num_rows($cek_kolom_tgl) === 0) {
    mysqli_query($conn, "ALTER TABLE `pendaftaran_ekskul` ADD COLUMN `tanggal_daftar` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `prestasi_foto` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ekskul_id` INT NOT NULL,
    `nama_file` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `notifikasi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `pesan` TEXT NOT NULL,
    `waktu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Akun Admin Default & Ami
$cek_admin = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin' LIMIT 1");
if (mysqli_num_rows($cek_admin) === 0) {
    $pass_admin_default = password_hash('123456', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (nama, nomor_induk, password, role, status_akun) 
                         VALUES ('Administrator SMKN 5', 'admin', '$pass_admin_default', 'admin', 'aktif')");
}

$cek_ami = mysqli_query($conn, "SELECT id FROM users WHERE nomor_induk = '20' LIMIT 1");
if (mysqli_num_rows($cek_ami) === 0) {
    $pass_ami = password_hash('12345', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (nama, nomor_induk, password, role, status_akun) 
                         VALUES ('Ami', '20', '$pass_ami', 'admin', 'aktif')");
}

// Isi otomatis 21 data ekskul SMKN 5 Malang jika tabel ekskul masih kosong
$cek_ekskul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ekskul"));
if ((int)$cek_ekskul['total'] === 0) {
    $data_awal = [
        ['PRAMUKA', 'Abdul Syukur Budi Santoso & Putri Mirza Bayyinatur Rosyidah, S.Pd.', 'Jumat, 13.00 - 15.00 WIB (Lap Indor)', 'Pendidikan kepanduan yang membentuk karakter mandiri, kerja sama regu, keterampilan tali-temali, dan pionering.', 'Seragam Pramuka lengkap beserta atribut (hasduk, baret/topi), tali pramuka, buku saku, dan alat tulis.'],
        ['PASKIBRA', 'Agil Evan, M.Tr.T.', 'Sabtu, 07.00 - 12.00 WIB (Lap. Basket)', 'Pasukan Pengibar Bendera sekolah yang menempa kedisiplinan tinggi, jiwa kepemimpinan, dan ketegasan PBB.', 'Pakaian latihan (kaos olahraga & celana training), sepatu olahraga bertali kuat, topi latihan, dan botol minum.'],
        ['TEATER', 'Belum diatur', 'Jumat, 15.00 WIB (Auditorium)', 'Wadah olah peran, olah tubuh, olah vokal, penulisan naskah drama, dan tata panggung.', 'Pakaian latihan berwarna gelap yang nyaman bergerak, naskah latihan, alat tulis, dan air minum.'],
        ['BDI', 'Syifaun Nisa Azzahra, S.H', 'Senin, 15.30 - 17.00 WIB (Masjid)', 'Badan Dakwah Islam di Masjid sekolah yang berfokus pada kajian keislaman, seni banjari/hadrah, dan pembinaan akhlak.', 'Busana muslim sopan/seragam sekolah rapi, perlengkapan salat pribadi, Al-Qur\'an saku, dan buku catatan.'],
        ['SEPAKBOLA', 'Belum diatur', 'Jumat, 15.00 WIB (Lapangan)', 'Pembinaan olahraga sepak bola lapangan besar yang mencakup latihan fisik, penguasaan bola, dan strategi.', 'Sepatu bola (pul), pelindung tulang kering, kaos kaki panjang, jersey/kaos olahraga, dan botol minum.'],
        ['VOLI', 'Amelya Ratu Airin', 'Kamis, 15.30 - 17.00 WIB (Lap Voli)', 'Ekstrakurikuler olahraga beregu di Lapangan Voli yang melatih teknik passing, service, smash, dan blocking.', 'Sepatu olahraga sol karet, pelindung lutut (knee pad), kaos dan celana olahraga, kaos kaki, dan botol minum.'],
        ['BULU TANGKIS', 'Belum diatur', 'Kamis, 15.30 - 17.30 WIB (Lap Indor)', 'Ekstrakurikuler olahraga raket yang berfokus pada kelincahan footwork, teknik pukulan lob, smash, dropshot, dan stamina.', 'Raket bulutangkis pribadi, shuttlecock latihan, sepatu olahraga non-marking, pakaian olahraga, dan air minum.'],
        ['KUNGFU', 'Belum diatur', 'Jumat, 15.00 WIB (Lap Indor)', 'Seni bela diri tradisional Tiongkok yang melatih kelenturan tubuh, keseimbangan, kecepatan gerak, dan pengendalian diri.', 'Pakaian latihan bela diri/kaos olahraga longgar, celana training, sepatu olahraga ringan, dan air minum.'],
        ['PENCAK SILAT', 'Belum diatur', 'Jumat, 15.00 WIB (Lap Indor)', 'Seni bela diri tradisional warisan budaya Indonesia yang melatih kelincahan, kekuatan fisik, dan jurus.', 'Seragam latihan pencak silat beserta sabuk, pelindung tubuh (jika memiliki), dan air minum.'],
        ['KARATE', 'Belum diatur', 'Jumat, 15.00 WIB (Lap Indor)', 'Seni bela diri yang menempa kedisiplinan, ketahanan mental, teknik dasar tangkisan, pukulan, tendangan, serta penguasaan jurus.', 'Seragam karate (gi) beserta sabuk sesuai tingkatan, pelindung tangan/kaki, dan botol air minum.'],
        ['FUTSAL', 'Mohammad Riza Indrayanto', 'Rabu, 19.00 - 21.00 WIB (Lap Indor)', 'Ekstrakurikuler olahraga beregu yang berfokus pada kecepatan kontrol bola, akurasi umpan pendek, taktik bertahan dan menyerang.', 'Sepatu futsal, pelindung tulang kering (shin guard), kaos kaki panjang, jersey/kaos olahraga, dan air minum.'],
        ['DRUMBAND', 'Belum diatur', 'Jumat, 15.00 WIB (Lap Indor)', 'Ekstrakurikuler seni musik perkusi dan tiup yang memadukan keterampilan memainkan alat musik drumband dengan kekompakan baris-berbaris.', 'Pakaian olahraga/latihan yang nyaman, sepatu olahraga bertali, topi latihan, dan botol air minum.'],
        ['ENGLISH CONVERSATION CLUB', "Muhammad Syafi'i, S.Pd., M.Si.", 'Kamis, 15.30 - 17.00 WIB (Lab Workshop DKV)', 'Komunitas praktik percakapan bahasa Inggris secara aktif, diskusi, debat, dan storytelling di Lab Workshop DKV.', 'Buku catatan kosakata, alat tulis, kamus saku/aplikasi kamus di smartphone, dan botol minum.'],
        ['TARI TRADISIONAL', 'Abdullah Muhammad Yusuf', 'Selasa, 15.30 - 17.00 WIB (Aula / Lap Indor)', 'Melestarikan seni tari daerah Nusantara dengan melatih keluwesan gerak, wiraga, wirama, dan wirasa.', 'Kain jarik/sampur (selendang tari), pakaian latihan yang nyaman bergerak, kaos kaki, dan botol minum.'],
        ['MODERN DANCE', 'Rendra Orlando S.', 'Kamis, 15.30 - 17.00 WIB (Lap Indor)', 'Ekstrakurikuler seni tari modern yang melatih kelenturan gerak, energi panggung, dan kekompakan koreografi.', 'Pakaian latihan yang nyaman dan menyerap keringat, sepatu sneakers/dance lentur, dan air minum.'],
        ['KARAWITAN', 'Sony Eko Wandono, S.Sn.', 'Rabu, 15.30 - 17.00 WIB (Aula Depan)', 'Pelestarian seni budaya tradisional Jawa melalui latihan memainkan instrumen gamelan serta olah vokal sinden.', 'Pakaian sopan dan rapi (kaos kaki bersih untuk masuk area gamelan), buku catatan notasi gending, dan alat tulis.'],
        ['SENI MUSIK', 'Jeni Astina Siringo', 'Kamis, 16.00 - 17.30 WIB (Aula Depan)', 'Menyalurkan bakat bermusik siswa dalam memainkan instrumen band maupun akustik dan olah vokal.', 'Alat musik pribadi seperti gitar/bass/stick drum (jika ada), pick gitar, kabel jack, dan catatan lagu.'],
        ['BASKET', 'Belum diatur', 'Rabu & Jumat, 15.30 - 17.30 WIB (Lap. Basket)', 'Ekstrakurikuler olahraga bola basket yang melatih teknik dribbling, passing, shooting, defense, serta kekompakan kerja sama tim.', 'Sepatu basket/olahraga bersol karet, kaos dan celana olahraga, kaos kaki, handuk kecil, dan botol air minum.'],
        ['JURNALISTIK', 'R. H. Authonul Muther', 'Kamis, 15.30 - 17.00 WIB (Auditorium)', 'Wadah bagi siswa yang gemar menulis, meliput berita, wawancara narasumber, dan mendokumentasikan kegiatan sekolah.', 'Buku catatan kecil (notes), pulpen, smartphone/alat perekam suara, serta laptop (jika ada).'],
        ['PMR', 'M. Sururi Al Faruq, S.Pd.', 'Selasa, 15.45 - 17.00 WIB (G104)', 'Palang Merah Remaja yang membekali siswa dengan keterampilan pertolongan pertama pada kecelakaan (P3K), penanganan medis darurat saat upacara atau acara sekolah, serta kegiatan kemanusiaan.', 'Buku saku kepalangmerahan, alat tulis, slayer/syal PMR, perlengkapan P3K dasar latihan (perban gulung/mitela), dan air minum.'],
        ['KARYA ILMIAH REMAJA', 'Belum diatur', 'Jumat, 15.00 WIB (Ruang Kelas)', 'Ekstrakurikuler riset dan inovasi bagi siswa yang gemar bereksperimen serta menyusun karya tulis ilmiah.', 'Buku catatan riset, alat tulis, laptop, serta bahan eksperimen sesuai proyek kelompok.']
    ];

    foreach ($data_awal as $item) {
        $n = mysqli_real_escape_string($conn, $item[0]);
        $p = mysqli_real_escape_string($conn, $item[1]);
        $j = mysqli_real_escape_string($conn, $item[2]);
        $d = mysqli_real_escape_string($conn, $item[3]);
        $k = mysqli_real_escape_string($conn, $item[4]);
        mysqli_query($conn, "INSERT INTO ekskul (nama_ekskul, nama_pembina, jadwal, deskripsi, perlengkapan, foto) 
                             VALUES ('$n', '$p', '$j', '$d', '$k', 'vohisma.jpg')");
    }
}
?>