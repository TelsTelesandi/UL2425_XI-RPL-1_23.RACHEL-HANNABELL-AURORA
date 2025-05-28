<!-- sarana.php -->
<?php include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sarana = $_POST['nama_sarana'];
    $jumlah_tersedia = $_POST['jumlah_tersedia'];
    $lokasi = $_POST['lokasi'];
    
    $query = mysqli_query($conn, "INSERT INTO sarana (nama_sarana, jumlah_tersedia, lokasi, status) 
                                 VALUES ('$nama_sarana', $jumlah_tersedia, '$lokasi', 'tersedia')");
    
    if ($query) {
        echo "<script>alert('Sarana berhasil ditambahkan!');window.location='sarana.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan sarana!');</script>";
    }
}

// Get all sarana with current stock information
$query = "SELECT s.*, 
          s.jumlah_tersedia as total_stok,
          (s.jumlah_tersedia - COALESCE(SUM(CASE WHEN p.status = 'dipinjam' THEN p.jumlah_pinjam ELSE 0 END), 0)) as stok_tersedia,
          COALESCE(SUM(CASE WHEN p.status = 'dipinjam' THEN p.jumlah_pinjam ELSE 0 END), 0) as sedang_dipinjam
          FROM sarana s
          LEFT JOIN peminjaman p ON s.sarana_id = p.sarana_id
          GROUP BY s.sarana_id
          ORDER BY s.nama_sarana ASC";

$data = mysqli_query($conn, $query);
?>
<h3>Kelola Sarana</h3>
<form method="POST">
    <input name="nama_sarana" placeholder="Nama Sarana" required>
    <input name="jumlah_tersedia" type="number" placeholder="Jumlah" required>
    <input name="lokasi" placeholder="Lokasi" required>
    <button name="tambah" class="btn btn-primary">Tambah</button>
</form>
<table class="table mt-3">
    <tr>
        <th>Nama</th>
        <th>Total Stok</th>
        <th>Stok Tersedia</th>
        <th>Sedang Dipinjam</th>
        <th>Lokasi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while ($r = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $r['nama_sarana'] ?></td>
            <td><?= $r['total_stok'] ?></td>
            <td><?= $r['stok_tersedia'] ?></td>
            <td><?= $r['sedang_dipinjam'] ?></td>
            <td><?= $r['lokasi'] ?></td>
            <td><span class="badge bg-<?= $r['stok_tersedia'] > 0 ? 'success' : 'danger' ?>"><?= $r['stok_tersedia'] > 0 ? 'Tersedia' : 'Kosong' ?></span></td>
            <td><a href="?hapus=<?= $r['sarana_id'] ?>" onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm">Hapus</a></td>
        </tr>
    <?php } ?>
</table>
<?php include '../includes/footer.php'; ?>