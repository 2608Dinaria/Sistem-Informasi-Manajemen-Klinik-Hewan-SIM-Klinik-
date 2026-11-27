<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

$error = '';
$success = '';

// Cek ID hewan yang akan diedit
if (!isset($_GET['id'])) {
    header("Location: " . APP_URL . "/pemilik/data_hewan_saya.php");
    exit;
}

$hewan_id = (int)$_GET['id'];

// Ambil data pemilik
$stmt = $conn->prepare("SELECT * FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pemilik_id = $pemilik['id'];

// Ambil data hewan untuk diedit
$stmt = $conn->prepare("SELECT * FROM pasien WHERE id = ? AND id_pemilik = ?");
$stmt->bind_param("ii", $hewan_id, $pemilik_id);
$stmt->execute();
$data_hewan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data_hewan) {
    $error = "Data hewan tidak ditemukan!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_hewan = sanitize($_POST['nama_hewan']);
    $jenis = sanitize($_POST['jenis']);
    $ras = sanitize($_POST['ras']);
    $umur = !empty($_POST['umur']) ? (int)$_POST['umur'] : null;
    $gender = sanitize($_POST['gender']);
    $berat_badan = !empty($_POST['berat_badan']) ? (float)$_POST['berat_badan'] : null;

    if (empty($nama_hewan) || empty($jenis) || empty($gender)) {
        $error = 'Nama hewan, jenis, dan gender wajib diisi!';
    } else {
        $stmt = $conn->prepare("
            UPDATE pasien 
            SET nama_hewan = ?, jenis = ?, ras = ?, umur = ?, gender = ?, berat_badan = ?
            WHERE id = ? AND id_pemilik = ?
        ");
        $stmt->bind_param("sssisdii", 
            $nama_hewan, $jenis, $ras, $umur, $gender, $berat_badan,
            $hewan_id, $pemilik_id
        );

        if ($stmt->execute()) {
            $success = 'Data hewan berhasil diperbarui!';
            header('refresh:2;url=' . APP_URL . '/pemilik/data_hewan_saya.php');
        } else {
            $error = 'Gagal memperbarui data hewan!';
        }
        $stmt->close();
    }
}

$page_title = 'Edit Hewan';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-paw me-2"></i>Edit Hewan</h2>
            <a href="<?php echo APP_URL; ?>/pemilik/data_hewan_saya.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($data_hewan): ?>
        <div class="form-container">
            <form method="POST" action="" data-validate="true">
                <div class="mb-3">
                    <label for="nama_hewan" class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" 
                           id="nama_hewan" 
                           name="nama_hewan" 
                           required
                           value="<?php echo $data_hewan['nama_hewan']; ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis" class="form-label">Jenis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" 
                               id="jenis" 
                               name="jenis" 
                               required
                               value="<?php echo $data_hewan['jenis']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ras" class="form-label">Ras</label>
                        <input type="text" class="form-control form-control-custom" 
                               id="ras" 
                               name="ras" 
                               value="<?php echo $data_hewan['ras']; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="umur" class="form-label">Umur (tahun)</label>
                        <input type="number" class="form-control form-control-custom" 
                               id="umur" name="umur" min="0" max="30"
                               value="<?php echo $data_hewan['umur']; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom" id="gender" name="gender" required>
                            <option value="">Pilih Gender</option>
                            <option value="Jantan" <?php if ($data_hewan['gender'] == 'Jantan') echo 'selected'; ?>>Jantan</option>
                            <option value="Betina" <?php if ($data_hewan['gender'] == 'Betina') echo 'selected'; ?>>Betina</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" class="form-control form-control-custom" 
                               id="berat_badan" 
                               name="berat_badan" 
                               min="0"
                               value="<?php echo $data_hewan['berat_badan']; ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Update
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>
