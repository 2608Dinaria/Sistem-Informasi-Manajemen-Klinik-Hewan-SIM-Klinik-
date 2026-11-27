<?php include __DIR__ . '/layouts/header.php'; ?>

<!-- KONTAK KAMI -->
<section class="container py-5 mt-4">
  <div class="text-center mb-5">
    <h2 class="fw-bold text-primary">Hubungi Kami</h2>
    <p class="text-secondary">Kami siap membantu Anda dalam perawatan dan layanan kesehatan hewan</p>
  </div>

  <div class="row g-5">
    <!-- FORM KONTAK -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm p-4">
        <h5 class="fw-bold text-primary mb-3">Kirim Pesan</h5>
        <form>
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" placeholder="Masukkan nama Anda" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="Masukkan email aktif" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Subjek</label>
            <input type="text" class="form-control" placeholder="Tuliskan subjek pesan Anda..." required>
          </div>
          <div class="mb-3">
            <label class="form-label">Pesan</label>
            <textarea class="form-control" rows="4" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-send me-2"></i>Kirim Pesan
          </button>
        </form>
      </div>
    </div>

    <!-- INFORMASI KONTAK -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm p-4 h-100">
        <h5 class="fw-bold text-primary mb-3">Informasi Kontak</h5>

        <p class="text-secondary mb-2">
          <i class="bi bi-geo-alt-fill text-primary me-2"></i>
          <strong>Klinik Dinas Peternakan Sumatera Utara</strong><br>
          Jl. Peternakan Sejahtera No. 45, Medan, Sumatera Utara
        </p>

        <p class="text-secondary mb-2">
          <i class="bi bi-telephone-fill text-primary me-2"></i>
          (061) 765-4321
        </p>

        <p class="text-secondary mb-2">
          <i class="bi bi-envelope-fill text-primary me-2"></i>
          klinikpeternakan@dinas.go.id
        </p>

        <p class="text-secondary mb-4">
          <i class="bi bi-clock-fill text-primary me-2"></i>
          Senin - Sabtu: 08.00 - 17.00 WIB
        </p>

        <h6 class="fw-bold text-primary mb-2">Ikuti Kami</h6>
        <div class="mb-4">
          <a href="#" class="text-primary me-3"><i class="bi bi-facebook fs-4"></i></a>
          <a href="#" class="text-primary me-3"><i class="bi bi-instagram fs-4"></i></a>
          <a href="#" class="text-primary me-3"><i class="bi bi-whatsapp fs-4"></i></a>
          <a href="#" class="text-primary"><i class="bi bi-twitter-x fs-4"></i></a>
        </div>

        <h6 class="fw-bold text-primary mb-2">Lokasi Kami</h6>
        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.0598149261194!2d98.67879587378374!3d3.582612150260406!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x302e68b7c3b4f6c3%3A0xfcb24ac97b9947b1!2sMedan%2C%20Sumatera%20Utara!5e0!3m2!1sid!2sid!4v1731428745123!5m2!1sid!2sid"
            width="100%" height="250" style="border:0;" allowfullscreen loading="lazy"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/layouts/footer.php'; ?>
