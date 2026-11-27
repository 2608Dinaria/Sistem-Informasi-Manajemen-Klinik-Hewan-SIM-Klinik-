<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

$error = '';
$success = '';

// Ambil data pemilik
$stmt = $conn->prepare("SELECT * FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pemilik_id = $pemilik['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pasien_id = (int)$_POST['pasien_id'];
    $dokter_id = (int)$_POST['dokter_id'];
    $jadwal_dokter_id = (int)$_POST['jadwal_dokter_id'];
    $tanggal = sanitize($_POST['tanggal']);
    $jam = sanitize($_POST['jam']);
    $keluhan = sanitize($_POST['keluhan']);
    
    if (empty($pasien_id) || empty($dokter_id) || empty($jadwal_dokter_id) || empty($tanggal) || empty($jam)) {
        $error = 'Semua field wajib diisi!';
    } else {

        // Cek apakah slot tersedia (FCFS)
        $stmt = $conn->prepare("SELECT max_slot, slot_terisi FROM jadwal_dokter WHERE id = ?");
        $stmt->bind_param("i", $jadwal_dokter_id);
        $stmt->execute();
        $jadwal_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$jadwal_info) {
            $error = 'Jadwal dokter tidak ditemukan!';
        } elseif ($jadwal_info['slot_terisi'] >= $jadwal_info['max_slot']) {
            $error = 'Mohon maaf, kuota jadwal dokter sudah penuh.';
        } else {

            // Cek apakah pasien milik pemilik ini
            $stmt = $conn->prepare("SELECT id FROM pasien WHERE id = ? AND id_pemilik = ?");
            $stmt->bind_param("ii", $pasien_id, $pemilik_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows == 0) {
                $error = 'Data hewan tidak valid!';
                $stmt->close();
            } else {
                $stmt->close();

                // Mulai transaksi
                $conn->begin_transaction();

                try {
                    // Update slot terisi
                    $stmt_update = $conn->prepare("UPDATE jadwal_dokter 
                                                   SET slot_terisi = slot_terisi + 1 
                                                   WHERE id = ?");
                    $stmt_update->bind_param("i", $jadwal_dokter_id);
                    $stmt_update->execute();

                    // INSERT dengan created_at & updated_at
                    $status = 'pending';
                    $stmt = $conn->prepare("
                        INSERT INTO appointment 
                        (pasien_id, dokter_id, jadwal_dokter_id, tanggal, jam, status, keluhan, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");

                    if (!$stmt) {
                        throw new Exception("Error prepare: " . $conn->error);
                    }

                    $stmt->bind_param(
                        "iiissss",
                        $pasien_id,
                        $dokter_id,
                        $jadwal_dokter_id,
                        $tanggal,
                        $jam,
                        $status,
                        $keluhan
                    );

                    if (!$stmt->execute()) {
                        throw new Exception("Error execute: " . $stmt->error);
                    }

                    $conn->commit();
                    $success = 'Appointment berhasil dibuat!';
                    header('refresh:2;url=' . APP_URL . '/pemilik/appointment.php');

                } catch (Exception $e) {
                    $conn->rollback();
                    $error = 'Gagal membuat appointment! Error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Ambil daftar hewan
$hewan_list = $conn->prepare("SELECT * FROM pasien WHERE id_pemilik = ? ORDER BY nama_hewan");
$hewan_list->bind_param("i", $pemilik_id);
$hewan_list->execute();
$hewan_result = $hewan_list->get_result();

// Ambil daftar dokter
$dokter_list = $conn->query("SELECT * FROM dokter ORDER BY nama");

$page_title = 'Buat Appointment';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-plus me-2"></i>Buat Appointment</h2>
            <a href="<?php echo APP_URL; ?>/pemilik/appointment.php" class="btn btn-secondary btn-custom">
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

        <div class="form-container">
            <form method="POST" action="" data-validate="true" id="appointmentForm">

                <div class="mb-3">
                    <label class="form-label">Pilih Hewan *</label>
                    <select class="form-select form-select-custom" name="pasien_id" required>
                        <option value="">Pilih Hewan</option>
                        <?php while ($h = $hewan_result->fetch_assoc()): ?>
                            <option value="<?php echo $h['id']; ?>">
                                <?php echo $h['nama_hewan']; ?> (<?php echo $h['jenis']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Dokter *</label>
                    <select class="form-select form-select-custom" id="dokter_id" name="dokter_id" required>
                        <option value="">Pilih Dokter</option>
                        <?php while ($d = $dokter_list->fetch_assoc()): ?>
                            <option value="<?php echo $d['id']; ?>">
                                <?php echo $d['nama']; ?> - <?php echo $d['spesialis']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Jadwal *</label>
                    <select class="form-select form-select-custom" id="jadwal_dokter_id" name="jadwal_dokter_id" required>
                        <option value="">Pilih Dokter terlebih dahulu</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal *</label>
                        <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" readonly required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam *</label>
                        <input type="time" class="form-control form-control-custom" id="jam" name="jam" readonly required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keluhan</label>
                    <textarea class="form-control form-control-custom" name="keluhan" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Buat Appointment
                </button>

            </form>
        </div>

    </div>
</div>

<script>
document.getElementById('dokter_id').addEventListener('change', function() {
    const dokterId = this.value;
    const jadwalSelect = document.getElementById('jadwal_dokter_id');
    const tanggalInput = document.getElementById('tanggal');
    const jamInput = document.getElementById('jam');

    jadwalSelect.innerHTML = '<option value="">Loading...</option>';
    tanggalInput.value = '';
    jamInput.value = '';

    if (dokterId) {
        fetch('<?php echo APP_URL; ?>/api/get_jadwal.php?dokter_id=' + dokterId)
            .then(response => response.json())
            .then(data => {
                jadwalSelect.innerHTML = '<option value="">Pilih Jadwal</option>';

                data.forEach(jadwal => {
                    const tersedia = jadwal.max_slot - (jadwal.terisi || 0);
                    const option = document.createElement('option');

                    option.value = jadwal.id;
                    option.textContent = `${jadwal.tanggal} ${jadwal.jam_mulai} - ${jadwal.jam_selesai} (Tersedia: ${tersedia}/${jadwal.max_slot})`;

                    option.dataset.tanggal = jadwal.tanggal;
                    option.dataset.jam = jadwal.jam_mulai;

                    jadwalSelect.appendChild(option);
                });
            })
            .catch(() => {
                jadwalSelect.innerHTML = '<option value="">Gagal memuat jadwal</option>';
            });
    }
});

document.getElementById('jadwal_dokter_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('tanggal').value = selected.dataset.tanggal;
        document.getElementById('jam').value = selected.dataset.jam;
    }
});
</script>

<?php include '../layouts/footer.php'; ?>
