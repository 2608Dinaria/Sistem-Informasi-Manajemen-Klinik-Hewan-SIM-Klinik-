<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

// Ambil daftar dokter
$dokter_list = $conn->query("SELECT id, nama, spesialis FROM dokter ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dokter_id = (int)$_POST['dokter_id'];
    $tanggal = sanitize($_POST['tanggal']);
    $jam_mulai = sanitize($_POST['jam_mulai']);
    $jam_selesai = sanitize($_POST['jam_selesai']);
    $max_slot = (int)$_POST['max_slot'];
    
    if (empty($dokter_id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($max_slot)) {
        $error = 'Semua field wajib diisi!';
    } elseif ($jam_mulai >= $jam_selesai) {
        $error = 'Jam mulai harus lebih awal dari jam selesai!';
    } else {
        // Cek bentrok jadwal
        $stmt = $conn->prepare("
            SELECT id FROM jadwal_dokter 
            WHERE dokter_id = ? 
            AND tanggal = ? 
            AND (
                (jam_mulai <= ? AND jam_selesai > ?) OR
                (jam_mulai < ? AND jam_selesai >= ?) OR
                (jam_mulai >= ? AND jam_selesai <= ?)
            )
        ");
        // Logic overlap: (StartA <= EndB) and (EndA >= StartB)
        // Here checking if new time overlaps with existing
        // Existing: StartE, EndE. New: StartN, EndN
        // Overlap if: StartE < EndN AND EndE > StartN
        
        // Let's simplify the query logic for overlap
        // Overlap if NOT (EndE <= StartN OR StartE >= EndN)
        // Which means: EndE > StartN AND StartE < EndN
        
        $stmt = $conn->prepare("
            SELECT id FROM jadwal_dokter 
            WHERE dokter_id = ? 
            AND tanggal = ? 
            AND jam_selesai > ? 
            AND jam_mulai < ?
        ");
        
        $stmt->bind_param("isss", $dokter_id, $tanggal, $jam_mulai, $jam_selesai);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Dokter ini sudah memiliki jadwal yang bentrok di waktu tersebut!';
        } else {
            $stmt = $conn->prepare("INSERT INTO jadwal_dokter (dokter_id, tanggal, jam_mulai, jam_selesai, max_slot) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $dokter_id, $tanggal, $jam_mulai, $jam_selesai, $max_slot);
            
            if ($stmt->execute()) {
                $success = 'Jadwal berhasil ditambahkan!';
                header('refresh:2;url=' . APP_URL . '/admin/jadwal_dokter.php');
            } else {
                $error = 'Gagal menambahkan jadwal!';
            }
        }
        $stmt->close();
    }
}

$page_title = 'Tambah Jadwal Dokter';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-plus me-2"></i>Tambah Jadwal Dokter</h2>
            <a href="<?php echo APP_URL; ?>/admin/jadwal_dokter.php" class="btn btn-secondary btn-custom">
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
                    <label for="dokter_id" class="form-label">Dokter <span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" id="dokter_id" name="dokter_id" required>
                        <option value="">Pilih Dokter</option>
                        <?php while ($d = $dokter_list->fetch_assoc()): ?>
                            <option value="<?php echo $d['id']; ?>">
                                <?php echo $d['nama']; ?> - <?php echo $d['spesialis']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="jam_mulai" name="jam_mulai" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="jam_selesai" name="jam_selesai" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="max_slot" class="form-label">Maksimum Slot (Kuota) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control form-control-custom" id="max_slot" name="max_slot" required min="1">
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan Jadwal
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
