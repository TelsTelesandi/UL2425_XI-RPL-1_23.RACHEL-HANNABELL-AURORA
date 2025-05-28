<?php
include 'config/db.php';

// Check table structure
$desc_query = "DESCRIBE peminjaman";
$result = mysqli_query($conn, $desc_query);

echo "Table structure:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "{$row['Field']}: {$row['Type']}\n";
}

// Check current status values
$status_query = "SELECT DISTINCT status FROM peminjaman";
$result = mysqli_query($conn, $status_query);

echo "\nCurrent status values:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "- {$row['status']}\n";
}

mysqli_close($conn);
?> 