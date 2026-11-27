<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$page_title = 'Laporan';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-file-alt me-2"></i>Laporan</h2>
        </div>

        <!-- Row untuk Card Laporan -->
        <div class="row mt-4">

            <!-- Laporan Rekam Medis -->
            <div class="col-md-4 mb-3">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-file-medical fa-3x text-primary mb-3"></i>
                        <h5>Laporan Rekam Medis</h5>
                        <a href="<?php echo APP_URL; ?>/admin/laporan_rekam_medis.php" 
                           class="btn btn-primary-custom-solid btn-custom mt-3">
                            <i class="fas fa-eye me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Laporan Vaksinasi -->
            <div class="col-md-4 mb-3">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-syringe fa-3x text-primary mb-3"></i>
                        <h5>Laporan Vaksinasi</h5>
                        <a href="<?php echo APP_URL; ?>/admin/laporan_vaksinasi.php" 
                           class="btn btn-primary-custom-solid btn-custom mt-3">
                            <i class="fas fa-eye me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Laporan Appointment -->
            <div class="col-md-4 mb-3">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5>Laporan Appointment</h5>
                        <a href="<?php echo APP_URL; ?>/admin/laporan_appointment.php" 
                           class="btn btn-primary-custom-solid btn-custom mt-3">
                            <i class="fas fa-eye me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

        </div> <!-- End Row -->

    </div>
</div>
