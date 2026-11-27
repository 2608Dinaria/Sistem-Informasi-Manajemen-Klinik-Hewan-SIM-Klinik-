<?php
require_once __DIR__ . '/config.php';

if (!isset($conn)) {
    die("Koneksi database tidak tersedia. Periksa config.php");
}

$message = '';

if (isset($_POST['register'])) {

    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger text-center'>Konfirmasi password tidak cocok!</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = "pemilik";

        // Cek email atau username sudah digunakan
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-danger text-center'>Username atau Email sudah digunakan!</div>";
        } else {

            // Insert ke tabel users
            $stmt = $conn->prepare("INSERT INTO users (nama, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nama, $email, $username, $hashed_password, $role);
            
            if ($stmt->execute()) {

                // Ambil ID user yang baru dibuat
                $user_id = $stmt->insert_id;

                // Tambahkan ke tabel pemilik
                $stmt2 = $conn->prepare("INSERT INTO pemilik (id_user, nama, alamat, telepon) VALUES (?, ?, '', '')");
                $stmt2->bind_param("is", $user_id, $nama);
                $stmt2->execute();
                $stmt2->close();

                echo "<script>
                        alert('Registrasi berhasil! Silakan login untuk melanjutkan.');
                        window.location.href = 'login.php';
                      </script>";
                exit;
            } else {
                $message = "<div class='alert alert-danger text-center'>Terjadi kesalahan saat registrasi. Coba lagi!</div>";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun - Klinik Dinas Peternakan</title>
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

/* CARD */
.register-card {
    width: 600px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    border-top: 5px solid #0d6efd;
    overflow: hidden;
    padding: 40px 50px;
}

/* HEADER */
.register-header {
    text-align: center;
    margin-bottom: 25px;
}

.register-header h3 {
    color: #0d6efd;
    font-weight: 700;
    font-size: 1.7rem;
}

.register-header p {
    color: #666;
    font-size: 0.95rem;
    margin-top: 5px;
}

/* FORM */
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
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.25);
}

.input-group-text {
    background: none;
    border-left: 0;
    cursor: pointer;
}

/* BUTTON */
.btn-primary {
    width: 100%;
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 0;
}

.btn-primary:hover {
    background-color: #0b5ed7;
}

/* FOOTER */
.footer-text {
    text-align: center;
    margin-top: 18px;
    font-size: 0.9rem;
    color: #666;
}

.footer-text a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
}

.footer-text a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="register-card">

    <div class="register-header">
        <h3>🐾 Sistem Informasi Manajemen Klinik Hewan</h3>
        <p>Daftarkan akun Anda untuk memulai layanan klinik hewan.</p>
    </div>

    <?= $message ?>

    <form method="POST" autocomplete="off">

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama lengkap Anda" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="Username untuk login" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi" required>
                <span class="input-group-text" onclick="togglePassword('password','eyePass')">
                    <i id="eyePass" class="bi bi-eye"></i>
                </span>
            </div>
        </div>

        <div class="mb-4">
            <label for="confirm_password" class="form-label">Ulangi Kata Sandi</label>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi kata sandi" required>
                <span class="input-group-text" onclick="togglePassword('confirm_password','eyeConfirm')">
                    <i id="eyeConfirm" class="bi bi-eye"></i>
                </span>
            </div>
        </div>

        <button name="register" class="btn btn-primary">
            Daftar Sekarang
        </button>

        <div class="footer-text">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>

    </form>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

</body>
</html>
