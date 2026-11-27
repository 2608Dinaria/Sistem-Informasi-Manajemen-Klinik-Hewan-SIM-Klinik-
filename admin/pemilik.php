<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

// Hapus pemilik
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM pemilik WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Pemilik berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus pemilik!';
    }
    $stmt->close();
}

$page_title = 'Data Pemilik';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-users me-2"></i>Data Pemilik</h2>
            <div class="user-info">
                <a href="<?php echo APP_URL; ?>/admin/tambah_pemilik.php" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-plus me-2"></i>Tambah Pemilik
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
                <div class="mb-3">
                    <input type="text" class="form-control form-control-custom search-input" placeholder="Cari nama, alamat, atau telepon...">
                </div>
                <div class="table-container">
                    <table class="table table-custom" id="tablePemilik">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("SELECT * FROM pemilik ORDER BY id DESC");
                            $no = 1;
                            if ($stmt->num_rows > 0):
                                while ($row = $stmt->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['alamat']; ?></td>
                                <td><?php echo $row['telepon']; ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/admin/edit_pemilik.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger btn-delete" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


