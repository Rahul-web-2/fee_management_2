<?php
// Database connection details
$servername = "localhost"; // Change if your database server is not localhost
$username = "root";        // Default username for XAMPP
$password = "";            // Default password for XAMPP (leave empty)
$dbname = "fee_database";  // Replace with your database name

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>