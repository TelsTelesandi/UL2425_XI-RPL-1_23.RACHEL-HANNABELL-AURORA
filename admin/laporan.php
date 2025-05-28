<!-- laporan.php -->
<?php include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$user_filter = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Build query with filters
$query = "SELECT p.*, u.nama_lengkap, s.nama_sarana, s.lokasi 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.user_id 
          JOIN sarana s ON p.sarana_id = s.sarana_id 
          WHERE 1=1";

if ($start_date) {
    $query .= " AND p.tanggal_pinjam >= '$start_date'";
}
if ($end_date) {
    $query .= " AND p.tanggal_pinjam <= '$end_date'";
}
if ($status) {
    $query .= " AND p.status = '$status'";
}
if ($user_filter) {
    $query .= " AND p.user_id = '$user_filter'";
}

$query .= " ORDER BY p.peminjaman_id DESC";
$data = mysqli_query($conn, $query);

// Get all users for filter
$users_query = mysqli_query($conn, "SELECT user_id, nama_lengkap FROM users WHERE role = 'user' ORDER BY nama_lengkap");

// Get summary data
$summary_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_peminjaman,
    SUM(CASE WHEN status = 'dipinjam' THEN 1 ELSE 0 END) as total_dipinjam,
    SUM(CASE WHEN status = 'dikembalikan' THEN 1 ELSE 0 END) as total_dikembalikan
    FROM peminjaman WHERE 1=1" . 
    ($start_date ? " AND tanggal_pinjam >= '$start_date'" : '') .
    ($end_date ? " AND tanggal_pinjam <= '$end_date'" : '') .
    ($user_filter ? " AND user_id = '$user_filter'" : ''));

$summary = mysqli_fetch_assoc($summary_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman - Admin</title>
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
        .summary-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-card .number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-card .label {
            color: #6c757d;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 15px;
            }
            .print-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .print-header h3 {
                margin-bottom: 10px;
                font-size: 18px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .print-header p {
                font-size: 14px;
                margin-bottom: 0;
            }
            .period-info {
                text-align: center;
                margin-bottom: 20px;
                font-size: 12px;
            }
            .no-print, .page-header {
                display: none !important;
            }
            .table {
                width: 100% !important;
                margin-bottom: 0;
                font-size: 12px;
            }
            .badge {
                border: 1px solid #000;
                font-size: 11px;
            }
            @page {
                margin: 0.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar no-print">
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
                        <a class="nav-link" href="pengembalian.php">
                            <i class="bi bi-arrow-return-left"></i> Approval Pengembalian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="laporan.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h3>Laporan Peminjaman Sarana</h3>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4 no-print">
                    <div class="col-md-4">
                        <div class="summary-card">
                            <div class="number"><?= $summary['total_peminjaman'] ?></div>
                            <div class="label">Total Peminjaman</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card">
                            <div class="number text-warning"><?= $summary['total_dipinjam'] ?></div>
                            <div class="label">Sedang Dipinjam</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card">
                            <div class="number text-success"><?= $summary['total_dikembalikan'] ?></div>
                            <div class="label">Sudah Dikembalikan</div>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mb-4 no-print">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="dipinjam" <?= $status == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                    <option value="dikembalikan" <?= $status == 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Peminjam</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Semua Peminjam</option>
                                    <?php while ($user = mysqli_fetch_assoc($users_query)) { ?>
                                        <option value="<?= $user['user_id'] ?>" <?= $user_filter == $user['user_id'] ? 'selected' : '' ?>>
                                            <?= $user['nama_lengkap'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="print-area">
                    <div class="print-header">
                        <h3>LAPORAN PEMINJAMAN SARANA</h3>
                        <p><?= date('d/m/Y H:i') ?></p>
                    </div>

                    <?php if ($start_date && $end_date) { ?>
                        <div class="period-info">
                            <p>Periode <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></p>
                        </div>
                    <?php } ?>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Peminjam</th>
                                    <th>Sarana</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                    <th>Kondisi Kembali</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($data)) { 
                                    $status_class = '';
                                    $status_text = '';
                                    
                                    switch($row['status']) {
                                        case 'menunggu':
                                            $status_class = 'bg-warning';
                                            $status_text = 'Menunggu Persetujuan';
                                            break;
                                        case 'dipinjam':
                                            $status_class = 'bg-primary';
                                            $status_text = 'Sedang Dipinjam';
                                            break;
                                        case 'menunggu_pengembalian':
                                            $status_class = 'bg-info';
                                            $status_text = 'Menunggu Konfirmasi Kembali';
                                            break;
                                        case 'dikembalikan':
                                            $status_class = 'bg-success';
                                            $status_text = 'Selesai';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'bg-danger';
                                            $status_text = 'Ditolak';
                                            break;
                                    }
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= $row['nama_sarana'] ?></td>
                                    <td><?= $row['jumlah_pinjam'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                    <td><?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-' ?></td>
                                    <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                    <td><?= $row['kondisi_kembali'] ? ucfirst(str_replace('_', ' ', $row['kondisi_kembali'])) : '-' ?></td>
                                    <td><?= $row['catatan_kembali'] ?: '-' ?></td>
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