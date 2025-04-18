<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "project";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $order_id = $_POST['order_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = uniqid("txn_");
    
    $sql = "INSERT INTO Payments (order_id, user_id, amount, payment_status, payment_method, transaction_id, created_at) 
            VALUES ('$order_id', '$user_id', '$amount', 'Pending', '$payment_method', '$transaction_id', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        echo "Payment successful. Transaction ID: " . $transaction_id;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>