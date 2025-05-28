<!-- dashboard.php -->
<?php 
include '../includes/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE user_id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

// Get user's active loans
$active_loans = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = $user_id AND status = 'dipinjam'");
$active_loans_data = mysqli_fetch_assoc($active_loans);

// Get user's total loans
$total_loans = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = $user_id");
$total_loans_data = mysqli_fetch_assoc($total_loans);

// Get user's returned items
$returned_items = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = $user_id AND status = 'dikembalikan'");
$returned_items_data = mysqli_fetch_assoc($returned_items);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - User</title>
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
            <div class="col-md-3 col-lg-2 sidebar">
                <h4 class="mb-4">User</h4>
                <div class="mb-4">
                    <small class="text-muted">Selamat datang,</small>
                    <div class="fw-bold"><?= $user_data['nama_lengkap'] ?></div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php">
                            <i class="bi bi-arrow-left-right"></i> Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pengembalian.php">
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
            <div class="col-md-9 col-lg-10 main-content">
                <h3 class="mb-4">Dashboard</h3>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Peminjaman Aktif</h5>
                                <p class="card-text h2"><?= $active_loans_data['total'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Sudah Dikembalikan</h5>
                                <p class="card-text h2"><?= $returned_items_data['total'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Peminjaman</h5>
                                <p class="card-text h2"><?= $total_loans_data['total'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Menu Cepat</h5>
                                <div class="list-group">
                                    <a href="peminjaman.php" class="list-group-item list-group-item-action">
                                        <i class="bi bi-plus-circle me-2"></i> Buat Peminjaman Baru
                                    </a>
                                    <a href="pengembalian.php" class="list-group-item list-group-item-action">
                                        <i class="bi bi-arrow-return-left me-2"></i> Proses Pengembalian
                                    </a>
                                    <a href="laporan.php" class="list-group-item list-group-item-action">
                                        <i class="bi bi-file-text me-2"></i> Lihat Riwayat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Peminjaman Aktif</h5>
                                <?php
                                $active_items = mysqli_query($conn, "SELECT p.*, s.nama_sarana 
                                    FROM peminjaman p 
                                    JOIN sarana s ON p.sarana_id = s.sarana_id 
                                    WHERE p.user_id = $user_id AND p.status = 'dipinjam' 
                                    ORDER BY p.peminjaman_id DESC 
                                    LIMIT 5");
                                
                                if (mysqli_num_rows($active_items) > 0) {
                                    echo '<div class="list-group">';
                                    while ($item = mysqli_fetch_assoc($active_items)) {
                                        echo '<div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">' . $item['nama_sarana'] . '</h6>
                                                <small>Dipinjam: ' . $item['tanggal_pinjam'] . '</small>
                                            </div>
                                        </div>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo '<p class="text-muted">Tidak ada peminjaman aktif</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>