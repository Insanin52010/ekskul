<?php
function renderOpsiKelas($kelas_terpilih = '') {
    $tingkat = ['XI', 'X'];
    $jurusan_2_kelas = ['KKR', 'KKA', 'KTK', 'DPB', 'ANIMASI'];
    $jurusan_3_kelas = ['RPL', 'TKJ', 'DKV'];
    $semua_jurusan = ['KKR', 'KKA', 'KTK', 'DPB', 'ANIMASI', 'RPL', 'TKJ', 'DKV'];

    echo '<option value="">-- Pilih Kelas --</option>';
    foreach ($tingkat as $t) {
        foreach ($semua_jurusan as $j) {
            $jumlah_kelas = in_array($j, $jurusan_2_kelas) ? 2 : 3;
            for ($no = 1; $no <= $jumlah_kelas; $no++) {
                $nama_kelas = "$t $j $no";
                $selected = ($kelas_terpilih === $nama_kelas) ? 'selected' : '';
                echo "<option value=\"$nama_kelas\" $selected>$nama_kelas</option>";
            }
        }
    }
}
?>