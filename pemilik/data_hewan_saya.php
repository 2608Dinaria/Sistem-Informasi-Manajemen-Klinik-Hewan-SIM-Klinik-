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

// Hapus hewan
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM pasien WHERE id = ? AND id_pemilik = ?");
    $stmt->bind_param("ii", $id, $pemilik_id);
    if ($stmt->execute()) {
        $success = 'Hewan berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus hewan!';
    }
    $stmt->close();
}

$page_title = 'Data Hewan Saya';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-paw me-2"></i>Data Hewan Saya</h2>
            <div class="user-info">
                <a href="<?php echo APP_URL; ?>/pemilik/tambah_hewan.php" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-plus me-2"></i>Tambah Hewan
                </a>
            </div>
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
        
        <div class="card-custom">
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Hewan</th>
                                <th>Jenis</th>
                                <th>Ras</th>
                                <th>Umur</th>
                                <th>Gender</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM pasien WHERE id_pemilik = ? ORDER BY id ASC");
                            $stmt->bind_param("i", $pemilik_id);
                            $stmt->execute();
                            $hewan = $stmt->get_result();
                            $no = 1;
                            
                            if ($hewan->num_rows > 0):
                                while ($row = $hewan->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_hewan']; ?></td>
                                <td><?php echo $row['jenis']; ?></td>
                                <td><?php echo $row['ras'] ?? '-'; ?></td>
                                <td><?php echo $row['umur'] ? $row['umur'] . ' tahun' : '-'; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td>
                                    <a href="edit_hewan.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="rekam_medis_hewan.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm me-1">
                                        <i class="fas fa-notes-medical"></i>
                                    </a>

                                    <a href="?hapus=<?php echo $row['id']; ?>" 
                                    class="btn btn-sm btn-danger btn-delete" 
                                    onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>

                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada hewan terdaftar</td>
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

