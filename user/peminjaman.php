<?php 
include '../includes/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sarana_id = $_POST['sarana_id'];
    $jumlah_pinjam = $_POST['jumlah_pinjam'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    
    // Check available stock
    $stock_query = mysqli_query($conn, "SELECT s.*, 
                                      (s.jumlah_tersedia - COALESCE(SUM(CASE WHEN p.status = 'dipinjam' THEN p.jumlah_pinjam ELSE 0 END), 0)) as stok_tersedia
                                      FROM sarana s
                                      LEFT JOIN peminjaman p ON s.sarana_id = p.sarana_id
                                      WHERE s.sarana_id = $sarana_id
                                      GROUP BY s.sarana_id");
    $stock_data = mysqli_fetch_assoc($stock_query);
    
    if ($stock_data['stok_tersedia'] >= $jumlah_pinjam) {
        $query = mysqli_query($conn, "INSERT INTO peminjaman (user_id, sarana_id, jumlah_pinjam, tanggal_pinjam, status) 
                                     VALUES ($user_id, $sarana_id, $jumlah_pinjam, '$tanggal_pinjam', 'menunggu')");
        
        if ($query) {
            echo "<script>alert('Permintaan peminjaman berhasil diajukan!');window.location='peminjaman.php';</script>";
        } else {
            echo "<script>alert('Gagal mengajukan peminjaman!');</script>";
        }
    } else {
        echo "<script>alert('Stok tidak mencukupi! Stok tersedia: " . $stock_data['stok_tersedia'] . "');</script>";
    }
}

// Get available sarana
$sarana_query = "SELECT s.*, 
                (s.jumlah_tersedia - COALESCE(SUM(CASE WHEN p.status = 'dipinjam' THEN p.jumlah_pinjam ELSE 0 END), 0)) as stok_tersedia
                FROM sarana s
                LEFT JOIN peminjaman p ON s.sarana_id = p.sarana_id
                GROUP BY s.sarana_id
                HAVING stok_tersedia > 0
                ORDER BY s.nama_sarana ASC";
$sarana_data = mysqli_query($conn, $sarana_query);

// Get user's active loans
$loans_query = "SELECT p.*, s.nama_sarana, s.lokasi 
                FROM peminjaman p 
                JOIN sarana s ON p.sarana_id = s.sarana_id 
                WHERE p.user_id = $user_id 
                AND p.status IN ('menunggu', 'dipinjam', 'menunggu_pengembalian')
                ORDER BY p.peminjaman_id DESC";
$loans_data = mysqli_query($conn, $loans_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman Sarana - User</title>
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
                        <a class="nav-link active" href="peminjaman.php">
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

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Peminjaman Sarana</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pinjamModal">
                        <i class="bi bi-plus-circle"></i> Ajukan Peminjaman
                    </button>
                </div>

                <!-- Active Loans -->
                <h5 class="mb-3">Peminjaman Aktif</h5>
                <div class="table-responsive mb-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sarana</th>
                                <th>Jumlah</th>
                                <th>Lokasi</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($loan = mysqli_fetch_assoc($loans_data)) { 
                                $status_class = '';
                                $status_text = '';
                                
                                switch($loan['status']) {
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
                                }
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $loan['nama_sarana'] ?></td>
                                <td><?= $loan['jumlah_pinjam'] ?></td>
                                <td><?= $loan['lokasi'] ?></td>
                                <td><?= date('d/m/Y', strtotime($loan['tanggal_pinjam'])) ?></td>
                                <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                            </tr>
                            <?php } ?>
                            <?php if ($no == 1) { ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada peminjaman aktif</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pinjam Modal -->
    <div class="modal fade" id="pinjamModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sarana</label>
                            <select name="sarana_id" class="form-select" required>
                                <option value="">Pilih Sarana</option>
                                <?php while ($sarana = mysqli_fetch_assoc($sarana_data)) { ?>
                                    <option value="<?= $sarana['sarana_id'] ?>" data-stok="<?= $sarana['stok_tersedia'] ?>">
                                        <?= $sarana['nama_sarana'] ?> (Tersedia: <?= $sarana['stok_tersedia'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="jumlah_pinjam" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pinjam</label>
                            <input type="date" name="tanggal_pinjam" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ajukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelector('select[name="sarana_id"]').addEventListener('change', function() {
        const maxStok = this.options[this.selectedIndex].dataset.stok;
        const jumlahInput = document.querySelector('input[name="jumlah_pinjam"]');
        jumlahInput.max = maxStok;
        jumlahInput.value = '';
    });

    document.querySelector('input[name="jumlah_pinjam"]').addEventListener('input', function() {
        const maxStok = document.querySelector('select[name="sarana_id"]').options[
            document.querySelector('select[name="sarana_id"]').selectedIndex
        ].dataset.stok;
        
        if (parseInt(this.value) > parseInt(maxStok)) {
            alert('Jumlah melebihi stok tersedia!');
            this.value = maxStok;
        }
    });
    </script>
</body>
</html> 