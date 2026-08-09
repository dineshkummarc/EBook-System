<?php
// Database Configuration
// Update these values to match your server environment

$host     = 'localhost';
$dbname   = 'ebook_system';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;margin:40px auto;max-width:500px;">
        <h3>Database Connection Failed</h3>
        <p>' . mysqli_connect_error() . '</p>
        <p style="font-size:.875rem;color:#6b7280;">Please check your database credentials in <code>db_config.php</code></p>
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
