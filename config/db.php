<!-- db.php -->
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pjb_rachel_hannabell_aurora";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>

