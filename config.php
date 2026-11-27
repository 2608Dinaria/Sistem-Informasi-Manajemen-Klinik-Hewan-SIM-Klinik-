<?php
// Konfigurasi Sistem Informasi Manajemen Klinik Hewan Dinas Peternakan

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'klinik_hewan_dinas1');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Konfigurasi Aplikasi
define('APP_NAME', 'Sistem Informasi Manajemen Klinik Hewan');
define('APP_INSTITUTION', 'Dinas Peternakan');
define('APP_URL', 'http://localhost/lintasdisiplinilmu');

// Konfigurasi Session
define('SESSION_LIFETIME', 3600); // 1 jam

// Konfigurasi Email (untuk reset password)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk check login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Fungsi untuk check role
function checkRole($allowedRoles) {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/login.php');
        exit;
    }
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        header('Location: ' . APP_URL . '/login.php');
        exit;
    }
}

// Fungsi untuk redirect berdasarkan role
function redirectByRole() {
    if (isLoggedIn()) {
        $role = $_SESSION['role'];
        switch ($role) {
            case 'admin':
                header('Location: ' . APP_URL . '/admin/dashboard.php');
                break;
            case 'dokter':
                header('Location: ' . APP_URL . '/dokter/dashboard.php');
                break;
            case 'pemilik':
                header('Location: ' . APP_URL . '/pemilik/dashboard.php');
                break;
        }
        exit;
    }
}

// Fungsi untuk format tanggal Indonesia
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $tanggal = date('Y-m-d', strtotime($tanggal));
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Fungsi untuk format waktu
function formatWaktu($waktu) {
    return date('H:i', strtotime($waktu));
}

// Fungsi untuk generate token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Fungsi untuk sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>


