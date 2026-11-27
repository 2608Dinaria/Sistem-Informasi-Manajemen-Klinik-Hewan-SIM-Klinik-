<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

$error = '';
$success = '';

// Ambil data dokter
$stmt = $conn->prepare("SELECT * FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dokter_id = $dokter['id'];
$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pasien_id = (int)$_POST['pasien_id'];
    $tanggal = sanitize($_POST['tanggal']);
    $pemeriksaan = $_POST['pemeriksaan'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $obat = $_POST['obat'];
    $catatan = $_POST['catatan'];
    $appointment_id_post = !empty($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null;
    
    if (empty($pasien_id) || empty($tanggal) || empty($diagnosis) || empty($treatment)) {
        $error = 'Pasien, tanggal, diagnosis, dan treatment wajib diisi!';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO rekam_medis 
            (pasien_id, dokter_id, appointment_id, tanggal, pemeriksaan, diagnosis, treatment, obat, catatan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiissssss", 
            $pasien_id, $dokter_id, $appointment_id_post, $tanggal, 
            $pemeriksaan, $diagnosis, $treatment, $obat, $catatan
        );
        
        if ($stmt->execute()) {
            $success = 'Rekam medis berhasil ditambahkan!';
            header('refresh:2;url=' . APP_URL . '/dokter/rekam_medis.php');
        } else {
            $error = 'Gagal menambahkan rekam medis!';
        }
        $stmt->close();
    }
}

// Ambil daftar pasien
$pasien_list = $conn->query("SELECT * FROM pasien ORDER BY nama_hewan");

// Jika ada appointment_id, ambil data appointment
$appointment_data = null;
if ($appointment_id) {
    $stmt = $conn->prepare("SELECT * FROM appointment WHERE id = ? AND dokter_id = ?");
    $stmt->bind_param("ii", $appointment_id, $dokter_id);
    $stmt->execute();
    $appointment_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$page_title = 'Tambah Rekam Medis';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-file-medical me-2"></i>Tambah Rekam Medis</h2>
            <a href="<?php echo APP_URL; ?>/dokter/rekam_medis.php" class="btn btn-secondary btn-custom">
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
                
                <?php if ($appointment_id): ?>
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="pasien_id" class="form-label">Pasien <span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" id="pasien_id" name="pasien_id" required>
                        <option value="">Pilih Pasien</option>
                        <?php while ($p = $pasien_list->fetch_assoc()): 
                            $selected = ($appointment_data && $appointment_data['pasien_id'] == $p['id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $p['id']; ?>" <?= $selected; ?>>
                                <?= $p['nama_hewan']; ?> (<?= $p['jenis']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal"
                           value="<?= date('Y-m-d'); ?>" required>
                </div>

                <!-- Semua textarea Summernote TANPA form-control -->
                <div class="mb-3">
                    <label for="pemeriksaan" class="form-label">Pemeriksaan</label>
                    <textarea class="summernote" id="pemeriksaan" name="pemeriksaan"></textarea>
                </div>

                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis <span class="text-danger">*</span></label>
                    <textarea class="summernote" id="diagnosis" name="diagnosis" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="treatment" class="form-label">Treatment <span class="text-danger">*</span></label>
                    <textarea class="summernote" id="treatment" name="treatment" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="obat" class="form-label">Obat</label>
                    <textarea class="summernote" id="obat" name="obat"></textarea>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="summernote" id="catatan" name="catatan"></textarea>
                </div>

                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- jQuery WAJIB -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Summernote CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

<script>
$(document).ready(function() {
    $('.summernote').summernote({
        placeholder: 'Ketikkan data rekam medis...',
        height: 160,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['undo', 'redo']]
        ]
    });
});
</script>

<!-- Perbaikan tampilan editor -->
<style>
.note-editor.note-frame {
    border-radius: 6px !important;
    border: 1px solid #ced4da !important;
}
</style>
