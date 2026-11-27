<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pasien
$stmt = $conn->prepare("SELECT * FROM pasien WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pasien = $result->fetch_assoc();
$stmt->close();

if (!$pasien) {
    header('Location: ' . APP_URL . '/admin/pasien.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemilik = (int)$_POST['id_pemilik'];
    $nama_hewan = sanitize($_POST['nama_hewan']);
    $jenis = sanitize($_POST['jenis']);
    $ras = sanitize($_POST['ras']);
    $umur = !empty($_POST['umur']) ? (int)$_POST['umur'] : null;
    $gender = sanitize($_POST['gender']);
    $berat_badan = !empty($_POST['berat_badan']) ? (float)$_POST['berat_badan'] : null;
    
    if (empty($id_pemilik) || empty($nama_hewan) || empty($jenis) || empty($gender)) {
        $error = 'Pemilik, nama hewan, jenis, dan gender wajib diisi!';
    } else {
        $stmt = $conn->prepare("UPDATE pasien SET id_pemilik = ?, nama_hewan = ?, jenis = ?, ras = ?, umur = ?, gender = ?, berat_badan = ? WHERE id = ?");
        $stmt->bind_param("isssisdi", $id_pemilik, $nama_hewan, $jenis, $ras, $umur, $gender, $berat_badan, $id);
        
        if ($stmt->execute()) {
            $success = 'Data pasien berhasil diupdate!';
            header('refresh:2;url=' . APP_URL . '/admin/pasien.php');
        } else {
            $error = 'Gagal mengupdate data!';
        }
        $stmt->close();
    }
}

// Ambil daftar pemilik
$pemilik_list = $conn->query("SELECT * FROM pemilik ORDER BY nama");

$page_title = 'Edit Pasien';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-edit me-2"></i>Edit Pasien</h2>
            <a href="<?php echo APP_URL; ?>/admin/pasien.php" class="btn btn-secondary btn-custom">
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
                    <label for="id_pemilik" class="form-label">Pemilik <span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" id="id_pemilik" name="id_pemilik" required>
                        <option value="">Pilih Pemilik</option>
                        <?php 
                        $pemilik_list->data_seek(0);
                        while ($p = $pemilik_list->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $p['id'] == $pasien['id_pemilik'] ? 'selected' : ''; ?>>
                                <?php echo $p['nama']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="nama_hewan" class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" id="nama_hewan" name="nama_hewan" value="<?php echo $pasien['nama_hewan']; ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis" class="form-label">Jenis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" id="jenis" name="jenis" value="<?php echo $pasien['jenis']; ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="ras" class="form-label">Ras</label>
                        <input type="text" class="form-control form-control-custom" id="ras" name="ras" value="<?php echo $pasien['ras'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="umur" class="form-label">Umur (tahun)</label>
                        <input type="number" class="form-control form-control-custom" id="umur" name="umur" value="<?php echo $pasien['umur'] ?? ''; ?>" min="0" max="30">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom" id="gender" name="gender" required>
                            <option value="">Pilih Gender</option>
                            <option value="Jantan" <?php echo $pasien['gender'] == 'Jantan' ? 'selected' : ''; ?>>Jantan</option>
                            <option value="Betina" <?php echo $pasien['gender'] == 'Betina' ? 'selected' : ''; ?>>Betina</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" class="form-control form-control-custom" id="berat_badan" name="berat_badan" value="<?php echo $pasien['berat_badan'] ?? ''; ?>" min="0">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>



