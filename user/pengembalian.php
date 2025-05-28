<?php 
include '../includes/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

// Handle return submission
if (isset($_GET['return']) && isset($_GET['id'])) {
    $peminjaman_id = $_GET['id'];
    
    // Update peminjaman status
    $query = mysqli_query($conn, "UPDATE peminjaman SET status = 'menunggu_pengembalian' WHERE peminjaman_id = $peminjaman_id AND user_id = $user_id");
    
    if ($query) {
        echo "<script>alert('Permintaan pengembalian berhasil diajukan!');window.location='pengembalian.php';</script>";
    } else {
        echo "<script>alert('Gagal mengajukan pengembalian!');</script>";
    }
}

// Get active loans
$query = "SELECT p.*, s.nama_sarana, s.lokasi 
          FROM peminjaman p 
          JOIN sarana s ON p.sarana_id = s.sarana_id 
          WHERE p.user_id = $user_id 
          AND p.status = 'dipinjam'
          ORDER BY p.peminjaman_id DESC";
$data = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pengembalian Sarana - User</title>
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
                <h4 class="mb-4">User</h4>
                <div class="mb-4">
                    <small class="text-muted">Selamat datang,</small>
                    <div class="fw-bold"><?= $user_data['nama_lengkap'] ?></div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php">
                            <i class="bi bi-arrow-left-right"></i> Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pengembalian.php">
                            <i class="bi bi-arrow-return-left"></i> Pengembalian
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
                <h3 class="mb-4">Pengembalian Sarana</h3>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
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
                                <td><?= $row['nama_sarana'] ?></td>
                                <td><?= $row['jumlah_pinjam'] ?></td>
                                <td><?= $row['lokasi'] ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td>
                                    <a href="?return=1&id=<?= $row['peminjaman_id'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('Ajukan pengembalian sarana ini?')">
                                        <i class="bi bi-arrow-return-left"></i> Kembalikan
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($no == 1) { ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada sarana yang sedang dipinjam</td>
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