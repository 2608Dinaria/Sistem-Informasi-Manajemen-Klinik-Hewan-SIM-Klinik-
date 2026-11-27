<?php
if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-hospital"></i> Admin Panel</h4>
        <small class="text-white-50"><?php echo APP_INSTITUTION; ?></small>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/users_admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users_admin.php' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Kelola Admin
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/tambah_dokter.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tambah_dokter.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i> Tambah Dokter
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/jadwal_dokter.php"
               class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['jadwal_dokter.php','tambah_jadwal.php','edit_jadwal.php']) ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Jadwal Dokter
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/jadwal_vaksinasi.php"
               class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['jadwal_vaksinasi.php','tambah_jadwal_vaksinasi.php','edit_jadwal_vaksinasi.php']) ? 'active' : ''; ?>">
                <i class="fas fa-syringe"></i> Jadwal Vaksinasi
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/konfirmasi_vaksinasi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'konfirmasi_vaksinasi.php' ? 'active' : ''; ?>">
                <i class="fas fa-check-double"></i> Konfirmasi Vaksin
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/pemilik.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['pemilik.php', 'tambah_pemilik.php', 'edit_pemilik.php']) ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Data Pemilik
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/pasien.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['pasien.php', 'tambah_pasien.php', 'edit_pasien.php', 'detail_pasien.php']) ? 'active' : ''; ?>">
                <i class="fas fa-paw"></i> Data Pasien
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/laporan_pasien.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laporan_pasien.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Laporan Pasien
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/admin/reset_password.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reset_password.php' ? 'active' : ''; ?>">
                <i class="fas fa-key"></i> Reset Password
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/logout.php"
            onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>


