<?php
if (!isLoggedIn() || $_SESSION['role'] != 'pemilik') {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

// Jika session nama tidak ada, siapkan fallback
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : "Pengguna";
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-user"></i> <?php echo $nama_user; ?></h4>
        <small class="text-white-50"><?php echo APP_INSTITUTION; ?></small>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo APP_URL; ?>/pemilik/dashboard.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="<?php echo APP_URL; ?>/pemilik/profil.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i> Profil Saya
            </a>
        </li>

        <li>
            <a href="<?php echo APP_URL; ?>/pemilik/data_hewan_saya.php"
               class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['data_hewan_saya.php', 'tambah_hewan.php']) ? 'active' : ''; ?>">
                <i class="fas fa-paw"></i> Data Hewan Saya
            </a>
        </li>

        <li>
            <a href="<?php echo APP_URL; ?>/pemilik/appointment.php"
               class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['appointment.php', 'buat_appointment.php']) ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> Appointment
            </a>
        </li>

        <li>
            <a href="<?php echo APP_URL; ?>/pemilik/riwayat_vaksinasi.php"
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'riwayat_vaksinasi.php' ? 'active' : ''; ?>">
                <i class="fas fa-syringe"></i> Riwayat Vaksinasi
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
