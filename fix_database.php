<?php
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/db.php';

// Drop the status column and recreate it
$queries = [
    "ALTER TABLE peminjaman DROP COLUMN status",
    "ALTER TABLE peminjaman ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'menunggu'"
];

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "Success: " . $query . "\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
}

// Show the current table structure
$desc = mysqli_query($conn, "DESCRIBE peminjaman");
echo "\nTable structure:\n";
while ($row = mysqli_fetch_assoc($desc)) {
    echo "{$row['Field']}: {$row['Type']}\n";
}

mysqli_close($conn);
?> 