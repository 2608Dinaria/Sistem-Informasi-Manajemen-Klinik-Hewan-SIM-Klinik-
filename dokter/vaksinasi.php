<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

// Ambil data dokter
$stmt = $conn->prepare("SELECT * FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dokter_id = $dokter['id'];

// LOGIC INPUT VAKSINASI (Jika ada parameter ID)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Ambil data vaksinasi
    $query = "SELECT v.*, h.nama_hewan, h.jenis, h.gender, h.umur, h.ras, p.nama AS nama_pemilik, jv.jenis_vaksin as jenis_vaksin_jadwal
              FROM vaksinasi v
              JOIN pasien h ON v.hewan_id = h.id
              JOIN pemilik p ON h.id_pemilik = p.id
              JOIN jadwal_vaksinasi jv ON v.jadwal_id = jv.id
              WHERE v.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='vaksinasi.php';</script>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status']; // Sudah Divaksin / Ditunda
        $catatan_dokter = sanitize($_POST['catatan_dokter']);
        
        if ($status == 'Sudah Divaksin') {
            $tanggal_vaksin = sanitize($_POST['tanggal_vaksin']);
            $jenis_vaksin = sanitize($_POST['jenis_vaksin']);
            $dosis = sanitize($_POST['dosis']);
            $next_vaksin_date = !empty($_POST['next_vaksin_date']) ? sanitize($_POST['next_vaksin_date']) : NULL;
            
            $stmt = $conn->prepare("UPDATE vaksinasi SET 
                status = ?, 
                dokter_id = ?, 
                tanggal_vaksin = ?, 
                jenis_vaksin = ?, 
                dosis = ?, 
                catatan_dokter = ?, 
                next_vaksin_date = ? 
                WHERE id = ?");
            $stmt->bind_param("sisssssi", $status, $dokter_id, $tanggal_vaksin, $jenis_vaksin, $dosis, $catatan_dokter, $next_vaksin_date, $id);
            
        } else { // Ditunda
            $alasan_penundaan = sanitize($_POST['alasan_penundaan']);
            
            $stmt = $conn->prepare("UPDATE vaksinasi SET 
                status = ?, 
                dokter_id = ?, 
                alasan_penundaan = ?, 
                catatan_dokter = ? 
                WHERE id = ?");
            $stmt->bind_param("sissi", $status, $dokter_id, $alasan_penundaan, $catatan_dokter, $id);
        }
        
        if ($stmt->execute()) {
            echo "<script>alert('Data vaksinasi berhasil disimpan!'); window.location.href='jadwal_vaksin_hari_ini.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data!');</script>";
        }
    }

    $page_title = 'Input Data Vaksinasi';
    include '../layouts/header_dashboard.php';
    ?>

    <div class="wrapper">
        <?php include '../layouts/sidebar_dokter.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h2><i class="fas fa-notes-medical me-2"></i>Pemeriksaan & Input Vaksinasi</h2>
                <a href="jadwal_vaksin_hari_ini.php" class="btn btn-secondary btn-custom">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-paw me-2"></i>Data Pasien</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Nama Hewan</th>
                                    <td>: <?php echo htmlspecialchars($data['nama_hewan']); ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis</th>
                                    <td>: <?php echo htmlspecialchars($data['jenis']); ?></td>
                                </tr>
                                <tr>
                                    <th>Ras</th>
                                    <td>: <?php echo htmlspecialchars($data['ras']); ?></td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>: <?php echo htmlspecialchars($data['gender']); ?></td>
                                </tr>
                                <tr>
                                    <th>Umur</th>
                                    <td>: <?php echo $data['umur']; ?> Tahun</td>
                                </tr>
                                <tr>
                                    <th>Pemilik</th>
                                    <td>: <?php echo htmlspecialchars($data['nama_pemilik']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Input Hasil Vaksinasi</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" id="formVaksin">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status Pemeriksaan</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusLayak" value="Sudah Divaksin" checked onchange="toggleForm()">
                                            <label class="form-check-label" for="statusLayak">
                                                Layak & Sudah Divaksin
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusTunda" value="Ditunda" onchange="toggleForm()">
                                            <label class="form-check-label" for="statusTunda">
                                                Tidak Layak / Ditunda
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Form Layak -->
                                <div id="formLayak">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tanggal Vaksin <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal_vaksin" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Jenis Vaksin <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="jenis_vaksin" value="<?php echo htmlspecialchars($data['jenis_vaksin_jadwal']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Dosis (ml/cc)</label>
                                            <input type="text" class="form-control" name="dosis" placeholder="Contoh: 1 ml">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Vaksin Berikutnya (Optional)</label>
                                            <input type="date" class="form-control" name="next_vaksin_date">
                                            <small class="text-muted">Isi jika perlu booster/ulangan.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Form Tunda -->
                                <div id="formTunda" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Penundaan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="alasan_penundaan" rows="2" placeholder="Jelaskan mengapa hewan tidak layak vaksin..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Catatan Dokter</label>
                                    <textarea class="form-control" name="catatan_dokter" rows="3" placeholder="Catatan tambahan hasil pemeriksaan..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary-custom-solid btn-lg w-100">
                                    <i class="fas fa-save me-2"></i>Simpan Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleForm() {
        const isLayak = document.getElementById('statusLayak').checked;
        const formLayak = document.getElementById('formLayak');
        const formTunda = document.getElementById('formTunda');
        const inputsLayak = formLayak.querySelectorAll('input');
        const inputTunda = formTunda.querySelector('textarea');
        
        if (isLayak) {
            formLayak.style.display = 'block';
            formTunda.style.display = 'none';
            inputsLayak.forEach(input => input.required = true);
            formLayak.querySelector('input[name="next_vaksin_date"]').required = false;
            formLayak.querySelector('input[name="dosis"]').required = false;
            inputTunda.required = false;
        } else {
            formLayak.style.display = 'none';
            formTunda.style.display = 'block';
            inputsLayak.forEach(input => input.required = false);
            inputTunda.required = true;
        }
    }
    toggleForm();
    </script>

    <?php include '../layouts/footer.php'; ?>

<?php
} else {
    // LOGIC LIST VAKSINASI (Default)
    $page_title = 'Riwayat Vaksinasi';
    include '../layouts/header_dashboard.php';
    ?>

    <div class="wrapper">
        <?php include '../layouts/sidebar_dokter.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h2><i class="fas fa-syringe me-2"></i>Riwayat Vaksinasi</h2>
                <div class="user-info">
                    <a href="<?php echo APP_URL; ?>/dokter/tambah_vaksinasi.php" class="btn btn-primary-custom-solid btn-custom">
                        <i class="fas fa-plus me-2"></i>Tambah Vaksinasi Manual
                    </a>
                </div>
            </div>
            
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="table-container">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pasien</th>
                                    <th>Jenis Vaksin</th>
                                    <th>Dosis</th>
                                    <th>Jadwal Booster</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->prepare("
                                    SELECT v.*, p.nama_hewan 
                                    FROM vaksinasi v
                                    JOIN pasien p ON v.hewan_id = p.id
                                    WHERE v.dokter_id = ?
                                    ORDER BY v.tanggal_vaksin DESC
                                ");
                                $stmt->bind_param("i", $dokter_id);
                                $stmt->execute();
                                $vaksin = $stmt->get_result();
                                $no = 1;
                                
                                if ($vaksin->num_rows > 0):
                                    while ($row = $vaksin->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo formatTanggal($row['tanggal_vaksin']); ?></td>
                                    <td><?php echo $row['nama_hewan']; ?></td>
                                    <td><?php echo $row['jenis_vaksin']; ?></td>
                                    <td><?php echo $row['dosis'] ?? '-'; ?></td>
                                    <td><?php echo $row['next_vaksin_date'] ? formatTanggal($row['next_vaksin_date']) : '-'; ?></td>
                                </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data vaksinasi</td>
                                </tr>
                                <?php 
                                endif; 
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../layouts/footer.php'; ?>
<?php } ?>