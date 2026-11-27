<?php
if (!isLoggedIn() || $_SESSION['role'] != 'dokter') {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-user-md"></i> Dokter Panel</h4>
        <small class="text-white-50"><?php echo APP_INSTITUTION; ?></small>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo APP_URL; ?>/dokter/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/dokter/jadwal_saya.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['jadwal_saya.php', 'tambah_jadwal.php']) ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Jadwal Praktik
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/dokter/appointment_saya.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'appointment_saya.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> Appointment
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/dokter/rekam_medis.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['rekam_medis.php', 'tambah_rekam_medis.php']) ? 'active' : ''; ?>">
                <i class="fas fa-file-medical"></i> Rekam Medis
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/dokter/jadwal_vaksin_hari_ini.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'jadwal_vaksin_hari_ini.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> Jadwal Vaksinasi
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/dokter/vaksinasi.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['vaksinasi.php', 'tambah_vaksinasi.php']) ? 'active' : ''; ?>">
                <i class="fas fa-syringe"></i> Vaksinasi
            </a>
        </li>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/logout.php"
            onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>


