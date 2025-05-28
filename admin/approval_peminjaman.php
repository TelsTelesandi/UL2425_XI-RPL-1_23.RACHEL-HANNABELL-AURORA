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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
                    <a class="nav-link active" href="approval_peminjaman.php">
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
                    <h2>Approval Peminjaman</h2>
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
                                                <span class="badge bg-warning">
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