<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)$_POST['user_id'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    if (empty($user_id) || empty($password)) {
        $error = 'User dan password wajib diisi!';
    } elseif ($password != $password_confirm) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Password berhasil direset!';
        } else {
            $error = 'Gagal reset password!';
        }
        $stmt->close();
    }
}

$page_title = 'Reset Password';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-key me-2"></i>Reset Password</h2>
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
        
        <div class="form-container">
            <form method="POST" action="" data-validate="true">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Pilih User <span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" id="user_id" name="user_id" required>
                        <option value="">Pilih User</option>
                        <?php
                        $stmt = $conn->query("SELECT id, nama, username, role FROM users WHERE role IN ('dokter', 'pemilik') ORDER BY role, nama");
                        while ($user = $stmt->fetch_assoc()):
                        ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo $user['nama']; ?> (<?php echo $user['username']; ?>) - <?php echo ucfirst($user['role']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-custom" id="password" name="password" required minlength="6">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-custom" id="password_confirm" name="password_confirm" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
