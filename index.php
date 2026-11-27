<?php
require_once 'config.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isLoggedIn()) {
    redirectByRole();
}
?>
<?php include 'layouts/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <div class="text-center text-lg-start">
                    <img src="<?php echo APP_URL; ?>/assets/img/logo_dinas.png" alt="Logo Dinas Peternakan" class="logo-dinas" onerror="this.style.display='none'">
                    <h1 class="hero-title">Sistem Informasi Manajemen Klinik Hewan</h1>
                    <p class="hero-subtitle"><?php echo APP_INSTITUTION; ?></p>
                    <p class="lead mb-4">Layanan resmi kesehatan hewan terpercaya untuk masyarakat</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="<?php echo APP_URL; ?>/login.php" class="btn btn-hero btn-primary-custom">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="<?php echo APP_URL; ?>/register_pemilik.php" class="btn btn-hero btn-outline-custom">
                            <i class="fas fa-user-plus me-2"></i>Daftar Pemilik Hewan
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img src="<?php echo APP_URL; ?>/assets/img/home2.webp" 
                    alt="Gambar Klinik Hewan" 
                    class="img-fluid rounded shadow-sm">
            </div>

        </div>
    </div>
</section>

<!-- Tentang Dinas Section -->
<section class="section-padding bg-white">
    <div class="container">
        <h2 class="section-title">Tentang Dinas Peternakan</h2>
        <p class="section-subtitle">Pelayanan kesehatan hewan profesional dan terpercaya</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card-custom text-center">
                    <div class="card-body-custom">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h4>Terpercaya</h4>
                        <p>Layanan resmi dari instansi pemerintah yang terpercaya dan profesional</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card-custom text-center">
                    <div class="card-body-custom">
                        <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                        <h4>Dokter Berpengalaman</h4>
                        <p>Dilayani oleh dokter hewan terverifikasi dan berpengalaman</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card-custom text-center">
                    <div class="card-body-custom">
                        <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                        <h4>Pelayanan Cepat</h4>
                        <p>Sistem appointment online untuk kemudahan dan efisiensi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Klinik Section -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title">Layanan Klinik</h2>
        <p class="section-subtitle">Berbagai layanan kesehatan hewan yang kami sediakan</p>
        
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-stethoscope fa-3x text-primary mb-3"></i>
                        <h5>Pemeriksaan Umum</h5>
                        <p class="text-muted">Pemeriksaan kesehatan rutin untuk hewan peliharaan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-syringe fa-3x text-primary mb-3"></i>
                        <h5>Vaksinasi</h5>
                        <p class="text-muted">Program vaksinasi lengkap dengan jadwal booster</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-file-medical fa-3x text-primary mb-3"></i>
                        <h5>Rekam Medis</h5>
                        <p class="text-muted">Pencatatan riwayat kesehatan hewan secara digital</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card-custom">
                    <div class="card-body-custom text-center">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5>Appointment Online</h5>
                        <p class="text-muted">Reservasi jadwal kunjungan secara online</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jam Operasional Section -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="section-title">Jam Operasional</h2>
                <div class="card-custom">
                    <div class="card-body-custom">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><strong>Senin - Jumat</strong></td>
                                    <td>08:00 - 16:00 WIB</td>
                                </tr>
                                <tr>
                                    <td><strong>Sabtu</strong></td>
                                    <td>08:00 - 12:00 WIB</td>
                                </tr>
                                <tr>
                                    <td><strong>Minggu & Hari Libur</strong></td>
                                    <td>Tutup</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> Untuk layanan darurat di luar jam operasional, silakan hubungi nomor darurat yang tersedia.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'layouts/footer.php'; ?>
