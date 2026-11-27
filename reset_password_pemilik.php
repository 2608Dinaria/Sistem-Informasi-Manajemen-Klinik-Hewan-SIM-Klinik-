<?php
require_once 'config.php';
require_once 'koneksi.php';

$error = '';
$success = '';
$valid_token = false;
$user_id = null;

// Jika sudah login, redirect
if (isLoggedIn()) {
    redirectByRole();
}

// Validasi token
if (isset($_GET['token'])) {
    $token = sanitize($_GET['token']);
    
    $stmt = $conn->prepare("SELECT user_id, expires_at, used FROM reset_tokens WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $token_data = $result->fetch_assoc();
        
        // Cek apakah token sudah digunakan
        if ($token_data['used'] == 1) {
            $error = 'Token sudah digunakan! Silakan request reset password baru.';
        } 
        // Cek apakah token sudah expired
        elseif (strtotime($token_data['expires_at']) < time()) {
            $error = 'Token sudah kadaluarsa! Silakan request reset password baru.';
        } 
        // Cek apakah user adalah pemilik
        else {
            $stmt2 = $conn->prepare("SELECT id, role FROM users WHERE id = ? AND role = 'pemilik'");
            $stmt2->bind_param("i", $token_data['user_id']);
            $stmt2->execute();
            $user_result = $stmt2->get_result();
            
            if ($user_result->num_rows == 1) {
                $valid_token = true;
                $user_id = $token_data['user_id'];
            } else {
                $error = 'Token tidak valid untuk akun ini!';
            }
            $stmt2->close();
        }
    } else {
        $error = 'Token tidak valid!';
    }
    $stmt->close();
} else {
    $error = 'Token tidak ditemukan!';
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    if (empty($password) || empty($password_confirm)) {
        $error = 'Password dan konfirmasi password harus diisi!';
    } elseif ($password != $password_confirm) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            // Mark token as used
            $token = sanitize($_GET['token']);
            $stmt2 = $conn->prepare("UPDATE reset_tokens SET used = 1 WHERE token = ?");
            $stmt2->bind_param("s", $token);
            $stmt2->execute();
            $stmt2->close();
            
            $success = 'Password berhasil direset! Silakan login dengan password baru.';
            header('refresh:2;url=' . APP_URL . '/login.php');
        } else {
            $error = 'Terjadi kesalahan saat reset password!';
        }
        $stmt->close();
    }
}
?>
<?php 
$page_title = 'Reset Password';
include 'layouts/header.php'; 
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <i class="fas fa-lock fa-3x text-primary"></i>
        </div>
        <h2 class="login-title">Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
            <?php if (!$valid_token): ?>
                <div class="text-center mt-4">
                    <a href="<?php echo APP_URL; ?>/lupa_password.php" class="btn btn-primary-custom-solid btn-custom">
                        <i class="fas fa-redo me-2"></i>Request Reset Password Baru
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($valid_token && !$success): ?>
            <p class="text-center text-muted mb-4">Masukkan password baru Anda</p>
            
            <form method="POST" action="" data-validate="true">
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control form-control-custom" id="password" name="password" required minlength="6" autofocus>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control form-control-custom" id="password_confirm" name="password_confirm" required>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom w-100">
                    <i class="fas fa-save me-2"></i>Reset Password
                </button>
            </form>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="<?php echo APP_URL; ?>/login.php" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
            </a>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>


