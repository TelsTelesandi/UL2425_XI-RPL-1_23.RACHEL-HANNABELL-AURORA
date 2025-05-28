<?php
session_start();
require_once '../config/koneksi.php';
require_once '../auth/check_login.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Get statistics
$sql_dipinjam = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Sedang Dipinjam'";
$sql_dikembalikan = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dikembalikan'";
$sql_menunggu = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Menunggu Persetujuan'";

$result_dipinjam = mysqli_query($koneksi, $sql_dipinjam);
$result_dikembalikan = mysqli_query($koneksi, $sql_dikembalikan);
$result_menunggu = mysqli_query($koneksi, $sql_menunggu);

$dipinjam = mysqli_fetch_assoc($result_dipinjam)['total'];
$dikembalikan = mysqli_fetch_assoc($result_dikembalikan)['total'];
$menunggu = mysqli_fetch_assoc($result_menunggu)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Sarpras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-card {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card.dipinjam {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        .stats-card.dikembalikan {
            background: linear-gradient(45deg, #28a745, #1e7e34);
        }
        .stats-card.menunggu {
            background: linear-gradient(45deg, #ffc107, #d39e00);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        .content-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filter-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .table-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-cetak {
            background-color: #007bff;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-cetak:hover {
            background-color: #0056b3;
            color: white;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        .status-dipinjam {
            background-color: #007bff;
            color: white;
        }
        .status-dikembalikan {
            background-color: #28a745;
            color: white;
        }
        .status-menunggu {
            background-color: #ffc107;
            color: black;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>E-Sarpras</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kelola_sarana.php">
                        <i class="bi bi-box-seam"></i>
                        Kelola Sarana
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="approval_peminjaman.php">
                        <i class="bi bi-clipboard-check"></i>
                        Approval Peminjaman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="approval_pengembalian.php">
                        <i class="bi bi-arrow-return-left"></i>
                        Approval Pengembalian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="rekap.php">
                        <i class="bi bi-file-earmark-text"></i>
                        Rekap Data
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="../auth/logout.php">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <button class="btn btn-primary">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Sedang Dipinjam</h5>
                                <h2 class="mb-0"><?php echo $dipinjam; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Sudah Dikembalikan</h5>
                                <h2 class="mb-0"><?php echo $dikembalikan; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Menunggu Persetujuan</h5>
                                <h2 class="mb-0"><?php echo $menunggu; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card mt-4">
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Semua</option>
                                    <option value="Sedang Dipinjam">Sedang Dipinjam</option>
                                    <option value="dikembalikan">Dikembalikan</option>
                                    <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Peminjam</label>
                                <input type="text" class="form-control" name="peminjam" placeholder="Nama peminjam">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sarana</th>
                                        <th>Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT p.*, s.nama_sarana, u.nama_lengkap 
                                           FROM peminjaman p 
                                           JOIN sarana s ON p.sarana_id = s.sarana_id 
                                           JOIN users u ON p.user_id = u.user_id 
                                           ORDER BY p.tanggal_pinjam DESC";
                                    $result = mysqli_query($koneksi, $sql);
                                    $no = 1;

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $badge_class = '';
                                        switch ($row['status']) {
                                            case 'Sedang Dipinjam':
                                                $badge_class = 'badge bg-primary';
                                                break;
                                            case 'dikembalikan':
                                                $badge_class = 'badge bg-success';
                                                break;
                                            case 'Menunggu Persetujuan':
                                                $badge_class = 'badge bg-warning';
                                                break;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row['nama_sarana']; ?></td>
                                            <td><?php echo $row['nama_lengkap']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?php echo $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                                            <td><span class="<?php echo $badge_class; ?>"><?php echo $row['status']; ?></span></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewDetail(<?php echo $row['peminjaman_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 