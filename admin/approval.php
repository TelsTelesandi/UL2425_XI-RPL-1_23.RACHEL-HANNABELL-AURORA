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
    <title>Approval Peminjaman - E-Sarpras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .content-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .approval-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        .status-menunggu {
            background-color: #ffc107;
            color: black;
        }
        .status-dipinjam {
            background-color: #007bff;
            color: white;
        }
        .status-dikembalikan {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="mb-4">Admin</h3>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kelola_sarana.php">
                            <i class="bi bi-box"></i> Kelola Sarana
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="approval.php">
                            <i class="bi bi-check2-square"></i> Approval Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rekap.php">
                            <i class="bi bi-file-text"></i> Rekap Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../auth/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="content-header">
                    <h2 class="mb-0">Approval Peminjaman</h2>
                </div>

                <!-- Approval Section -->
                <div class="approval-section">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sarana</th>
                                <th>Peminjam</th>
                                <th>Kelas</th>
                                <th>Jumlah</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.*, s.nama_sarana, u.nama_lengkap, u.kelas 
                                   FROM peminjaman p 
                                   JOIN sarana s ON p.sarana_id = s.sarana_id 
                                   JOIN users u ON p.user_id = u.user_id 
                                   WHERE p.status = 'Menunggu Persetujuan'
                                   ORDER BY p.tanggal_pinjam DESC";
                            $result = mysqli_query($koneksi, $sql);
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_sarana']; ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['kelas']; ?></td>
                                    <td><?php echo $row['jumlah_pinjam']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                    <td>
                                        <span class="status-badge status-menunggu">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-success" onclick="approveRequest(<?php echo $row['peminjaman_id']; ?>)">
                                                <i class="bi bi-check-lg"></i> Setuju
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="rejectRequest(<?php echo $row['peminjaman_id']; ?>)">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function approveRequest(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?')) {
                window.location.href = 'proses_approval.php?id=' + id + '&action=approve';
            }
        }

        function rejectRequest(id) {
            if (confirm('Apakah Anda yakin ingin menolak peminjaman ini?')) {
                window.location.href = 'proses_approval.php?id=' + id + '&action=reject';
            }
        }
    </script>
</body>
</html> 