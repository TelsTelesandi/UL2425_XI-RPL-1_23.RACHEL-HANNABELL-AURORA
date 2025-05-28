<!-- pengembalian.php -->
<?php include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

// Handle return approval
if (isset($_GET['action']) && isset($_GET['id'])) {
    $peminjaman_id = $_GET['id'];
    $action = $_GET['action'];
    
    // Get peminjaman data
    $check_query = mysqli_query($conn, "SELECT p.*, s.sarana_id, s.nama_sarana
                                      FROM peminjaman p 
                                      JOIN sarana s ON p.sarana_id = s.sarana_id
                                      WHERE p.peminjaman_id = $peminjaman_id");
    $peminjaman = mysqli_fetch_assoc($check_query);
    
    if ($action == 'approve') {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update peminjaman status
            $update_status = mysqli_query($conn, "UPDATE peminjaman 
                                                SET status = 'dikembalikan',
                                                    tanggal_kembali = CURRENT_DATE,
                                                    kondisi_kembali = '" . $_POST['kondisi_kembali'] . "',
                                                    catatan_kembali = '" . $_POST['catatan_kembali'] . "'
                                                WHERE peminjaman_id = $peminjaman_id");
            
            if (!$update_status) {
                throw new Exception("Gagal mengupdate status pengembalian");
            }
            
            mysqli_commit($conn);
            echo "<script>alert('Pengembalian berhasil dikonfirmasi!');window.location='pengembalian.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "');window.location='pengembalian.php';</script>";
        }
    }
}

// Get pending returns
$query = "SELECT p.*, u.nama_lengkap, s.nama_sarana, s.lokasi
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.user_id
          JOIN sarana s ON p.sarana_id = s.sarana_id
          WHERE p.status = 'menunggu_pengembalian'
          ORDER BY p.peminjaman_id DESC";
$data = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approval Pengembalian - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h4 class="mb-4">Admin</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sarana.php">
                            <i class="bi bi-box"></i> Kelola Sarana
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php">
                            <i class="bi bi-arrow-left-right"></i> Approval Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pengembalian.php">
                            <i class="bi bi-arrow-return-left"></i> Approval Pengembalian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">
                            <i class="bi bi-file-text"></i> Rekap Data
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link text-danger" href="../logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h3 class="mb-4">Approval Pengembalian</h3>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Sarana</th>
                                <th>Jumlah</th>
                                <th>Lokasi</th>
                                <th>Tanggal Pinjam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($data)) { 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td><?= $row['nama_sarana'] ?></td>
                                <td><?= $row['jumlah_pinjam'] ?></td>
                                <td><?= $row['lokasi'] ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#returnModal<?= $row['peminjaman_id'] ?>">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Kembali
                                    </button>
                                </td>
                            </tr>

                            <!-- Return Modal -->
                            <div class="modal fade" id="returnModal<?= $row['peminjaman_id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="?action=approve&id=<?= $row['peminjaman_id'] ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Kondisi Saat Kembali</label>
                                                    <select name="kondisi_kembali" class="form-select" required>
                                                        <option value="baik">Baik</option>
                                                        <option value="rusak_ringan">Rusak Ringan</option>
                                                        <option value="rusak_berat">Rusak Berat</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="catatan_kembali" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Konfirmasi Kembali</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($no == 1) { ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada permintaan pengembalian yang menunggu konfirmasi</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>