<?php
session_start();
require_once '../config/koneksi.php';
require_once '../auth/check_login.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Data - E-Sarpras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
        .rekap-section {
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
                    <a class="nav-link" href="index.php">
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
                    <a class="nav-link active" href="rekap.php">
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
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="stats-card">
                        <div class="stats-number text-primary">
                            <?php
                            $sql = "SELECT COUNT(*) as count FROM peminjaman WHERE status = 'Sedang Dipinjam'";
                            $result = mysqli_query($koneksi, $sql);
                            $row = mysqli_fetch_assoc($result);
                            echo $row['count'];
                            ?>
                        </div>
                        <div class="stats-label">Sedang Dipinjam</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <div class="stats-number text-success">
                            <?php
                            $sql = "SELECT COUNT(*) as count FROM peminjaman WHERE status = 'dikembalikan'";
                            $result = mysqli_query($koneksi, $sql);
                            $row = mysqli_fetch_assoc($result);
                            echo $row['count'];
                            ?>
                        </div>
                        <div class="stats-label">Sudah Dikembalikan</div>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Rekap Data</h2>
                <a href="cetak_laporan.php" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </a>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3" method="GET">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua</option>
                                <option value="Sedang Dipinjam" <?php echo ($_GET['status'] ?? '') === 'Sedang Dipinjam' ? 'selected' : ''; ?>>Sedang Dipinjam</option>
                                <option value="dikembalikan" <?php echo ($_GET['status'] ?? '') === 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                                <option value="Menunggu Persetujuan" <?php echo ($_GET['status'] ?? '') === 'Menunggu Persetujuan' ? 'selected' : ''; ?>>Menunggu Persetujuan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Peminjam</label>
                            <input type="text" class="form-control" name="peminjam" placeholder="Nama peminjam" value="<?php echo $_GET['peminjam'] ?? ''; ?>">
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
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Sarana</th>
                                    <th>Peminjam</th>
                                    <th>Kelas</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $where = [];
                                $params = [];

                                if (!empty($_GET['start_date'])) {
                                    $where[] = "p.tanggal_pinjam >= ?";
                                    $params[] = $_GET['start_date'];
                                }

                                if (!empty($_GET['end_date'])) {
                                    $where[] = "p.tanggal_pinjam <= ?";
                                    $params[] = $_GET['end_date'];
                                }

                                if (!empty($_GET['status'])) {
                                    $where[] = "p.status = ?";
                                    $params[] = $_GET['status'];
                                }

                                if (!empty($_GET['peminjam'])) {
                                    $where[] = "u.nama_lengkap LIKE ?";
                                    $params[] = "%{$_GET['peminjam']}%";
                                }

                                $sql = "SELECT p.*, s.nama_sarana, u.nama_lengkap, u.kelas 
                                       FROM peminjaman p 
                                       JOIN sarana s ON p.sarana_id = s.sarana_id 
                                       JOIN users u ON p.user_id = u.user_id";

                                if (!empty($where)) {
                                    $sql .= " WHERE " . implode(" AND ", $where);
                                }

                                $sql .= " ORDER BY p.tanggal_pinjam DESC";

                                $stmt = mysqli_prepare($koneksi, $sql);

                                if (!empty($params)) {
                                    $types = str_repeat('s', count($params));
                                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                                }

                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
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
                                        <td><?php echo $row['kelas']; ?></td>
                                        <td><?php echo $row['jumlah_pinjam']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td><?php echo $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                                        <td><span class="<?php echo $badge_class; ?>"><?php echo $row['status']; ?></span></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 