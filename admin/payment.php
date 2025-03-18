<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// ... rest of your code


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get booking details
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;


// Fetch booking details from database
$sql = "SELECT * FROM roombook WHERE id='$booking_id'";
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);

if(!$booking) die("Invalid booking request");

// Calculate nights
$checkin = new DateTime($booking['cin']);
$checkout = new DateTime($booking['cout']);
$nights = $checkout->diff($checkin)->days;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* style.css (Enhanced Version) */
        :root {
            --primary: #4a90e2;
            --secondary: #6c5ce7;
            --success: #00b894;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .payment-header {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .payment-header h2 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-body {
            padding: 30px;
        }

        .payment-card {
            background: linear-gradient(45deg, #2d3436, #636e72);
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .card-chip {
            width: 40px;
            margin-bottom: 20px;
        }

        .card-number-input {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            font-size: 1.2em;
            letter-spacing: 2px;
            padding: 15px;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 20px;
        }

        .card-number-input::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .input-group {
            flex: 1;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }

        .form-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        .pay-button {
            background: var(--primary);
            color: white;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .pay-button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .payment-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--success);
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>Secure Payment Gateway</h2>
            <p>Complete your reservation</p>
        </div>
        
        <div class="payment-body">
            <div class="payment-summary">
                <div class="summary-item">
                    <span>Room Type:</span>
                    <span><?php echo ucfirst($room_type); ?></span>
                </div>
                <div class="summary-item">
                    <span>Nights:</span>
                    <span><?php echo $nights; ?></span>
                </div>
                <div class="summary-item">
                    <span>Total:</span>
                    <span class="total-amount">$<?php echo $total; ?></span>
                </div>
            </div>

            <div class="payment-card">
                <svg class="card-chip" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                </svg>
                <input type="text" class="card-number-input" placeholder="4242 4242 4242 4242" pattern="\d{16}">
            </div>

            <form method="post" action="process_payment.php">
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="month" class="form-input" placeholder="MM/YY" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="text" class="form-input" placeholder="CVV" pattern="\d{3}" required>
                    </div>
                </div>

                <button type="submit" class="pay-button">
                    <i class="fas fa-lock"></i>
                    Pay $<?php echo $total; ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>