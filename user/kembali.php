<!-- kembali.php -->
<?php include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

$data = mysqli_query($conn, "SELECT p.*, s.nama_sarana FROM peminjaman p 
JOIN sarana s ON p.sarana_id = s.sarana_id
WHERE p.user_id={$_SESSION['user_id']} AND p.status='disetujui'");
?>
<h3>Ajukan Pengembalian</h3>
<table class="table">
  <tr>
    <th>Sarana</th>
    <th>Tgl Pinjam</th>
    <th>Status</th>
  </tr>
  <?php while ($r = mysqli_fetch_assoc($data)) { ?>
    <tr>
      <td><?= $r['nama_sarana'] ?></td>
      <td><?= $r['tanggal_pinjam'] ?></td>
      <td>Menunggu konfirmasi admin</td>
    </tr>
  <?php } ?>
</table>
<?php include '../includes/footer.php'; ?>