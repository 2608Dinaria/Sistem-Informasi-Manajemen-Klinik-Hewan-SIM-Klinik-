<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>
        Klinik Dinas Peternakan
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS (PATH AMAN TANPA APP_URL) -->
    <link rel="stylesheet" href="/lintasdisiplinilmu/assets/css/style.css">

    <?php if (isset($additional_css)) echo $additional_css; ?>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
  <div class="container">

    <!-- Logo / Brand -->
    <a class="navbar-brand fw-bold text-primary" href="/lintasdisiplinilmu/index.php">
        <i class="bi bi-hospital me-1"></i> Klinik Dinas Peternakan
    </a>

    <!-- Hamburger menu -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">

        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'index.php' ? 'text-primary fw-bold active' : '' ?>" href="/lintasdisiplinilmu/index.php">
            Beranda
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'vaksinasi.php' ? 'text-primary fw-bold active' : '' ?>" href="/lintasdisiplinilmu/vaksinasi.php">
            Vaksinasi
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'tentang.php' ? 'text-primary fw-bold active' : '' ?>" href="/lintasdisiplinilmu/tentang.php">
            Tentang Kami
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'kontak.php' ? 'text-primary fw-bold active' : '' ?>" href="/lintasdisiplinilmu/kontak.php">
            Kontak
          </a>
        </li>

        <!-- Dropdown Login -->
        <li class="nav-item dropdown ms-3">
          <a class="nav-link dropdown-toggle d-flex align-items-center text-primary fw-semibold" 
            href="#" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5 me-1"></i>
            <span>Masuk</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li>
              <a class="dropdown-item" href="login.php">
                <i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Login
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="register_pemilik.php">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Daftar Akun
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
