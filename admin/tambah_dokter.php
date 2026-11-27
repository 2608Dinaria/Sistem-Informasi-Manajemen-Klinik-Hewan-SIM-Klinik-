<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitize($_POST['nama']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $spesialis = sanitize($_POST['spesialis']);
    $no_sip = sanitize($_POST['no_sip']);
    
    if (empty($nama) || empty($username) || empty($password) || empty($spesialis)) {
        $error = 'Nama, username, password, dan spesialis wajib diisi!';
    } elseif ($password != $password_confirm) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'dokter';
            $status = 'aktif';
            
            $conn->begin_transaction();
            
            try {
                // Insert user
                $stmt = $conn->prepare("INSERT INTO users (nama, username, password, email, role, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $nama, $username, $hashed_password, $email, $role, $status);
                $stmt->execute();
                $user_id = $conn->insert_id;
                $stmt->close();
                
                // Insert dokter
                $stmt2 = $conn->prepare("INSERT INTO dokter (id_user, nama, spesialis, no_sip) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("isss", $user_id, $nama, $spesialis, $no_sip);
                $stmt2->execute();
                $stmt2->close();
                
                $conn->commit();
                $success = 'Dokter berhasil ditambahkan!';
                header('refresh:2;url=' . APP_URL . '/admin/dashboard.php');
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Gagal menambahkan dokter!';
            }
        }
    }
}

$page_title = 'Tambah Dokter';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-user-md me-2"></i>Tambah Dokter</h2>
            <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
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
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" id="nama" name="nama" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" id="username" name="username" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-custom" id="password" name="password" required minlength="6">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-custom" id="password_confirm" name="password_confirm" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="spesialis" class="form-label">Spesialis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" id="spesialis" name="spesialis" required placeholder="Contoh: Bedah Hewan, Umum">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="no_sip" class="form-label">No. SIP</label>
                        <input type="text" class="form-control form-control-custom" id="no_sip" name="no_sip" placeholder="Nomor Surat Izin Praktik">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>
