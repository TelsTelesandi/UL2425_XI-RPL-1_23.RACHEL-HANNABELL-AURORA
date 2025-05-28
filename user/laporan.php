<!-- laporan.php -->
<?php 
include '../includes/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query with filters
$query = "SELECT p.*, u.nama_lengkap, s.nama_sarana 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.user_id 
          JOIN sarana s ON p.sarana_id = s.sarana_id 
          WHERE p.user_id = $user_id";

if ($start_date) {
    $query .= " AND p.tanggal_pinjam >= '$start_date'";
}
if ($end_date) {
    $query .= " AND p.tanggal_pinjam <= '$end_date'";
}
if ($status) {
    $query .= " AND p.status = '$status'";
}

$query .= " ORDER BY p.peminjaman_id DESC";
$data = mysqli_query($conn, $query);

// Get user data
$user_query = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE user_id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

// Get summary data
$summary_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_peminjaman,
    SUM(CASE WHEN status = 'dipinjam' THEN 1 ELSE 0 END) as total_dipinjam,
    SUM(CASE WHEN status = 'dikembalikan' THEN 1 ELSE 0 END) as total_dikembalikan
    FROM peminjaman 
    WHERE user_id = $user_id" .
    ($start_date ? " AND tanggal_pinjam >= '$start_date'" : '') .
    ($end_date ? " AND tanggal_pinjam <= '$end_date'" : '') .
    ($status ? " AND status = '$status'" : ''));

$summary = mysqli_fetch_assoc($summary_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Peminjaman - User</title>
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
            <div class="col-md-3 col-lg-2 sidebar no-print">
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
                        <a class="nav-link" href="pengembalian.php">
                            <i class="bi bi-arrow-return-left"></i> Pengembalian
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
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h3>Riwayat Peminjaman</h3>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>

                <div class="print-area">
                    <div class="print-header">
                        <h3>LAPORAN PEMINJAMAN SARANA</h3>
                        <p><?= date('d/m/Y') ?></p>
                    </div>

                    <?php if (mysqli_num_rows($data) > 0) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
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
                    <?php } else { ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Belum ada riwayat peminjaman.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>