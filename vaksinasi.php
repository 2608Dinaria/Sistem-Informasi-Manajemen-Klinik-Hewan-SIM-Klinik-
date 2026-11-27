<?php include __DIR__ . '/layouts/header.php'; ?>

<section class="container py-5 mt-4">
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <h2 class="fw-bold text-primary mb-3">Program Vaksinasi Hewan</h2>
      <p class="text-secondary">
        Klinik Dinas Peternakan menyediakan layanan vaksinasi rutin untuk hewan peliharaan dan hewan ternak.
        Vaksinasi membantu mencegah penyakit berbahaya dan memastikan hewan tetap sehat serta produktif.
      </p>

      <ul class="list-unstyled text-secondary small mt-3">
        <li><i class="bi bi-check-circle text-primary me-2"></i>Vaksinasi rabies, parvo, dan flu kucing</li>
        <li><i class="bi bi-check-circle text-primary me-2"></i>Jadwal vaksinasi disesuaikan dengan usia hewan</li>
        <li><i class="bi bi-check-circle text-primary me-2"></i>Pencatatan otomatis di sistem klinik</li>
      </ul>

      <a href="login.php" class="btn btn-primary mt-3">
        <i class="bi bi-calendar-check me-2"></i>Buat Jadwal Vaksinasi
      </a>
    </div>

    <div class="col-md-6 text-center">
      <img src="assets/img/vaksin.jpg" alt="Vaksinasi" class="img-fluid rounded shadow-sm">
    </div>
  </div>

  <div class="text-center mt-5">
    <h5 class="fw-bold text-primary mb-3">Jenis Vaksin yang Tersedia</h5>
    <div class="row justify-content-center g-3">

      <div class="col-md-3">
        <div class="card p-3 card-hover h-100">
          <h6 class="fw-bold text-primary">Rabies</h6>
          <p class="small text-secondary">
            Mencegah infeksi virus rabies yang berbahaya bagi hewan dan manusia.
          </p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3 card-hover h-100">
          <h6 class="fw-bold text-primary">Parvovirus</h6>
          <p class="small text-secondary">
            Melindungi anjing dari penyakit pencernaan yang sangat menular.
          </p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3 card-hover h-100">
          <h6 class="fw-bold text-primary">Flu Kucing</h6>
          <p class="small text-secondary">
            Mencegah pilek dan batuk menular pada kucing kesayangan Anda.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/layouts/footer.php'; ?>
