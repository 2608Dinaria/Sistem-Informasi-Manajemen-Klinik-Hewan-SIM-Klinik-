<?php
// Jika halaman memakai $page_title, pastikan aman
$page_title = isset($page_title) ? $page_title : "Dashboard";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $page_title ?> - Klinik Dinas Peternakan
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/lintasdisiplinilmu/assets/css/style.css">

    <?php if (isset($additional_css)) echo $additional_css; ?>
</head>

<!-- Hapus padding-top default dari layout navbar -->
<body style="padding-top:0 !important;">
