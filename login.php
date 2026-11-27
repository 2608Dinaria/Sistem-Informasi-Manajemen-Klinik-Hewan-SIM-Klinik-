<?php
session_start();
include 'config.php';

// Jika sudah login, arahkan langsung ke dashboard sesuai role
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'dokter') {
        header("Location: dokter/dashboard.php");
        exit;
    } else {
        header("Location: pemilik/dashboard.php");
        exit;
    }
}

$message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Cek apakah email & username cocok
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND username='$username'");

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        if (password_verify($password, $data['password'])) {
            session_regenerate_id(true);

            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['role'] = $data['role'];

            // Redirect sesuai role
            if ($data['role'] == 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } elseif ($data['role'] == 'dokter') {
                header("Location: dokter/dashboard.php");
                exit;
            } else {
                header("Location: pemilik/dashboard.php");
                exit;
            }

        } else {
            $message = "<div class='alert alert-danger text-center'>Password salah!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Email dan Username tidak cocok!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Klinik Dinas Peternakan</title>
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
.login-card {
    width: 600px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    border-top: 5px solid #0d6efd;
    overflow: hidden;
    padding: 40px 50px;
}

/* HEADER */
.login-header {
    text-align: center;
    margin-bottom: 25px;
}

.login-header h3 {
    color: #0d6efd;
    font-weight: 700;
    font-size: 1.7rem;
}

.login-header p {
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

.forgot-link {
    text-align: right;
    font-size: 0.9rem;
    margin-top: -5px;
}

.forgot-link a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
}

.forgot-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="login-card">

    <div class="login-header">
        <h3>🐾 Sistem Informasi Manajemen Klinik Hewan</h3>
        <p>Dinas Peternakan</p>
    </div>

    <?= $message ?>

    <form method="POST" autocomplete="off">

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email Anda" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username Anda" required>
        </div>

        <div class="mb-2">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
                <span class="input-group-text" onclick="togglePassword('password','eyePass')">
                    <i id="eyePass" class="bi bi-eye"></i>
                </span>
            </div>
        </div>

        <div class="forgot-link">
            <a href="lupa_password.php">Lupa Password?</a>
        </div>

        <button type="submit" name="login" class="btn btn-primary mt-3">
            Login
        </button>

        <div class="footer-text">
            <p>
                Belum punya akun?
                <a href="register_pemilik.php">Daftar di sini</a>
            </p>

            <p class="mt-2">
                <a href="<?php echo APP_URL; ?>/index.php" class="text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda
                </a>
            </p>
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
