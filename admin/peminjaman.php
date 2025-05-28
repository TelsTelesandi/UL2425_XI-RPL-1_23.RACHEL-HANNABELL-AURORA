<!-- peminjaman.php -->
<?php include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

// Handle approval/rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $peminjaman_id = $_GET['id'];
    $action = $_GET['action'];
    
    // Get peminjaman data
    $check_query = mysqli_query($conn, "SELECT p.*, s.jumlah_tersedia, s.sarana_id,
                                      (s.jumlah_tersedia - COALESCE(SUM(CASE WHEN p2.status = 'dipinjam' AND p2.peminjaman_id != p.peminjaman_id THEN p2.jumlah_pinjam ELSE 0 END), 0)) as stok_tersedia
                                      FROM peminjaman p 
                                      JOIN sarana s ON p.sarana_id = s.sarana_id
                                      LEFT JOIN peminjaman p2 ON s.sarana_id = p2.sarana_id
                                      WHERE p.peminjaman_id = $peminjaman_id
                                      GROUP BY p.peminjaman_id");
    $peminjaman = mysqli_fetch_assoc($check_query);
    
    if ($action == 'approve') {
        if ($peminjaman['stok_tersedia'] >= $peminjaman['jumlah_pinjam']) {
            // Start transaction
            mysqli_begin_transaction($conn);
            
            try {
                // Update peminjaman status
                $update_status = mysqli_query($conn, "UPDATE peminjaman SET status = 'dipinjam' WHERE peminjaman_id = $peminjaman_id");
                
                if (!$update_status) {
                    throw new Exception("Gagal mengupdate status peminjaman");
                }
                
                mysqli_commit($conn);
                echo "<script>alert('Peminjaman berhasil disetujui!');window.location='peminjaman.php';</script>";
            } catch (Exception $e) {
                mysqli_rollback($conn);
                echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "');window.location='peminjaman.php';</script>";
            }
        } else {
            echo "<script>alert('Stok tidak mencukupi! Stok tersedia: " . $peminjaman['stok_tersedia'] . "');window.location='peminjaman.php';</script>";
        }
    } elseif ($action == 'reject') {
        $query = mysqli_query($conn, "UPDATE peminjaman SET status = 'ditolak' WHERE peminjaman_id = $peminjaman_id");
        if ($query) {
            echo "<script>alert('Peminjaman ditolak!');window.location='peminjaman.php';</script>";
        }
    }
}

// Get pending requests
$query = "SELECT p.*, u.nama_lengkap, s.nama_sarana, s.lokasi,
          (s.jumlah_tersedia - COALESCE(SUM(CASE WHEN p2.status = 'dipinjam' AND p2.peminjaman_id != p.peminjaman_id THEN p2.jumlah_pinjam ELSE 0 END), 0)) as stok_tersedia
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.user_id
          JOIN sarana s ON p.sarana_id = s.sarana_id
          LEFT JOIN peminjaman p2 ON s.sarana_id = p2.sarana_id
          WHERE p.status = 'menunggu'
          GROUP BY p.peminjaman_id
          ORDER BY p.peminjaman_id DESC";
$data = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approval Peminjaman - Admin</title>
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
                        <a class="nav-link active" href="peminjaman.php">
                            <i class="bi bi-arrow-left-right"></i> Approval Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pengembalian.php">
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
                <h3 class="mb-4">Approval Peminjaman</h3>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Sarana</th>
                                <th>Jumlah</th>
                                <th>Stok Tersedia</th>
                                <th>Lokasi</th>
                                <th>Tanggal Pinjam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($data)) { 
                                $can_approve = $row['stok_tersedia'] >= $row['jumlah_pinjam'];
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td><?= $row['nama_sarana'] ?></td>
                                <td><?= $row['jumlah_pinjam'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $can_approve ? 'success' : 'danger' ?>">
                                        <?= $row['stok_tersedia'] ?>
                                    </span>
                                </td>
                                <td><?= $row['lokasi'] ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td>
                                    <?php if ($can_approve) { ?>
                                        <a href="?action=approve&id=<?= $row['peminjaman_id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </a>
                                    <?php } ?>
                                    <a href="?action=reject&id=<?= $row['peminjaman_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman ini?')">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($no == 1) { ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada permintaan peminjaman yang menunggu persetujuan</td>
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