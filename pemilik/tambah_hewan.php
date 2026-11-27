<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

$error = '';
$success = '';

// Ambil data pemilik
$stmt = $conn->prepare("SELECT * FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pemilik_id = $pemilik['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_hewan = sanitize($_POST['nama_hewan']);
    $jenis = sanitize($_POST['jenis']);
    $ras = sanitize($_POST['ras']);
    $umur = !empty($_POST['umur']) ? (int)$_POST['umur'] : null;
    $gender = sanitize($_POST['gender']);
    $berat_badan = !empty($_POST['berat_badan']) ? (float)$_POST['berat_badan'] : null;
    
    if (empty($nama_hewan) || empty($jenis) || empty($gender)) {
        $error = 'Nama hewan, jenis, dan gender wajib diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO pasien (id_pemilik, nama_hewan, jenis, ras, umur, gender, berat_badan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssisd", $pemilik_id, $nama_hewan, $jenis, $ras, $umur, $gender, $berat_badan);
        
        if ($stmt->execute()) {
            $success = 'Hewan berhasil ditambahkan!';
            header('refresh:2;url=' . APP_URL . '/pemilik/data_hewan_saya.php');
        } else {
            $error = 'Gagal menambahkan hewan!';
        }
        $stmt->close();
    }
}

$page_title = 'Tambah Hewan';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-paw me-2"></i>Tambah Hewan</h2>
            <a href="<?php echo APP_URL; ?>/pemilik/data_hewan_saya.php" class="btn btn-secondary btn-custom">
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
                    <label for="nama_hewan" class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" id="nama_hewan" name="nama_hewan" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis" class="form-label">Jenis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" id="jenis" name="jenis" required placeholder="Contoh: Kucing, Anjing">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="ras" class="form-label">Ras</label>
                        <input type="text" class="form-control form-control-custom" id="ras" name="ras" placeholder="Contoh: Persian, Golden Retriever">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="umur" class="form-label">Umur (tahun)</label>
                        <input type="number" class="form-control form-control-custom" id="umur" name="umur" min="0" max="30">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom" id="gender" name="gender" required>
                            <option value="">Pilih Gender</option>
                            <option value="Jantan">Jantan</option>
                            <option value="Betina">Betina</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" class="form-control form-control-custom" id="berat_badan" name="berat_badan" min="0">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>


