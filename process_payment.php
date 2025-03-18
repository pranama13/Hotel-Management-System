<?php
session_start();
include '../config.php';

// Basic validation
if(empty($_POST['card_number']) || empty($_POST['expiry']) || empty($_POST['cvv'])) {
    die("Please fill all payment fields");
}

$booking_id = $_POST['booking_id'];
$amount = $_POST['amount'];

// Validate card details (basic example)
$card_valid = preg_match('/^\d{16}$/', $_POST['card_number']);
$expiry_valid = preg_match('/^[0-9]{4}-[0-9]{2}$/', $_POST['expiry']);
$cvv_valid = preg_match('/^\d{3}$/', $_POST['cvv']);

if(!$card_valid || !$expiry_valid || !$cvv_valid) {
    die("Invalid payment details");
}

// Update database
$sql = "UPDATE roombook SET 
    stat = 'Confirmed',
    payment_date = NOW()
    WHERE id = '$booking_id'";

if(mysqli_query($conn, $sql)) {
    header("Location: reservation_complete.php?id=$booking_id");
} else {
    echo "Payment processing failed: " . mysqli_error($conn);
}