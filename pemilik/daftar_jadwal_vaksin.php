<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

$page_title = 'Jadwal Vaksinasi';
include '../layouts/header_dashboard.php';

// Ambil jadwal yang belum lewat dan kuota masih ada (optional: show all future schedules even if full)
$today = date('Y-m-d');
$query = "SELECT jv.*, d.nama as nama_dokter 
          FROM jadwal_vaksinasi jv 
          LEFT JOIN dokter d ON jv.dokter_id = d.id 
          WHERE jv.tanggal >= '$today' 
          ORDER BY jv.tanggal ASC, jv.waktu ASC";
$result = $conn->query($query);
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-syringe me-2"></i>Jadwal Vaksinasi Tersedia</h2>
        </div>
        
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        $sisa_kuota = $row['kuota'] - $row['kuota_terpakai'];
                        $is_full = $sisa_kuota <= 0;
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="card-title text-primary fw-bold mb-3">
                                    <i class="fas fa-calendar-day me-2"></i><?php echo formatTanggal($row['tanggal']); ?>
                                </h5>
                                <div class="mb-2">
                                    <i class="far fa-clock me-2 text-muted"></i> <?php echo formatWaktu($row['waktu']); ?>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-vial me-2 text-muted"></i> <strong><?php echo htmlspecialchars($row['jenis_vaksin']); ?></strong>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-user-md me-2 text-muted"></i> <?php echo $row['nama_dokter'] ? htmlspecialchars($row['nama_dokter']) : 'Dokter Jaga'; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge <?php echo $is_full ? 'bg-danger' : 'bg-success'; ?> rounded-pill">
                                        <?php echo $is_full ? 'Kuota Penuh' : 'Sisa Kuota: ' . $sisa_kuota; ?>
                                    </span>
                                </div>
                                
                                <?php if (!empty(trim($row['catatan']))): ?>
                                    <div class="catatan-summernote mb-3">
                                        <?php echo $row['catatan']; ?>
                                    </div>
                                <?php endif; ?>

                                
                                <div class="d-grid">
                                    <?php if ($is_full): ?>
                                        <button class="btn btn-secondary disabled">Penuh</button>
                                    <?php else: ?>
                                        <a href="form_daftar_vaksin.php?jadwal_id=<?php echo $row['id']; ?>" class="btn btn-primary-custom-solid">
                                            Daftar Sekarang
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>Belum ada jadwal vaksinasi yang tersedia saat ini.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
