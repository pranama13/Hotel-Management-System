<?php
session_start();
include '../config.php';

$booking_id = $_POST['booking_id'];
$amount = $_POST['amount'];

// Validate card details (basic example)
$card_valid = preg_match('/^\d{16}$/', $_POST['card_number']);
$expiry_valid = preg_match('/^[0-9]{4}-[0-9]{2}$/', $_POST['expiry']);
$cvv_valid = preg_match('/^\d{3}$/', $_POST['cvv']);

if (!$card_valid || !$expiry_valid || !$cvv_valid) {
    echo "
    <html>
    <head>
    <script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
    </head>
    <body>
    <script>
    swal({
        title: 'Error',
        text: 'Invalid payment details',
        icon: 'error',
        button: 'Go Back',
    }).then(() => {
        history.back();
    });
    </script>
    </body>
    </html>
    ";
    exit;
}

// Update database
$sql = "UPDATE roombook SET 
    stat = 'Confirmed',
    payment_date = NOW()
    WHERE id = '$booking_id'";

if (mysqli_query($conn, $sql)) {
    echo "
    <html>
    <head>
    <script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
    </head>
    <body>
    <script>
    swal({
        title: 'Reservation Successful',
        text: 'Your reservation has been confirmed. Redirecting to home...',
        icon: 'success',
        timer: 3000,
        buttons: false,
    }).then(() => {
        window.location = 'home.php';
    });
    </script>
    </body>
    </html>
    ";
} else {
    echo "
    <html>
    <head>
    <script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
    </head>
    <body>
    <script>
    swal({
        title: 'Error',
        text: 'Payment processing failed: " . addslashes(mysqli_error($conn)) . "',
        icon: 'error',
        button: 'Go Back',
    }).then(() => {
        history.back();
    });
    </script>
    </body>
    </html>
    ";
}
?>