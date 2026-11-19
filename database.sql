-- Auto-Backup Database Portal Ekskul SMKN 5 Malang
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ekskul`;
CREATE TABLE `ekskul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_ekskul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `jadwal` varchar(255) DEFAULT NULL,
  `prestasi` text DEFAULT NULL,
  `pelatih_id` int(11) DEFAULT NULL,
  `status_pelatih_ekskul` enum('aktif','pending') NOT NULL DEFAULT 'aktif',
  `nama_pembina` varchar(150) DEFAULT NULL,
  `perlengkapan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pelatih_id` (`pelatih_id`),
  CONSTRAINT `ekskul_ibfk_1` FOREIGN KEY (`pelatih_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ekskul` VALUES('1', 'PRAMUKA', 'Pendidikan kepanduan yang membentuk karakter mandiri, kerja sama regu, keterampilan tali-temali, sandi, pertolongan pertama, hingga kegiatan berkemah dan bakti masyarakat.', '1795069125_pramuka.jpg', 'Jumat, 13.00 - 15.00 WIB (Lap Indor)', '', NULL, 'aktif', 'Abdul Syukur Budi Santoso & Putri Mirza Bayyinatur Rosyidah, S.Pd.', 'Seragam Pramuka lengkap beserta atribut (hasduk/kacu, baret/topi), tali pramuka, tongkat (saat materi PBB/pionering), buku saku, dan alat tulis.');
INSERT INTO `ekskul` VALUES('2', 'PASKIBRA', 'Pasukan Pengibar Bendera sekolah yang menempa kedisiplinan tinggi, jiwa kepemimpinan, ketegasan Peraturan Baris-Berbaris (PBB), serta formasi pengibaran bendera untuk upacara resmi maupun lomba LKBB.', '1795062639_paskii.jpg', 'Sabtu, 07.00 - 12.00 WIB (Lap. Basket)', '', NULL, 'aktif', 'Agil Evan, M.Tr.T.', 'Pakaian latihan (kaos olahraga & celana training), sepatu olahraga bertali yang kuat, topi latihan, ikat pinggang, dan botol air minum.');
INSERT INTO `ekskul` VALUES('3', 'TEATER', 'Wadah olah peran, olah tubuh, olah vokal, penulisan naskah drama, dan tata panggung untuk melatih kepercayaan diri, empati karakter, serta seni pertunjukan teater.', '1795054054_teater.jpg', '', '', NULL, 'aktif', NULL, 'Pakaian latihan berwarna gelap yang nyaman untuk bergerak bebas, naskah latihan, alat tulis, dan air minum.');
INSERT INTO `ekskul` VALUES('4', 'BDI', 'Organisasi kerohanian Islam sekolah yang berfokus pada syiar Islam, kajian keagamaan rutin, pelatihan kepemimpinan remaja masjid, peringatan hari besar Islam (PHBI), serta kegiatan sosial keagamaan di lingkungan SMKN 5 Malang.', '1795061129_bdi.jpg', '', '', NULL, 'aktif', NULL, 'Pakaian muslim sopan/seragam sekolah rapi, perlengkapan salat pribadi (sarung/mukena), Al-Qur\'an saku, serta buku catatan kajian dan alat tulis.');
INSERT INTO `ekskul` VALUES('5', 'SEPAKBOLA', 'Pembinaan olahraga sepak bola lapangan besar yang mencakup latihan fisik, penguasaan bola, umpan jauh, strategi posisi pemain, dan pertandingan persahabatan antar sekolah.', '1795069395_sepak bola.jpg', '', '', NULL, 'aktif', NULL, 'Sepatu bola (pul), pelindung tulang kering (shin guard), kaos kaki panjang, jersey/kaos olahraga, dan botol minum.');
INSERT INTO `ekskul` VALUES('6', 'VOLI', 'Ekstrakurikuler olahraga beregu di Lapangan Voli yang melatih teknik passing bawah/atas, service, smash, dan blocking, serta membangun kekompakan dan komunikasi tim di lapangan.', '1795054036_voli.jpg', 'Kamis, 15.30 - 17.00 WIB (Lap Voli)', '', NULL, 'aktif', 'Amelya Ratu Airin', 'Sepatu olahraga sol karet, pelindung lutut (knee pad), kaos dan celana olahraga, kaos kaki, serta botol air minum pribadi.');
INSERT INTO `ekskul` VALUES('7', 'BULU TANGKIS', 'Wadah pengembangan minat dan bakat olahraga bulu tangkis, meliputi latihan kelincahan gerak kaki (footwork), teknik pukulan (smash, dropshot, netting), serta pertandingan nomor tunggal maupun ganda.', '1795061177_bulu tangkis.jpg', 'Senin, 15.30 - 17.00 WIB (Lap Indor)', '', NULL, 'aktif', 'Bela Tria Cipta Susanti', 'Raket bulu tangkis pribadi, sepatu olahraga sol karet (indoor), kaos dan celana olahraga, handuk kecil, dan air minum.');
INSERT INTO `ekskul` VALUES('8', 'KUNGFU', 'Seni bela diri tradisional Tiongkok yang melatih kelenturan tubuh, keseimbangan, kecepatan gerak, jurus tangan kosong maupun senjata tradisional, serta pengendalian diri.', '1795061717_kungfu.jpg', '', '', NULL, 'aktif', NULL, 'Pakaian latihan bela diri/kaos olahraga longgar, celana training, sepatu bela diri/olahraga ringan, dan air minum.');
INSERT INTO `ekskul` VALUES('9', 'PENCAK SILAT', 'Seni bela diri tradisional warisan budaya Indonesia yang melatih kelincahan, kekuatan fisik, jurus tunggal/ganda, serta sportivitas untuk persiapan pertandingan kejuaraan pelajar.', '1795068664_pencak silat.jpg', '', '', NULL, 'aktif', NULL, 'Seragam latihan pencak silat beserta sabuk, pelindung tubuh/alat bantu latihan (jika memiliki), dan air minum.');
INSERT INTO `ekskul` VALUES('10', 'KARATE', 'Seni bela diri yang menempa kedisiplinan, ketahanan mental, teknik dasar tangkisan, pukulan, tendangan, serta penguasaan jurus (kata) dan pertarungan (kumite) untuk kejuaraan olahraga pelajar.', '1795061583_karate.jpg', '', '', NULL, 'aktif', NULL, 'Seragam karate (gi) beserta sabuk sesuai tingkatan, pelindung tangan/kaki (jika sudah punya), dan botol air minum.');
INSERT INTO `ekskul` VALUES('11', 'FUTSAL', 'Ekstrakurikuler olahraga beregu yang berfokus pada kecepatan kontrol bola, akurasi umpan pendek, taktik bertahan dan menyerang, serta ketahanan fisik untuk persiapan turnamen futsal antar pelajar.', '1795061490_futsal.jpg', 'Rabu, 19.00 - 21.00 WIB (Lap Indor)', '', NULL, 'aktif', 'Mohammad Riza Indrayanto', 'Sepatu futsal (sol datar/non-marking), pelindung tulang kering (shin guard), kaos kaki panjang, jersey/kaos olahraga, dan air minum.');
INSERT INTO `ekskul` VALUES('12', 'DRUMBAND', 'Ekstrakurikuler seni musik perkusi dan tiup yang memadukan keterampilan memainkan alat musik drumband (snare drum, bass drum, bellyra, pianika/brass) dengan kekompakan baris-berbaris dan koreografi formasi lapangan. Sering tampil memeriahkan acara sekolah maupun parade kota.', '1795061299_drumband.jpg', '', '', NULL, 'aktif', NULL, 'Pakaian olahraga/latihan yang nyaman, sepatu olahraga bertali, topi latihan, sarung tangan putih (jika ada), dan botol air minum. (Alat musik disediakan oleh sekolah).');
INSERT INTO `ekskul` VALUES('13', 'ENGLISH CONVERSATION CLUB', 'Komunitas praktik percakapan bahasa Inggris secara aktif dan santai di Lab Workshop DKV. Siswa dilatih meningkatkan kepercayaan diri dalam daily conversation, public speaking, diskusi interaktif, debate, hingga storytelling untuk persiapan lomba.', '1795061432_ec.jpg', 'Kamis, 15.30 - 17.00 WIB (Lab Workshop DKV)', '', NULL, 'aktif', 'Muhammad Syafi\'i, S.Pd., M.Si.', 'Buku catatan kosakata, alat tulis, kamus saku/aplikasi kamus di smartphone, dan botol minum.');
INSERT INTO `ekskul` VALUES('14', 'TARI TRADISIONAL', 'Melestarikan seni tari daerah Nusantara (seperti Tari Malangan, Tari Remo, dan tari kreasi nusantara) dengan melatih keluwesan gerak, wiraga, wirama, dan wirasa untuk pentas seni maupun festival budaya.', '1795069432_tari.jpg', 'Selasa, 15.30 - 17.00 WIB (Aula / Lap Indor)', '', NULL, 'aktif', 'Abdullah Muhammad Yusuf', 'Kain jarik/sampur (selendang tari), pakaian latihan yang nyaman bergerak, kaos kaki, ikat rambut (putri), dan botol minum.');
INSERT INTO `ekskul` VALUES('15', 'MODERN DANCE', 'Ekstrakurikuler seni tari modern yang melatih kelenturan gerak, energi panggung, kekompakan koreografi (hip-hop, K-pop cover, contemporary), serta kreativitas aransemen musik untuk tampil di pentas seni dan kompetisi dance.', '1795061790_dance.jpg', 'Kamis, 15.30 - 17.00 WIB (Lap Indor)', '', NULL, 'aktif', 'Rendra Orlando S.', 'Pakaian latihan yang nyaman dan menyerap keringat, sepatu sneakers/dance yang lentur, ikat rambut (bagi yang berambut panjang), dan air minum.');
INSERT INTO `ekskul` VALUES('16', 'KARAWITAN', 'Pelestarian seni budaya tradisional Jawa di Aula Depan melalui latihan memainkan instrumen gamelan (saron, bonang, kendang, gong) serta olah vokal sinden untuk mengiringi acara adat dan pentas budaya sekolah.', '1795061632_karawitan.jpg', 'Rabu, 15.30 - 17.00 WIB (Aula Depan)', '', NULL, 'aktif', 'Sony Eko Wandono, S.Sn.', 'Pakaian sopan dan rapi (kaos kaki bersih untuk masuk area gamelan), buku catatan notasi gending, dan alat tulis.');
INSERT INTO `ekskul` VALUES('17', 'SENI MUSIK', 'Menyalurkan bakat bermusik siswa dalam memainkan instrumen band maupun akustik (gitar, bass, drum, keyboard) dan olah vokal, melatih aransemen lagu, serta kekompakan tampil di panggung acara sekolah.', '1795069282_seni musik.jpg', 'Kamis, 16.00 - 17.30 WIB (Aula Depan)', '', NULL, 'aktif', 'Jeni Astina Siringo', 'Alat musik pribadi seperti gitar/bass/stick drum (jika ada), pick gitar, kabel jack pribadi, dan catatan lirik/chord lagu.');
INSERT INTO `ekskul` VALUES('18', 'BASKET', 'Wadah bagi siswa-siswi yang memiliki minat dan bakat di bidang olahraga bola basket. Ekstrakurikuler ini melatih teknik dasar (dribbling, passing, shooting), strategi permainan tim, serta ketahanan fisik untuk menghadapi berbagai turnamen antar pelajar.', '1795061030_basketball.jpeg', 'Selasa 16.00 - 18.00 & Sabtu 15.00 - 17.00 WIB (Lap Indor)', '', '4', 'aktif', 'Eva Rahmawati', 'Sepatu basket/olahraga sol karet (non-marking), kaos olahraga/jersey latihan, celana pendek olahraga, kaos kaki tebal, dan botol air minum pribadi.');
INSERT INTO `ekskul` VALUES('19', 'JURNALISTIK', 'Wadah bagi siswa yang gemar menulis, meliput berita, wawancara narasumber, dan mendokumentasikan kegiatan sekolah untuk dikelola menjadi artikel berita, buletin, maupun konten publikasi resmi SMKN 5 Malang.', '1795061527_jurnalis.jpg', 'Kamis, 15.30 - 17.00 WIB (Auditorium)', '', NULL, 'aktif', 'R. H. Authonul Muther', 'Buku catatan kecil (notes), pulpen, smartphone/alat perekam suara untuk liputan dan wawancara, serta laptop (jika ada)');
INSERT INTO `ekskul` VALUES('20', 'PMR', 'Palang Merah Remaja yang membekali siswa dengan keterampilan pertolongan pertama pada kecelakaan (P3K), penanganan medis darurat saat upacara atau acara sekolah, serta kegiatan kemanusiaan.', '1795069049_1794984776_pmr.png', 'Selasa, 15.45 - 17.00 WIB (G104)', '', NULL, 'aktif', 'M. Sururi Al Faruq, S.Pd.', 'Buku saku kepalangmerahan, alat tulis, slayer/syal PMR, perlengkapan P3K dasar latihan (perban gulung/mitela), dan air minum.');
INSERT INTO `ekskul` VALUES('21', 'KARYA ILMIAH REMAJA', 'Ekstrakurikuler riset dan inovasi bagi siswa yang gemar bereksperimen, memecahkan masalah, serta menyusun karya tulis ilmiah maupun produk teknologi tepat guna untuk diikutsertakan dalam lomba penelitian pelajar.', '1795061669_kir.jpg', '', '', NULL, 'aktif', NULL, 'Buku catatan riset, alat tulis, laptop (untuk penyusunan proposal dan laporan), serta bahan eksperimen sesuai proyek kelompok.');
INSERT INTO `ekskul` VALUES('22', 'ROHIS', NULL, 'default.jpg', 'Senin, 15.30 - 17.00 WIB (Masjid)', NULL, NULL, 'aktif', 'Syifaun Nisa Azzahra, S.H', NULL);
INSERT INTO `ekskul` VALUES('23', 'SENI RUPA MURNI', 'Ekstrakurikuler bagi siswa yang gemar menggambar dan melukis untuk mengeksplorasi teknik sketsa, arsiran, melukis di atas kanvas (cat air/akrilik), serta pameran karya seni rupa.', '1795069339_seni rupa murni.jpg', 'Senin, 15.30 - 17.00 WIB (Auditorium)', '', NULL, 'aktif', 'Miftahul Khoiriyah', 'Buku gambar/sketsa (sketchbook), pensil gambar (2B–8B), penghapus, kuas lukis, palet, dan cat lukis pribadi (sesuai proyek karya).');
INSERT INTO `ekskul` VALUES('24', 'SENI BATIK & SABLON', 'Wadah keterampilan kriya tekstil di Bengkel Tekstil yang melatih teknik membatik (mencanting, pewarnaan kain) serta teknik sablon manual untuk menghasilkan karya tekstil bernilai seni dan wirausaha.', '1795069230_seni batik.jpg', 'Rabu, 15.30 - 17.00 WIB (Bengkel Tekstil)', '', NULL, 'aktif', 'Latifatul Amalia, S.Pd., Gr.', 'Celemek/pakaian kerja agar seragam tidak terkena noda cat atau malam batik, buku sketsa motif, pensil, dan kain lap kecil.');
INSERT INTO `ekskul` VALUES('25', 'MODELLING', 'Wadah pengembangan bakat di bidang catwalk, postur tubuh, ekspresi panggung, photoshoot pose, serta public confidence untuk tampil di pagelaran busana (fashion show) maupun lomba modelling pelajar.', '1795061750_modeling.jpg', 'Rabu, 15.30 - 17.00 WIB (Lap Indor)', '', NULL, 'aktif', 'Abdulah Sidik, SAB', 'Pakaian latihan yang pas di badan (fit body) agar postur terlihat jelas, sepatu heels latihan (putri) atau sepatu pantofel/sneakers rapi (putra), ikat rambut, dan air minum.');
INSERT INTO `ekskul` VALUES('26', 'MUAYTHAI', 'Olahraga bela diri asal Thailand yang melatih kekuatan fisik, kardio, serta teknik serangan kombinasi pukulan, sikutan, lutut, dan tendangan untuk kebugaran maupun prestasi kejuaraan ring.', '1795061988_muaythai.jpg', 'Kamis, 15.30 - 17.00 WIB (Lap Voli / Indor)', '', NULL, 'aktif', 'Fabian Riski', 'Kaos olahraga, celana pendek olahraga/celana Muaythai, handwrap (pelilit tangan), sarung tinju/pelindung pribadi (jika sudah punya), handuk kecil, dan air minum.');
INSERT INTO `ekskul` VALUES('27', 'PIK-R', 'Pusat Informasi dan Konseling Remaja yang menjadi wadah edukasi sebaya, pengembangan karakter remaja, pelatihan komunikasi empatik, serta kampanye kesehatan mental dan generasi berencana di lingkungan sekolah.', '1795068921_Screenshot 2026-11-19 131445.png', 'Kamis, 15.30 - 17.00 WIB (Kelas / BK)', '', NULL, 'aktif', 'Divadinda Nasywa Azizah', 'Buku catatan materi, alat tulis, dan botol air minum pribadi.');
INSERT INTO `ekskul` VALUES('28', 'SCULPTING ANIMASI', 'Ekstrakurikuler kreatif di Bengkel Anim E 104 yang mempelajari teknik pemodelan dan pemahatan karakter 3D (3D digital sculpting), anatomi karakter, serta dasar animasi untuk industri kreatif.', '1795069171_3d-sculpting.webp', 'Sabtu, 08.00 - 10.00 WIB (Bengkel Anim E 104)', '', NULL, 'aktif', 'Erfian Andiantoro', 'Laptop pribadi (jika diperlukan), pen tablet/stylus (jika memiliki), flashdisk untuk menyimpan file proyek 3D, dan buku sketsa ide.');

DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `status_baca` enum('belum','sudah') DEFAULT 'belum',
  `waktu` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notifikasi` VALUES('4', '4', '🎉 Selamat! Akun Anda telah DITERIMA (di-ACC) sebagai Pelatih oleh Admin. Sekarang Anda sudah bisa memilih dan mengelola ekstrakurikuler.', 'belum', '2026-11-19 18:21:22');
INSERT INTO `notifikasi` VALUES('6', '1', 'Pelatih sinta (12) mengajukan diri melatih ekskul BASKET dan menunggu ACC Admin.', 'belum', '2026-11-19 18:29:02');
INSERT INTO `notifikasi` VALUES('7', '4', '🎉 Selamat! Pengajuan Anda sebagai Pelatih ekstrakurikuler BASKET telah DITERIMA (di-ACC) oleh Admin.', 'belum', '2026-11-19 18:31:28');
INSERT INTO `notifikasi` VALUES('8', '5', '🎉 Selamat! Pendaftaran kamu di ekstrakurikuler PMR telah DITERIMA oleh Admin.', 'belum', '2026-11-19 18:37:49');

DROP TABLE IF EXISTS `pendaftaran_ekskul`;
CREATE TABLE `pendaftaran_ekskul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `ekskul_id` int(11) NOT NULL,
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `tanggal_daftar` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `ekskul_id` (`ekskul_id`),
  CONSTRAINT `pendaftaran_ekskul_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pendaftaran_ekskul_ibfk_2` FOREIGN KEY (`ekskul_id`) REFERENCES `ekskul` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pendaftaran_ekskul` VALUES('1', '5', '20', 'diterima', '2026-11-19 18:37:44');

DROP TABLE IF EXISTS `prestasi_foto`;
CREATE TABLE `prestasi_foto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ekskul_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ekskul_id` (`ekskul_id`),
  CONSTRAINT `prestasi_foto_ibfk_1` FOREIGN KEY (`ekskul_id`) REFERENCES `ekskul` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `prestasi_foto` VALUES('1', '18', '1795061030_prestasi_0_prestasi_basket.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('2', '26', '1795061988_prestasi_0_prestasi_muaythai.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('3', '26', '1795061988_prestasi_1_prestasi_muaythai1.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('4', '2', '1795062639_prestasi_0_prestasi_paski.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('5', '2', '1795062639_prestasi_1_prestasi_paski1.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('6', '2', '1795062639_prestasi_2_prestasi_paski2.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('7', '18', '1795067797_prestasi_0_Screenshot 2026-11-19 114947.png', NULL);
INSERT INTO `prestasi_foto` VALUES('8', '18', '1795067797_prestasi_1_Screenshot 2026-11-19 125306.png', NULL);
INSERT INTO `prestasi_foto` VALUES('9', '18', '1795067797_prestasi_2_Screenshot 2026-11-19 125539.png', NULL);
INSERT INTO `prestasi_foto` VALUES('10', '18', '1795067833_prestasi_0_Screenshot 2026-11-19 114937.png', NULL);
INSERT INTO `prestasi_foto` VALUES('11', '20', '1795069049_prestasi_0_1794978781_prestasi_0_sertif opon2.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('12', '20', '1795069049_prestasi_1_1794978781_prestasi_1_sertif opon1.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('13', '20', '1795069049_prestasi_2_1794978781_prestasi_2_sertif opon.jpg', NULL);
INSERT INTO `prestasi_foto` VALUES('14', '20', '1795069049_prestasi_3_1794978781_prestasi_3_OPON.jpg', NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nomor_induk` varchar(50) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_plain` varchar(100) DEFAULT NULL,
  `role` enum('admin','siswa','pelatih') NOT NULL,
  `status_akun` enum('aktif','pending') DEFAULT 'aktif',
  `foto_profil` varchar(255) DEFAULT 'default_user.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_induk` (`nomor_induk`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES('1', 'Ami', '20', NULL, NULL, NULL, '$2y$12$8Adlobig11RzkwYS2tTbS.Hb8hhvdWfw3QWT6RqfVfqNHXTWrcBpu', NULL, 'admin', 'aktif', 'default_user.png');
INSERT INTO `users` VALUES('4', 'sinta', '12', NULL, NULL, NULL, '$2y$12$xs4mxJzcEfu6mbsfb6iHv.MKH.uaUWQPZ4XSfYWmRZLXKOkrwnD.K', '12345', 'pelatih', 'aktif', 'default_user.png');
INSERT INTO `users` VALUES('5', 'INSANIN KAMILIA AN NISAA', '14699/1801.063', 'XI RPL 2', 'Perempuan', 'Jawa Timur, Malang, Singosari, Langlang', '$2y$12$GD2549xD7w94gnLoaaD7p.gIATDGWYmI0af66.z5gcXQX6BIov9Z6', '12345', 'siswa', 'aktif', 'default_user.png');

SET FOREIGN_KEY_CHECKS=1;
