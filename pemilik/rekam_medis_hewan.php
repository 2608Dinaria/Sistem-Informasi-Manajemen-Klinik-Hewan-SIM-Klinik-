<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

if (!isset($_GET['id'])) {
    header("Location: data_hewan_saya.php");
    exit;
}

$id_hewan = (int)$_GET['id'];
$id_pemilik = $_SESSION['user_id'];

// Ambil data pemilik untuk validasi kepemilikan
$stmt = $conn->prepare("SELECT id FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $id_pemilik);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$pemilik_id = $pemilik['id'];
$stmt->close();

// Ambil data hewan dan validasi milik pemilik
$stmt = $conn->prepare("SELECT * FROM pasien WHERE id = ? AND id_pemilik = ?");
$stmt->bind_param("ii", $id_hewan, $pemilik_id);
$stmt->execute();
$result_hewan = $stmt->get_result();

if ($result_hewan->num_rows === 0) {
    echo "<script>alert('Data hewan tidak ditemukan atau bukan milik Anda!'); window.location='data_hewan_saya.php';</script>";
    exit;
}

$hewan = $result_hewan->fetch_assoc();
$stmt->close();

// Ambil rekam medis
$query_rm = "SELECT rm.*, d.nama AS nama_dokter 
             FROM rekam_medis rm 
             JOIN dokter d ON rm.dokter_id = d.id 
             WHERE rm.pasien_id = ? 
             ORDER BY rm.tanggal DESC";
$stmt = $conn->prepare($query_rm);
$stmt->bind_param("i", $id_hewan);
$stmt->execute();
$rekam_medis = $stmt->get_result();

$page_title = 'Rekam Medis Hewan';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php';  ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-notes-medical me-2"></i>Rekam Medis Hewan Saya</h2>
            <div class="user-info">
                <a href="data_hewan_saya.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card-custom">
                    <div class="card-body-custom">
                        <h5 class="card-title mb-3"><i class="fas fa-paw me-2"></i>Info Hewan</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Nama Hewan:</strong><br>
                                <?php echo $hewan['nama_hewan']; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Jenis:</strong><br>
                                <?php echo $hewan['jenis']; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Umur:</strong><br>
                                <?php echo $hewan['umur'] ? $hewan['umur'] . ' tahun' : '-'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php if ($rekam_medis->num_rows > 0): ?>
                    <?php while ($rm = $rekam_medis->fetch_assoc()): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-calendar-alt me-2"></i><?php echo date('d F Y', strtotime($rm['tanggal'])); ?></span>
                                <span><i class="fas fa-user-md me-2"></i>drh. <?php echo $rm['nama_dokter']; ?></span>
                            </div>

                            <div class="card-body content-box"><!-- tambahkan class ini -->
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary">Pemeriksaan</h6>
                                    <div class="border-bottom pb-2 content-box">
                                        <?php echo $rm['pemeriksaan']; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary">Diagnosis</h6>
                                    <div class="border-bottom pb-2 content-box">
                                        <?php echo $rm['diagnosis']; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary">Treatment</h6>
                                    <div class="border-bottom pb-2 content-box">
                                        <?php echo $rm['treatment']; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary">Obat</h6>
                                    <div class="border-bottom pb-2 content-box">
                                        <?php echo $rm['obat']; ?>
                                    </div>
                                </div>

                                <?php if (!empty($rm['catatan'])): ?>
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary">Catatan</h6>
                                    <div class="content-box">
                                        <?php echo $rm['catatan']; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>Belum ada rekam medis untuk hewan ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php
$stmt->close();
?>
