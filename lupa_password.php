<?php
require_once 'config.php';
require_once 'koneksi.php';

$error = '';
$success = '';

// Jika sudah login, redirect
if (isLoggedIn()) {
    redirectByRole();
}

// Proses lupa password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_email = sanitize($_POST['username_email']);
    
    if (empty($username_email)) {
        $error = 'Username atau email harus diisi!';
    } else {
        // Cari user dengan role pemilik
        $stmt = $conn->prepare("SELECT id, nama, email, role FROM users WHERE (username = ? OR email = ?) AND role = 'pemilik'");
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Generate token
            $token = generateToken();
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Hapus token lama
            $stmt2 = $conn->prepare("DELETE FROM reset_tokens WHERE user_id = ?");
            $stmt2->bind_param("i", $user['id']);
            $stmt2->execute();
            $stmt2->close();
            
            // Insert token baru
            $stmt3 = $conn->prepare("INSERT INTO reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt3->bind_param("iss", $user['id'], $token, $expires_at);
            
            if ($stmt3->execute()) {
                // Redirect ke halaman reset dengan token
                header('Location: ' . APP_URL . '/reset_password_pemilik.php?token=' . $token);
                exit;
            } else {
                $error = 'Terjadi kesalahan saat membuat token reset!';
            }
            $stmt3->close();
        } else {
            $error = 'Username atau email tidak ditemukan atau bukan akun pemilik!';
        }
        $stmt->close();
    }
}
?>
<?php 
$page_title = 'Lupa Password';
include 'layouts/header.php'; 
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <i class="fas fa-key fa-3x text-primary"></i>
        </div>
        <h2 class="login-title">Lupa Password</h2>
        <p class="text-center text-muted mb-4">Masukkan username atau email Anda untuk reset password</p>
        
        <div class="alert alert-info alert-custom">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Catatan:</strong> Fitur ini hanya untuk pemilik hewan. Admin dan dokter harus menghubungi administrator untuk reset password.
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" data-validate="true">
            <div class="mb-3">
                <label for="username_email" class="form-label">Username atau Email</label>
                <input type="text" class="form-control form-control-custom" id="username_email" name="username_email" required autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary-custom-solid btn-custom w-100">
                <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset
            </button>
        </form>
        
        <div class="text-center mt-4">
            <a href="<?php echo APP_URL; ?>/login.php" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
            </a>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>


