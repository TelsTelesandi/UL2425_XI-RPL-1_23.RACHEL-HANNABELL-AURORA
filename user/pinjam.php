<!-- pinjam.php -->
<?php include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $uid = $_SESSION['user_id'];
  $sid = $_POST['sarana_id'];
  $tgl = $_POST['tanggal_pinjam'];
  $jumlah = $_POST['jumlah_pinjam'];

  mysqli_query($conn, "INSERT INTO peminjaman (user_id, sarana_id, tanggal_pinjam, tanggal_kembali, jumlah_pinjam, status, catatan_admin)
    VALUES ($uid, $sid, '$tgl', '', $jumlah, 'menunggu', '')");
  echo "<div class='alert alert-success'>Peminjaman berhasil diajukan!</div>";
  }

$sarana = mysqli_query($conn, "SELECT * FROM sarana");
?>
<h3>Form Peminjaman</h3>
<form method="POST">
  <select name="sarana_id" class="form-control mb-2"><?php while ($s = mysqli_fetch_assoc($sarana)) {
   echo "<option value='{$s['sarana_id']}'>{$s['nama_sarana']}</option>";
  }
  ?>
  </select>
  <input type="date" name="tanggal_pinjam" class="form-control mb-2" required>
  <input type="number" name="jumlah_pinjam" class="form-control mb-2" placeholder="Jumlah" required>
  <button type="submit" class="btn btn-primary">Pinjam</button>
</form>
<?php include '../includes/footer.php'; ?>