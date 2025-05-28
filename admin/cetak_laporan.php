<?php
include '../includes/auth.php';
include '../config/db.php';

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$user_filter = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Build query with filters
$query = "SELECT p.*, u.nama_lengkap, s.nama_sarana 
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #8B5CF6;
            padding-bottom: 20px;
        }
        .print-header h1 {
            color: #8B5CF6;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .print-header p {
            margin: 5px 0;
            color: #666;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .table td {
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
        }
        .signature-space {
            margin-top: 80px;
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
            .badge {
                border: 1px solid #000;
                padding: 2px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Print Header -->
        <div class="print-header">
            <h1>E-SARPRAS SMK TELKOM TELESANDI BEKASI</h1>
            <p>Jl. KH. Mochammad, Marga Mulya, Bekasi Utara, Kota Bekasi</p>
            <p>Telp: (021) 123456 | Email: info@smktelkomtelesandi.sch.id</p>
            <h2 class="mt-4">LAPORAN PEMINJAMAN SARANA</h2>
            <p><?= date('d/m/Y') ?></p>
            
            <?php if ($start_date && $end_date) { ?>
                <p>Periode: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></p>
            <?php } ?>
        </div>

        <!-- Summary Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Total Peminjaman</h5>
                        <h3><?= $summary['total_peminjaman'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Sedang Dipinjam</h5>
                        <h3><?= $summary['total_dipinjam'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Sudah Dikembalikan</h5>
                        <h3><?= $summary['total_dikembalikan'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table">
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

        <!-- Footer with Signature -->
        <div class="footer">
            <p>Bekasi, <?= date('d/m/Y') ?></p>
            <p>Kepala Sekolah</p>
            <div class="signature-space"></div>
            <p>( ............................. )</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
