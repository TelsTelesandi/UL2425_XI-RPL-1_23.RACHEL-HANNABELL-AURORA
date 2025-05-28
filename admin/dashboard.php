<!-- dashboard.php -->
<?php include '../includes/auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
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
                <h4 class="mb-4">Admin</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
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
            <div class="col-md-9 col-lg-10 main-content">
                <h3 class="mb-4">Dashboard Admin</h3>
                <!-- Content goes here -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Sarana</h5>
                                <p class="card-text h2">
                                    <?php
                                    include '../config/db.php';
                                    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM sarana");
                                    $data = mysqli_fetch_assoc($result);
                                    echo $data['total'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Peminjaman Aktif</h5>
                                <p class="card-text h2">
                                    <?php
                                    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'");
                                    $data = mysqli_fetch_assoc($result);
                                    echo $data['total'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Pengguna</h5>
                                <p class="card-text h2">
                                    <?php
                                    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
                                    $data = mysqli_fetch_assoc($result);
                                    echo $data['total'];
                                    ?>
                                </p>
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