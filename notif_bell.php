<?php
// Komponen Lonceng Notifikasi Navbar untuk Siswa, Pelatih, dan Admin
$bell_user_id =$_SESSION['user_id'] ?? 0;
$bell_preview = (isset($is_admin_preview) && $is_admin_preview);$bell_query   = (!$bell_preview &&$bell_user_id) 
    ? mysqli_query($conn, "SELECT * FROM notifikasi WHERE user_id = '$bell_user_id' ORDER BY waktu DESC") 
    : false;
$bell_count   = ($bell_query) ? mysqli_num_rows($bell_query) : 0;
?>

<div class="notif-bell-wrapper" style="position: relative; display: inline-flex; align-items: center;">
    <button type="button" onclick="toggleNotifDropdown(event)" title="Notifikasi" style="position: relative; width: 42px; height: 42px; border-radius: 50%; background: #ffffff; border: 1.5px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.06); padding: 0;">
        🔔
        <?php if ($bell_count > 0) : ?>
            <span style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 800; min-width: 19px; height: 19px; padding: 0 5px; border-radius: 50px; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; line-height: 1;">
                <?= $bell_count; ?>
            </span>
        <?php endif; ?>
    </button>

    <div id="notifDropdownBox" onclick="event.stopPropagation()" style="display: none; position: absolute; right: 0; top: 52px; width: 340px; max-width: 88vw; background: #ffffff; border-radius: 18px; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.16); border: 1.5px solid #e2e8f0; z-index: 9999; overflow: hidden; text-align: left;">
        <div style="padding: 13px 16px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
            <strong style="font-size: 13.5px;">🔔 Notifikasi (<?= $bell_count; ?>)</strong>
            <?php if ($bell_count > 0 && !$bell_preview) : ?>
                <a href="?baca_semua_notif=1" style="font-size: 11px; color: #fef08a; background: rgba(255,255,255,0.16); padding: 4px 10px; border-radius: 50px; text-decoration: none; font-weight: 700; box-shadow: none;">Tandai Semua Dibaca</a>
            <?php endif; ?>
        </div>

        <div style="max-height: 310px; overflow-y: auto; padding: 8px;">
            <?php if ($bell_preview) : ?>
                <div style="padding: 20px; text-align: center; color: #64748b; font-size: 12.5px;">
                    Mode Pratinjau Admin — Notifikasi pengguna nonaktif saat pratinjau.
                </div>
            <?php elseif ($bell_count > 0) : ?>
                <?php while ($bn = mysqli_fetch_assoc($bell_query)) : ?>
                    <div style="padding: 10px 12px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                        <div style="font-size: 12.5px; color: #1e293b; line-height: 1.45;">
                            <?= htmlspecialchars($bn['pesan']); ?>
                            <div style="font-size: 10.5px; color: #64748b; margin-top: 4px;">🕒 <?= $bn['waktu']; ?></div>
                        </div>
                        <a href="?baca_notif=<?= $bn['id']; ?>" title="Hapus notifikasi" style="color: #ef4444; background: #fee2e2; padding: 3px 8px; border-radius: 50px; font-size: 11px; font-weight: 800; text-decoration: none; flex-shrink: 0; box-shadow: none;">✕</a>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div style="padding: 26px 16px; text-align: center; color: #94a3b8; font-size: 13px;">
                    Belum ada notifikasi baru ✨
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleNotifDropdown(e) {
    e.stopPropagation();
    const box = document.getElementById('notifDropdownBox');
    box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
}
document.addEventListener('click', function() {
    const box = document.getElementById('notifDropdownBox');
    if (box) box.style.display = 'none';
});
</script>