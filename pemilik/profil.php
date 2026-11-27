<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

$error = '';
$success = '';

// Ambil data pemilik
$stmt = $conn->prepare("SELECT p.*, u.username, u.email FROM pemilik p JOIN users u ON p.id_user = u.id WHERE p.id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitize($_POST['nama']);
    $alamat = sanitize($_POST['alamat']);
    $telepon = sanitize($_POST['telepon']);
    $email = sanitize($_POST['email']);
    
    if (empty($nama) || empty($alamat) || empty($telepon)) {
        $error = 'Nama, alamat, dan telepon wajib diisi!';
    } else {
        $conn->begin_transaction();
        
        try {
            // Update pemilik
            $stmt = $conn->prepare("UPDATE pemilik SET nama = ?, alamat = ?, telepon = ? WHERE id_user = ?");
            $stmt->bind_param("sssi", $nama, $alamat, $telepon, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
            
            // Update user
            $stmt2 = $conn->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
            $stmt2->bind_param("ssi", $nama, $email, $_SESSION['user_id']);
            $stmt2->execute();
            $stmt2->close();
            
            $_SESSION['nama'] = $nama;
            
            $conn->commit();
            $success = 'Profil berhasil diupdate!';
            // AMBIL ULANG DATA PEMILIK AGAR FORM TERISI DATA TERBARU
            $stmt = $conn->prepare("SELECT p.*, u.username, u.email 
                                    FROM pemilik p 
                                    JOIN users u ON p.id_user = u.id 
                                    WHERE p.id_user = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $pemilik = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Gagal mengupdate profil!';
        }
    }
}

$page_title = 'Profil Saya';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-user-circle me-2"></i>Profil Saya</h2>
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
                    <input type="text" class="form-control form-control-custom" id="nama" name="nama" value="<?php echo $pemilik['nama']; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control form-control-custom" id="username" value="<?php echo $pemilik['username']; ?>" disabled>
                    <small class="text-muted">Username tidak dapat diubah</small>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-custom" id="email" name="email" value="<?php echo $pemilik['email'] ?? ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-custom" id="alamat" name="alamat" rows="3" required><?php echo $pemilik['alamat']; ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control form-control-custom" id="telepon" name="telepon" value="<?php echo $pemilik['telepon']; ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>


