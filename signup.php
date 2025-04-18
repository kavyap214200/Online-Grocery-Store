<?php
header("Content-Type: application/json");

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$db = "project"; // Make sure this matches your database name
$user = "root"; // Default username for XAMPP
$pass = ""; // Empty password for XAMPP, "root" for WAMP

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Ensure the users table exists
$tableQuery = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL
)";
$conn->query($tableQuery);

// Get input data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    die(json_encode(["success" => false, "message" => "No data received!"]));
}

$username = $data["username"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_DEFAULT); // Secure password hashing
$role = $data["role"];

// Insert data into database
$sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Signup successful!"]);
} else {
    echo json_encode(["success" => false, "message" => "Signup failed: " . $stmt->error]);
}

// Close connection
$conn->close();
?>