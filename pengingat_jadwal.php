<?php
if (!function_exists('cekPengingatJadwalHariIni')) {
    function cekPengingatJadwalHariIni($conn, $user_id, $role) {
        if (!in_array($role, ['siswa', 'pelatih']) || empty($user_id)) {
            return;
        }

        date_default_timezone_set('Asia/Jakarta');
        $map_hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];
        $hari_ini = $map_hari[date('l')] ?? '';
        $tanggal_hari_ini = date('Y-m-d');

        if (empty($hari_ini)) {
            return;
        }

        // Buat tabel log agar pengingat untuk ekskul & tanggal yang sama tidak dikirim berulang kali
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `log_pengingat_jadwal` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `ekskul_id` INT NOT NULL,
            `tanggal` DATE NOT NULL,
            UNIQUE KEY `unik_pengingat` (`user_id`, `ekskul_id`, `tanggal`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $uid = (int)$user_id;

        if ($role === 'siswa') {
            $query_ekskul = mysqli_query($conn, "
                SELECT e.id, e.nama_ekskul, e.jadwal, e.perlengkapan 
                FROM pendaftaran_ekskul p 
                JOIN ekskul e ON p.ekskul_id = e.id 
                WHERE p.siswa_id = '$uid' AND p.status = 'diterima'
            ");
        } else {
            $query_ekskul = mysqli_query($conn, "
                SELECT id, nama_ekskul, jadwal, perlengkapan 
                FROM ekskul 
                WHERE pelatih_id = '$uid' AND status_pelatih_ekskul = 'aktif'
            ");
        }

        if (!$query_ekskul) {
            return;
        }

        while ($eks = mysqli_fetch_assoc($query_ekskul)) {
            $jadwal = (string)($eks['jadwal'] ?? '');
            if (stripos($jadwal, $hari_ini) !== false) {
                $eid = (int)$eks['id'];
                $cek_log = mysqli_query($conn, "
                    SELECT id FROM log_pengingat_jadwal 
                    WHERE user_id = '$uid' AND ekskul_id = '$eid' AND tanggal = '$tanggal_hari_ini'
                ");

                if ($cek_log && mysqli_num_rows($cek_log) === 0) {
                    $nama_eks = $eks['nama_ekskul'];
                    $info_barang = !empty(trim((string)$eks['perlengkapan']))
                        ? " Jangan lupa bawa: " . trim($eks['perlengkapan'])
                        : "";

                    $teks_pengingat = ($role === 'siswa')
                        ? "⏰ Pengingat Hari Ini ($hari_ini): Kamu ada jadwal ekstrakurikuler $nama_eks ($jadwal).$info_barang"
                        : "⏰ Pengingat Melatih Hari Ini ($hari_ini): Jadwal ekstrakurikuler $nama_eks ($jadwal).";

                    $pesan_safe = mysqli_real_escape_string($conn, $teks_pengingat);
                    mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan) VALUES ('$uid', '$pesan_safe')");
                    mysqli_query($conn, "INSERT IGNORE INTO log_pengingat_jadwal (user_id, ekskul_id, tanggal) VALUES ('$uid', '$eid', '$tanggal_hari_ini')");
                }
            }
        }
    }
}