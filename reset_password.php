<?php
require_once 'config.php';
require_once 'koneksi.php';

$message = "";

// ✅ Ambil token dari URL
if (!isset($_GET['token'])) {
    die("<div style='text-align:center;margin-top:40px;color:red;'>Token tidak ditemukan.</div>");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// ✅ Cek apakah token valid dan belum kedaluwarsa
$check = mysqli_query($conn, "
    SELECT * FROM reset_password_tokens 
    WHERE token='$token' AND expired_at > NOW()
");

if (mysqli_num_rows($check) === 0) {
    die("<div style='text-align:center;margin-top:40px;color:red;'>Token tidak valid atau sudah kedaluwarsa.</div>");
}

$data = mysqli_fetch_assoc($check);
$email = $data['email'];

// ✅ Proses form ubah password
if (isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "<div class='alert alert-danger text-center'>Konfirmasi password tidak cocok!</div>";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Update password user berdasarkan email
        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE email='$email'");

        // Hapus token supaya tidak bisa dipakai ulang
        mysqli_query($conn, "DELETE FROM reset_password_tokens WHERE token='$token'");

        echo "<script>
                alert('Password berhasil diubah! Silakan login kembali.');
                window.location.href = 'login.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Atur Ulang Password - Klinik Dinas Peternakan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background-color: #fff;
    font-family: "Inter", sans-serif;
}
.reset-box {
    max-width: 420px;
    margin: 60px auto;
    background: #fff;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.08);
}
h3 {
    font-weight: 700;
    text-align: left;
    margin-bottom: 10px;
}
.subtext {
    color: #6c757d;
    font-size: 0.95rem;
    margin-bottom: 25px;
}
.form-control {
    border-radius: 8px;
}
.btn-primary {
    width: 100%;
    border-radius: 8px;
    font-weight: 600;
    padding: 10px 0;
}
</style>
</head>
<body>

<div class="reset-box">
    <h3>🔒 Atur Ulang Password</h3>
    <p class="subtext">Masukkan password baru untuk akun Anda.</p>

    <?= $message ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password baru" required>
                <span class="input-group-text" onclick="togglePassword('password','eyePass')">
                    <i id="eyePass" class="bi bi-eye"></i>
                </span>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
                <span class="input-group-text" onclick="togglePassword('confirm_password','eyeConfirm')">
                    <i id="eyeConfirm" class="bi bi-eye"></i>
                </span>
            </div>
        </div>

        <button name="reset" class="btn btn-primary">Ubah Password</button>
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
