-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Nov 2026 pada 11.41
-- Versi server: 10.11.16-MariaDB
-- Versi PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_ekskul_smkn5`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ekskul`
--

CREATE TABLE `ekskul` (
  `id` int(11) NOT NULL,
  `nama_ekskul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `jadwal` varchar(255) DEFAULT NULL,
  `prestasi` text DEFAULT NULL,
  `pelatih_id` int(11) DEFAULT NULL,
  `nama_pembina` varchar(150) DEFAULT NULL,
  `perlengkapan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ekskul`
--

INSERT INTO `ekskul` (`id`, `nama_ekskul`, `deskripsi`, `foto`, `jadwal`, `prestasi`, `pelatih_id`, `nama_pembina`, `perlengkapan`) VALUES
(1, 'PRAMUKA', 'Pendidikan kepanduan yang membentuk karakter mandiri, kerja sama regu, keterampilan tali-temali, sandi, pertolongan pertama, hingga kegiatan berkemah dan bakti masyarakat.', '1795069125_pramuka.jpg', 'Jumat, 13.00 - 15.00 WIB (Lap Indor)', '', NULL, 'Abdul Syukur Budi Santoso & Putri Mirza Bayyinatur Rosyidah, S.Pd.', 'Seragam Pramuka lengkap beserta atribut (hasduk/kacu, baret/topi), tali pramuka, tongkat (saat materi PBB/pionering), buku saku, dan alat tulis.'),
(2, 'PASKIBRA', 'Pasukan Pengibar Bendera sekolah yang menempa kedisiplinan tinggi, jiwa kepemimpinan, ketegasan Peraturan Baris-Berbaris (PBB), serta formasi pengibaran bendera untuk upacara resmi maupun lomba LKBB.', '1795062639_paskii.jpg', 'Sabtu, 07.00 - 12.00 WIB (Lap. Basket)', '', NULL, 'Agil Evan, M.Tr.T.', 'Pakaian latihan (kaos olahraga & celana training), sepatu olahraga bertali yang kuat, topi latihan, ikat pinggang, dan botol air minum.'),
(3, 'TEATER', 'Wadah olah peran, olah tubuh, olah vokal, penulisan naskah drama, dan tata panggung untuk melatih kepercayaan diri, empati karakter, serta seni pertunjukan teater.', '1795054054_teater.jpg', '', '', NULL, NULL, 'Pakaian latihan berwarna gelap yang nyaman untuk bergerak bebas, naskah latihan, alat tulis, dan air minum.'),
(4, 'BDI', 'Organisasi kerohanian Islam sekolah yang berfokus pada syiar Islam, kajian keagamaan rutin, pelatihan kepemimpinan remaja masjid, peringatan hari besar Islam (PHBI), serta kegiatan sosial keagamaan di lingkungan SMKN 5 Malang.', '1795061129_bdi.jpg', '', '', NULL, NULL, 'Pakaian muslim sopan/seragam sekolah rapi, perlengkapan salat pribadi (sarung/mukena), Al-Qur\'an saku, serta buku catatan kajian dan alat tulis.'),
(5, 'SEPAKBOLA', 'Pembinaan olahraga sepak bola lapangan besar yang mencakup latihan fisik, penguasaan bola, umpan jauh, strategi posisi pemain, dan pertandingan persahabatan antar sekolah.', '1795069395_sepak bola.jpg', '', '', NULL, NULL, 'Sepatu bola (pul), pelindung tulang kering (shin guard), kaos kaki panjang, jersey/kaos olahraga, dan botol minum.'),
(6, 'VOLI', 'Ekstrakurikuler olahraga beregu di Lapangan Voli yang melatih teknik passing bawah/atas, service, smash, dan blocking, serta membangun kekompakan dan komunikasi tim di lapangan.', '1795054036_voli.jpg', 'Kamis, 15.30 - 17.00 WIB (Lap Voli)', '', NULL, 'Amelya Ratu Airin', 'Sepatu olahraga sol karet, pelindung lutut (knee pad), kaos dan celana olahraga, kaos kaki, serta botol air minum pribadi.'),
(7, 'BULU TANGKIS', 'Wadah pengembangan minat dan bakat olahraga bulu tangkis, meliputi latihan kelincahan gerak kaki (footwork), teknik pukulan (smash, dropshot, netting), serta pertandingan nomor tunggal maupun ganda.', '1795061177_bulu tangkis.jpg', 'Senin, 15.30 - 17.00 WIB (Lap Indor)', '', NULL, 'Bela Tria Cipta Susanti', 'Raket bulu tangkis pribadi, sepatu olahraga sol karet (indoor), kaos dan celana olahraga, handuk kecil, dan air minum.'),
(8, 'KUNGFU', 'Seni bela diri tradisional Tiongkok yang melatih kelenturan tubuh, keseimbangan, kecepatan gerak, jurus tangan kosong maupun senjata tradisional, serta pengendalian diri.', '1795061717_kungfu.jpg', '', '', NULL, NULL, 'Pakaian latihan bela diri/kaos olahraga longgar, celana training, sepatu bela diri/olahraga ringan, dan air minum.'),
(9, 'PENCAK SILAT', 'Seni bela diri tradisional warisan budaya Indonesia yang melatih kelincahan, kekuatan fisik, jurus tunggal/ganda, serta sportivitas untuk persiapan pertandingan kejuaraan pelajar.', '1795068664_pencak silat.jpg', '', '', NULL, NULL, 'Seragam latihan pencak silat beserta sabuk, pelindung tubuh/alat bantu latihan (jika memiliki), dan air minum.'),
(10, 'KARATE', 'Seni bela diri yang menempa kedisiplinan, ketahanan mental, teknik dasar tangkisan, pukulan, tendangan, serta penguasaan jurus (kata) dan pertarungan (kumite) untuk kejuaraan olahraga pelajar.', '1795061583_karate.jpg', '', '', NULL, NULL, 'Seragam karate (gi) beserta sabuk sesuai tingkatan, pelindung tangan/kaki (jika sudah punya), dan botol air minum.'),
(11, 'FUTSAL', 'Ekstrakurikuler olahraga beregu yang berfokus pada kecepatan kontrol bola, akurasi umpan pendek, taktik bertahan dan menyerang, serta ketahanan fisik untuk persiapan turnamen futsal antar pelajar.', '1795061490_futsal.jpg', 'Rabu, 19.00 - 21.00 WIB (Lap Indor)', '', NULL, 'Mohammad Riza Indrayanto', 'Sepatu futsal (sol datar/non-marking), pelindung tulang kering (shin guard), kaos kaki panjang, jersey/kaos olahraga, dan air minum.'),
(12, 'DRUMBAND', 'Ekstrakurikuler seni musik perkusi dan tiup yang memadukan keterampilan memainkan alat musik drumband (snare drum, bass drum, bellyra, pianika/brass) dengan kekompakan baris-berbaris dan koreografi formasi lapangan. Sering tampil memeriahkan acara sekolah maupun parade kota.', '1795061299_drumband.jpg', '', '', NULL, NULL, 'Pakaian olahraga/latihan yang nyaman, sepatu olahraga bertali, topi latihan, sarung tangan putih (jika ada), dan botol air minum. (Alat musik disediakan oleh sekolah).'),
(13, 'ENGLISH CONVERSATION CLUB', 'Komunitas praktik percakapan bahasa Inggris secara aktif dan santai di Lab Workshop DKV. Siswa dilatih meningkatkan kepercayaan diri dalam daily conversation, public speaking, diskusi interaktif, debate, hingga storytelling untuk persiapan lomba.', '1795061432_ec.jpg', 'Kamis, 15.30 - 17.00 WIB (Lab Workshop DKV)', '', NULL, 'Muhammad Syafi\'i, S.Pd., M.Si.', 'Buku catatan kosakata, alat tulis, kamus saku/aplikasi kamus di smartphone, dan botol minum.'),
(14, 'TARI TRADISIONAL', 'Melestarikan seni tari daerah Nusantara (seperti Tari Malangan, Tari Remo, dan tari kreasi nusantara) dengan melatih keluwesan gerak, wiraga, wirama, dan wirasa untuk pentas seni maupun festival budaya.', '1795069432_tari.jpg', 'Selasa, 15.30 - 17.00 WIB (Aula / Lap Indor)', '', NULL, 'Abdullah Muhammad Yusuf', 'Kain jarik/sampur (selendang tari), pakaian latihan yang nyaman bergerak, kaos kaki, ikat rambut (putri), dan botol minum.'),
(15, 'MODERN DANCE', 'Ekstrakurikuler seni tari modern yang melatih kelenturan gerak, energi panggung, kekompakan koreografi (hip-hop, K-pop cover, contemporary), serta kreativitas aransemen musik untuk tampil di pentas seni dan kompetisi dance.', '1795061790_dance.jpg', 'Kamis, 15.30 - 17.00 WIB (Lap Indor)', '', NULL, 'Rendra Orlando S.', 'Pakaian latihan yang nyaman dan menyerap keringat, sepatu sneakers/dance yang lentur, ikat rambut (bagi yang berambut panjang), dan air minum.'),
(16, 'KARAWITAN', 'Pelestarian seni budaya tradisional Jawa di Aula Depan melalui latihan memainkan instrumen gamelan (saron, bonang, kendang, gong) serta olah vokal sinden untuk mengiringi acara adat dan pentas budaya sekolah.', '1795061632_karawitan.jpg', 'Rabu, 15.30 - 17.00 WIB (Aula Depan)', '', NULL, 'Sony Eko Wandono, S.Sn.', 'Pakaian sopan dan rapi (kaos kaki bersih untuk masuk area gamelan), buku catatan notasi gending, dan alat tulis.'),
(17, 'SENI MUSIK', 'Menyalurkan bakat bermusik siswa dalam memainkan instrumen band maupun akustik (gitar, bass, drum, keyboard) dan olah vokal, melatih aransemen lagu, serta kekompakan tampil di panggung acara sekolah.', '1795069282_seni musik.jpg', 'Kamis, 16.00 - 17.30 WIB (Aula Depan)', '', NULL, 'Jeni Astina Siringo', 'Alat musik pribadi seperti gitar/bass/stick drum (jika ada), pick gitar, kabel jack pribadi, dan catatan lirik/chord lagu.'),
(18, 'BASKET', 'Wadah bagi siswa-siswi yang memiliki minat dan bakat di bidang olahraga bola basket. Ekstrakurikuler ini melatih teknik dasar (dribbling, passing, shooting), strategi permainan tim, serta ketahanan fisik untuk menghadapi berbagai turnamen antar pelajar.', '1795061030_basketball.jpeg', 'Selasa 16.00 - 18.00 & Sabtu 15.00 - 17.00 WIB (Lap Indor)', '', NULL, 'Eva Rahmawati', 'Sepatu basket/olahraga sol karet (non-marking), kaos olahraga/jersey latihan, celana pendek olahraga, kaos kaki tebal, dan botol air minum pribadi.'),
(19, 'JURNALISTIK', 'Wadah bagi siswa yang gemar menulis, meliput berita, wawancara narasumber, dan mendokumentasikan kegiatan sekolah untuk dikelola menjadi artikel berita, buletin, maupun konten publikasi resmi SMKN 5 Malang.', '1795061527_jurnalis.jpg', 'Kamis, 15.30 - 17.00 WIB (Auditorium)', '', NULL, 'R. H. Authonul Muther', 'Buku catatan kecil (notes), pulpen, smartphone/alat perekam suara untuk liputan dan wawancara, serta laptop (jika ada)'),
(20, 'PMR', 'Palang Merah Remaja yang membekali siswa dengan keterampilan pertolongan pertama pada kecelakaan (P3K), penanganan medis darurat saat upacara atau acara sekolah, serta kegiatan kemanusiaan.', '1795069049_1794984776_pmr.png', 'Selasa, 15.45 - 17.00 WIB (G104)', '', NULL, 'M. Sururi Al Faruq, S.Pd.', 'Buku saku kepalangmerahan, alat tulis, slayer/syal PMR, perlengkapan P3K dasar latihan (perban gulung/mitela), dan air minum.'),
(21, 'KARYA ILMIAH REMAJA', 'Ekstrakurikuler riset dan inovasi bagi siswa yang gemar bereksperimen, memecahkan masalah, serta menyusun karya tulis ilmiah maupun produk teknologi tepat guna untuk diikutsertakan dalam lomba penelitian pelajar.', '1795061669_kir.jpg', '', '', NULL, NULL, 'Buku catatan riset, alat tulis, laptop (untuk penyusunan proposal dan laporan), serta bahan eksperimen sesuai proyek kelompok.'),
(22, 'ROHIS', NULL, 'default.jpg', 'Senin, 15.30 - 17.00 WIB (Masjid)', NULL, NULL, 'Syifaun Nisa Azzahra, S.H', NULL),
(23, 'SENI RUPA MURNI', 'Ekstrakurikuler bagi siswa yang gemar menggambar dan melukis untuk mengeksplorasi teknik sketsa, arsiran, melukis di atas kanvas (cat air/akrilik), serta pameran karya seni rupa.', '1795069339_seni rupa murni.jpg', 'Senin, 15.30 - 17.00 WIB (Auditorium)', '', NULL, 'Miftahul Khoiriyah', 'Buku gambar/sketsa (sketchbook), pensil gambar (2B–8B), penghapus, kuas lukis, palet, dan cat lukis pribadi (sesuai proyek karya).'),
(24, 'SENI BATIK & SABLON', 'Wadah keterampilan kriya tekstil di Bengkel Tekstil yang melatih teknik membatik (mencanting, pewarnaan kain) serta teknik sablon manual untuk menghasilkan karya tekstil bernilai seni dan wirausaha.', '1795069230_seni batik.jpg', 'Rabu, 15.30 - 17.00 WIB (Bengkel Tekstil)', '', NULL, 'Latifatul Amalia, S.Pd., Gr.', 'Celemek/pakaian kerja agar seragam tidak terkena noda cat atau malam batik, buku sketsa motif, pensil, dan kain lap kecil.'),
(25, 'MODELLING', 'Wadah pengembangan bakat di bidang catwalk, postur tubuh, ekspresi panggung, photoshoot pose, serta public confidence untuk tampil di pagelaran busana (fashion show) maupun lomba modelling pelajar.', '1795061750_modeling.jpg', 'Rabu, 15.30 - 17.00 WIB (Lap Indor)', '', NULL, 'Abdulah Sidik, SAB', 'Pakaian latihan yang pas di badan (fit body) agar postur terlihat jelas, sepatu heels latihan (putri) atau sepatu pantofel/sneakers rapi (putra), ikat rambut, dan air minum.'),
(26, 'MUAYTHAI', 'Olahraga bela diri asal Thailand yang melatih kekuatan fisik, kardio, serta teknik serangan kombinasi pukulan, sikutan, lutut, dan tendangan untuk kebugaran maupun prestasi kejuaraan ring.', '1795061988_muaythai.jpg', 'Kamis, 15.30 - 17.00 WIB (Lap Voli / Indor)', '', NULL, 'Fabian Riski', 'Kaos olahraga, celana pendek olahraga/celana Muaythai, handwrap (pelilit tangan), sarung tinju/pelindung pribadi (jika sudah punya), handuk kecil, dan air minum.'),
(27, 'PIK-R', 'Pusat Informasi dan Konseling Remaja yang menjadi wadah edukasi sebaya, pengembangan karakter remaja, pelatihan komunikasi empatik, serta kampanye kesehatan mental dan generasi berencana di lingkungan sekolah.', '1795068921_Screenshot 2026-11-19 131445.png', 'Kamis, 15.30 - 17.00 WIB (Kelas / BK)', '', NULL, 'Divadinda Nasywa Azizah', 'Buku catatan materi, alat tulis, dan botol air minum pribadi.'),
(28, 'SCULPTING ANIMASI', 'Ekstrakurikuler kreatif di Bengkel Anim E 104 yang mempelajari teknik pemodelan dan pemahatan karakter 3D (3D digital sculpting), anatomi karakter, serta dasar animasi untuk industri kreatif.', '1795069171_3d-sculpting.webp', 'Sabtu, 08.00 - 10.00 WIB (Bengkel Anim E 104)', '', NULL, 'Erfian Andiantoro', 'Laptop pribadi (jika diperlukan), pen tablet/stylus (jika memiliki), flashdisk untuk menyimpan file proyek 3D, dan buku sketsa ide.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `status_baca` enum('belum','sudah') DEFAULT 'belum',
  `waktu` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran_ekskul`
--

CREATE TABLE `pendaftaran_ekskul` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `ekskul_id` int(11) NOT NULL,
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi_foto`
--

CREATE TABLE `prestasi_foto` (
  `id` int(11) NOT NULL,
  `ekskul_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prestasi_foto`
--

INSERT INTO `prestasi_foto` (`id`, `ekskul_id`, `nama_file`, `keterangan`) VALUES
(1, 18, '1795061030_prestasi_0_prestasi_basket.jpg', NULL),
(2, 26, '1795061988_prestasi_0_prestasi_muaythai.jpg', NULL),
(3, 26, '1795061988_prestasi_1_prestasi_muaythai1.jpg', NULL),
(4, 2, '1795062639_prestasi_0_prestasi_paski.jpg', NULL),
(5, 2, '1795062639_prestasi_1_prestasi_paski1.jpg', NULL),
(6, 2, '1795062639_prestasi_2_prestasi_paski2.jpg', NULL),
(7, 18, '1795067797_prestasi_0_Screenshot 2026-11-19 114947.png', NULL),
(8, 18, '1795067797_prestasi_1_Screenshot 2026-11-19 125306.png', NULL),
(9, 18, '1795067797_prestasi_2_Screenshot 2026-11-19 125539.png', NULL),
(10, 18, '1795067833_prestasi_0_Screenshot 2026-11-19 114937.png', NULL),
(11, 20, '1795069049_prestasi_0_1794978781_prestasi_0_sertif opon2.jpg', NULL),
(12, 20, '1795069049_prestasi_1_1794978781_prestasi_1_sertif opon1.jpg', NULL),
(13, 20, '1795069049_prestasi_2_1794978781_prestasi_2_sertif opon.jpg', NULL),
(14, 20, '1795069049_prestasi_3_1794978781_prestasi_3_OPON.jpg', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nomor_induk` varchar(50) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_plain` varchar(100) DEFAULT NULL,
  `role` enum('admin','siswa','pelatih') NOT NULL,
  `status_akun` enum('aktif','pending') DEFAULT 'aktif',
  `foto_profil` varchar(255) DEFAULT 'default_user.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `nomor_induk`, `kelas`, `jenis_kelamin`, `alamat`, `password`, `password_plain`, `role`, `status_akun`, `foto_profil`) VALUES
(1, 'Ami', '20', NULL, NULL, NULL, '$2y$12$8Adlobig11RzkwYS2tTbS.Hb8hhvdWfw3QWT6RqfVfqNHXTWrcBpu', NULL, 'admin', 'aktif', 'default_user.png');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `ekskul`
--
ALTER TABLE `ekskul`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelatih_id` (`pelatih_id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pendaftaran_ekskul`
--
ALTER TABLE `pendaftaran_ekskul`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `ekskul_id` (`ekskul_id`);

--
-- Indeks untuk tabel `prestasi_foto`
--
ALTER TABLE `prestasi_foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ekskul_id` (`ekskul_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_induk` (`nomor_induk`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ekskul`
--
ALTER TABLE `ekskul`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran_ekskul`
--
ALTER TABLE `pendaftaran_ekskul`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `prestasi_foto`
--
ALTER TABLE `prestasi_foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `ekskul`
--
ALTER TABLE `ekskul`
  ADD CONSTRAINT `ekskul_ibfk_1` FOREIGN KEY (`pelatih_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran_ekskul`
--
ALTER TABLE `pendaftaran_ekskul`
  ADD CONSTRAINT `pendaftaran_ekskul_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pendaftaran_ekskul_ibfk_2` FOREIGN KEY (`ekskul_id`) REFERENCES `ekskul` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `prestasi_foto`
--
ALTER TABLE `prestasi_foto`
  ADD CONSTRAINT `prestasi_foto_ibfk_1` FOREIGN KEY (`ekskul_id`) REFERENCES `ekskul` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
