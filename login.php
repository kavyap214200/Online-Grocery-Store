<?php
require 'db_connect.php'; // Include database connection

echo "Please enter your choice (signup/login): ";
$action = trim(fgets(STDIN));

function getInput($prompt) {
    echo $prompt;
    return trim(fgets(STDIN));
}

if ($action === "signup") {
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

    // Check if email already exists
    $email_check_stmt = $conn->prepare("SELECT * FROM sign WHERE email = ?");
    $email_check_stmt->bind_param("s", $email);
    $email_check_stmt->execute();
    $email_check_stmt->store_result();

    if ($email_check_stmt->num_rows > 0) {
        echo "Error: Email already exists. Please log in.\n";
        $email_check_stmt->close();
        $conn->close();
        exit;
    }

    $email_check_stmt->close();

    // Insert new record
    $stmt = $conn->prepare("INSERT INTO sign (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Signup successful! You can now log in.\n";
    } else {
        echo "Error: " . $stmt->error . "\n";
    }

    $stmt->close();
} elseif ($action === "login") {
    $email = getInput("Email: ");
    $password_input = getInput("Password: ");

    // Check if user exists
    $checkUser = $conn->prepare("SELECT password FROM sign WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows === 0) {
        die("Please Sign Up First.\n"); // This prevents login for unregistered users
    }

    // Verify password
    $checkUser->bind_result($storedPassword);
    $checkUser->fetch();

    if (password_verify($password_input, $storedPassword)) {
        echo "Login successful! Welcome back.\n";
    } else {
        echo "Incorrect password.\n";
    }

    $checkUser->close();
} else {
    echo "Invalid action. Please choose either signup or login.\n";
}

$conn->close();
?>
