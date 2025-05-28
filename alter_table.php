<?php
include 'config/db.php';

// Alter the table to increase status column length
$alter_query = "ALTER TABLE peminjaman MODIFY COLUMN status VARCHAR(50) NOT NULL";
if (mysqli_query($conn, $alter_query)) {
    echo "Table modified successfully\n";
} else {
    echo "Error modifying table: " . mysqli_error($conn) . "\n";
}

// Update any existing status values to match new scheme
$update_query = "UPDATE peminjaman SET status = 'dikembalikan' WHERE status = 'selesai' OR status = 'kembali'";
if (mysqli_query($conn, $update_query)) {
    echo "Status values updated successfully\n";
} else {
    echo "Error updating status values: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?> 