<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

// Ambil daftar dokter
$dokter_list = $conn->query("SELECT id, nama, spesialis FROM dokter ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = sanitize($_POST['tanggal']);
    $waktu = sanitize($_POST['waktu']);
    $jenis_vaksin = sanitize($_POST['jenis_vaksin']);
    $kuota = (int)$_POST['kuota'];
    $dokter_id = !empty($_POST['dokter_id']) ? (int)$_POST['dokter_id'] : NULL;
    $catatan = $_POST['catatan'];

    
    if (empty($tanggal) || empty($waktu) || empty($jenis_vaksin) || empty($kuota)) {
        $error = 'Field wajib diisi (Tanggal, Waktu, Jenis Vaksin, Kuota)!';
    } else {
        $stmt = $conn->prepare("INSERT INTO jadwal_vaksinasi (tanggal, waktu, jenis_vaksin, kuota, dokter_id, catatan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiis", $tanggal, $waktu, $jenis_vaksin, $kuota, $dokter_id, $catatan);
        
        if ($stmt->execute()) {
            $success = 'Jadwal vaksinasi berhasil ditambahkan!';
            header('refresh:2;url=' . APP_URL . '/admin/jadwal_vaksinasi.php');
        } else {
            $error = 'Gagal menambahkan jadwal!';
        }
        $stmt->close();
    }
}

$page_title = 'Tambah Jadwal Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-plus-circle me-2"></i>Tambah Jadwal Vaksinasi</h2>
            <a href="<?php echo APP_URL; ?>/admin/jadwal_vaksinasi.php" class="btn btn-secondary btn-custom">
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="waktu" class="form-label">Waktu <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="waktu" name="waktu" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="jenis_vaksin" class="form-label">Jenis Vaksin <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" id="jenis_vaksin" name="jenis_vaksin" placeholder="Contoh: Rabies, Distemper" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kuota" class="form-label">Kuota <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-custom" id="kuota" name="kuota" required min="1">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="dokter_id" class="form-label">Dokter Penanggung Jawab (Optional)</label>
                        <select class="form-select form-select-custom" id="dokter_id" name="dokter_id">
                            <option value="">-- Pilih Dokter --</option>
                            <?php while ($d = $dokter_list->fetch_assoc()): ?>
                                <option value="<?php echo $d['id']; ?>">
                                    <?php echo $d['nama']; ?> - <?php echo $d['spesialis']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Tambahan</label>
                    <textarea id="catatan" name="catatan"></textarea>
                </div>
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan Jadwal
                </button>
            </form>
        </div>
    </div>
</div>

<!-- jQuery WAJIB -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Summernote -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#catatan').summernote({
            placeholder: 'Informasi tambahan untuk pemilik hewan...',
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

