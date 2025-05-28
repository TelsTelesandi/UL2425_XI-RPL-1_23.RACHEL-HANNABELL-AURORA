<?php
include 'config/db.php';

// Read SQL file
$sql = file_get_contents('add_sarana.sql');

// Execute SQL
if(mysqli_multi_query($conn, $sql)) {
    echo "Sarana berhasil ditambahkan!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 