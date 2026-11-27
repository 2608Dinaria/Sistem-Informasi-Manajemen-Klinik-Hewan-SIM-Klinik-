<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

// Ambil ID Dokter
$stmt = $conn->prepare("SELECT id FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter_data = $stmt->get_result()->fetch_assoc();
$dokter_id = $dokter_data['id'];

$page_title = 'Jadwal Vaksinasi Hari Ini';
include '../layouts/header_dashboard.php';

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$where_date = "";

if ($filter == 'today') {
    $where_date = "AND jv.tanggal = CURDATE()";
} elseif ($filter == 'tomorrow') {
    $where_date = "AND jv.tanggal = CURDATE() + INTERVAL 1 DAY";
} elseif ($filter == 'upcoming') {
    $where_date = "AND jv.tanggal >= CURDATE()";
}

// Query Utama
$query = "SELECT 
            jv.*, 
            v.id AS vaksinasi_id, 
            v.status AS status_vaksinasi,
            p.nama_hewan,
            p.jenis AS jenis_hewan,
            pm.nama AS nama_pemilik,
            pm.telepon
          FROM jadwal_vaksinasi jv
          JOIN vaksinasi v ON jv.id = v.jadwal_id
          JOIN pasien p ON p.id = v.hewan_id
          JOIN pemilik pm ON pm.id = p.id_pemilik
          WHERE jv.dokter_id = ?
            $where_date
            AND v.status IN ('Menunggu Konfirmasi', 'Dikonfirmasi', 'Sudah Divaksin', 'Ditunda')
          ORDER BY jv.tanggal ASC, jv.waktu ASC";

// Note: User asked for 'Menunggu Konfirmasi Admin', 'Sudah Dijadwalkan', 'Dikonfirmasi'.
// My system uses 'Menunggu Konfirmasi', 'Dikonfirmasi', 'Sudah Divaksin', 'Ditunda'.
// 'Sudah Dijadwalkan' is not in my ENUM.
// I will include 'Menunggu Konfirmasi' (though usually doctor only sees confirmed), 'Dikonfirmasi'.
// User said "daftar jadwal vaksinasi yang sudah dikonfirmasi".
// But the query snippet included 'Menunggu Konfirmasi Admin'.
// I'll stick to 'Dikonfirmasi' mostly, but user query had more.
// I'll include 'Dikonfirmasi', 'Sudah Divaksin', 'Ditunda' to show history/progress too?
// User query: "AND v.status IN ('Menunggu Konfirmasi Admin', 'Sudah Dijadwalkan', 'Dikonfirmasi')"
// My ENUM: 'Menunggu Konfirmasi', 'Dikonfirmasi', 'Ditolak', 'Ditunda', 'Sudah Divaksin'.
// I will use: 'Dikonfirmasi', 'Sudah Divaksin', 'Ditunda'. 'Menunggu Konfirmasi' is for admin.
// Wait, if I follow user query strictly, I might miss my own statuses.
// I'll use my statuses that make sense for "Confirmed and ready/done".
// Actually, let's include 'Dikonfirmasi' (Ready), 'Sudah Divaksin' (Done), 'Ditunda' (Delayed).
// 'Menunggu Konfirmasi' is usually not for doctor.
// But I will follow the user's intent of "confirmed schedules".

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dokter_id);
$stmt->execute();
$result = $stmt->get_result();

// Grouping Data
$jadwal_group = [];
while ($row = $result->fetch_assoc()) {
    $jadwal_id = $row['id'];
    if (!isset($jadwal_group[$jadwal_id])) {
        $jadwal_group[$jadwal_id] = [
            'info' => [
                'tanggal' => $row['tanggal'],
                'waktu' => $row['waktu'],
                'jenis_vaksin' => $row['jenis_vaksin'],
                'catatan' => $row['catatan']
            ],
            'pasien' => []
        ];
    }
    $jadwal_group[$jadwal_id]['pasien'][] = $row;
}
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-check me-2"></i>Jadwal Vaksinasi</h2>
        </div>
        
        <!-- Filter Buttons -->
        <div class="mb-4">
            <a href="?filter=today" class="btn <?php echo $filter == 'today' ? 'btn-primary' : 'btn-outline-primary'; ?> me-2">Hari Ini</a>
            <a href="?filter=tomorrow" class="btn <?php echo $filter == 'tomorrow' ? 'btn-primary' : 'btn-outline-primary'; ?> me-2">Besok</a>
            <a href="?filter=upcoming" class="btn <?php echo $filter == 'upcoming' ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua Jadwal</a>
        </div>
        
        <?php if (empty($jadwal_group)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>Tidak ada jadwal vaksinasi untuk filter ini.
            </div>
        <?php else: ?>
            <?php foreach ($jadwal_group as $jadwal_id => $data): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-primary fw-bold mb-0">
                                <i class="fas fa-calendar-day me-2"></i><?php echo formatTanggal($data['info']['tanggal']); ?> 
                                <span class="text-muted ms-2"><i class="far fa-clock me-1"></i> <?php echo formatWaktu($data['info']['waktu']); ?></span>
                            </h5>
                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($data['info']['jenis_vaksin']); ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty(trim($data['info']['catatan']))): ?>
                            <div class="catatan-summernote mb-3 p-3 bg-light rounded">
                                <?php echo $data['info']['catatan']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Hewan</th>
                                        <th>Jenis</th>
                                        <th>Pemilik</th>
                                        <th>No HP</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['pasien'] as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['nama_hewan']); ?></td>
                                            <td><?php echo htmlspecialchars($p['jenis_hewan']); ?></td>
                                            <td><?php echo htmlspecialchars($p['nama_pemilik']); ?></td>
                                            <td><?php echo htmlspecialchars($p['telepon']); ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = 'bg-secondary';
                                                if ($p['status_vaksinasi'] == 'Sudah Divaksin') $badgeClass = 'bg-success';
                                                elseif ($p['status_vaksinasi'] == 'Dikonfirmasi') $badgeClass = 'bg-primary';
                                                elseif ($p['status_vaksinasi'] == 'Ditunda') $badgeClass = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $p['status_vaksinasi']; ?></span>
                                            </td>
                                            <td>
                                                <?php if ($p['status_vaksinasi'] == 'Dikonfirmasi' || $p['status_vaksinasi'] == 'Ditunda'): ?>
                                                    <a href="vaksinasi.php?id=<?php echo $p['vaksinasi_id']; ?>" class="btn btn-primary btn-sm">
                                                        Mulai Vaksinasi
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>Selesai</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
