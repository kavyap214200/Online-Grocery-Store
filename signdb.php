<?php
echo "Please enter the following details:\n";

function getInput($prompt) {
    echo $prompt;
    return trim(fgets(STDIN));
}

$name = getInput("Name: ");
$email = getInput("Email: ");
$password_input = getInput("Password: ");
$role = getInput("Role (e.g., admin, user): ");

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.\n");
}

// Hash the password
$password = password_hash($password_input, PASSWORD_DEFAULT);

// Database connection
$servername = getenv('DB_SERVER') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$db_password = getenv('DB_PASS') ?: "";
$dbname = getenv('DB_NAME') ?: "project";

$conn = new mysqli($servername, $username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email already exists
$email_check_stmt = $conn->prepare("SELECT * FROM sign WHERE email = ?");
$email_check_stmt->bind_param("s", $email);
$email_check_stmt->execute();
$email_check_stmt->store_result();

if ($email_check_stmt->num_rows > 0) {
    echo "Error: Email already exists.\n";
    $email_check_stmt->close();
    $conn->close();
    exit;
}

$email_check_stmt->close();

// Insert new record
$stmt = $conn->prepare("INSERT INTO sign (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    echo "New record inserted successfully!\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>