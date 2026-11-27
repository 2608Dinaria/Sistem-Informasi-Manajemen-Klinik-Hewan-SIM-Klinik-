<?php
require_once 'config.php';
require_once 'koneksi.php';

date_default_timezone_set('Asia/Jakarta'); // 🔹 Tambahkan baris ini
$message = '';

if (isset($_POST['reset'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Cek apakah email terdaftar
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($query) > 0) {
        // Buat token reset password unik
        $token = bin2hex(random_bytes(32));
        $expired = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Simpan token di tabel baru (jika belum ada, buat tabel reset_password_tokens)
        mysqli_query($conn, "
            CREATE TABLE IF NOT EXISTS reset_password_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(100) NOT NULL,
                token VARCHAR(255) NOT NULL,
                expired_at DATETIME NOT NULL
            )
        ");
        mysqli_query($conn, "DELETE FROM reset_password_tokens WHERE email='$email'");
        mysqli_query($conn, "
            INSERT INTO reset_password_tokens (email, token, expired_at)
            VALUES ('$email', '$token', '$expired')
        ");

        // Kirim link reset (sementara ditampilkan di layar untuk testing)
        $reset_link = "http://localhost/projectuas/reset_password.php?token=$token";

        $message = "
            <div class='alert alert-success text-center'>
                Link reset password telah dikirim ke email Anda.<br>
                (Untuk percobaan lokal: <a href='$reset_link'>Klik di sini</a>)
            </div>
        ";
    } else {
        $message = "<div class='alert alert-danger text-center'>Email tidak ditemukan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password - Klinik Dinas Peternakan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background-color: #ffffff;
    font-family: "Poppins", sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.forgot-card {
    width: 600px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    border-top: 5px solid #43A047;
    overflow: hidden;
    padding: 40px 50px;
}
.forgot-header {
    text-align: center;
    margin-bottom: 25px;
}
.forgot-header h3 {
    color: #2E7D32;
    font-weight: 700;
    font-size: 1.7rem;
}
.forgot-header p {
    color: #666;
    font-size: 0.95rem;
    margin-top: 5px;
}
label {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}
.form-control {
    border-radius: 10px;
    padding: 10px 12px;
}
.form-control:focus {
    border-color: #43A047;
    box-shadow: 0 0 0 0.15rem rgba(67,160,71,0.25);
}
.btn-success {
    width: 100%;
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 0;
}
.btn-success:hover {
    background-color: #388E3C;
}
.footer-text {
    text-align: center;
    margin-top: 18px;
    font-size: 0.9rem;
    color: #666;
}
.footer-text a {
    color: #43A047;
    text-decoration: none;
    font-weight: 600;
}
.footer-text a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="forgot-card">
    <div class="forgot-header">
        <h3>🐾 Klinik Dinas Peternakan</h3>
        <p>Lupa kata sandi Anda? Masukkan email untuk reset password.</p>
    </div>

    <?= $message ?>

    <form method="POST" autocomplete="off">
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email terdaftar Anda" required>
        </div>

        <button type="submit" name="reset" class="btn btn-success mt-2">Kirim Link Reset Password</button>

        <div class="footer-text">
            <a href="login.php">⬅ Kembali ke Halaman Login</a>
        </div>
    </form>
</div>

</body>
</html>
