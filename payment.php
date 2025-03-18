<?php
require_once '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get booking details
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;

// Fetch booking details from database with prepared statement
$sql = "SELECT * FROM roombook WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Invalid booking request for ID: " . $booking_id);
}

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
    <title>Secure Payment - Holiday Inn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a90e2;
            --secondary: #6c5ce7;
            --success: #00b894;
            --card-bg: linear-gradient(135deg, #2d3436, #636e72);
            --text-light: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .payment-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--shadow);
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
            color: var(--text-light);
            padding: 30px;
            text-align: center;
        }

        .payment-header h2 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .payment-body {
            padding: 30px;
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
            font-size: 16px;
        }

        .summary-item span:last-child {
            font-weight: 500;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--success);
        }

        .payment-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            color: var(--text-light);
            margin-bottom: 30px;
            position: relative;
            height: 220px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .card-chip {
            width: 50px;
            height: 35px;
            position: absolute;
            top: 20px;
            left: 20px;
            background: #d4af37;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }

        .card-visa {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
        }

        .card-number-display {
            position: absolute;
            top: 90px;
            left: 20px;
            right: 20px;
            font-size: 1.4em;
            letter-spacing: 4px;
            font-family: 'Courier New', Courier, monospace;
        }

        .card-details {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .card-expiry, .card-cvv {
            font-family: 'Courier New', Courier, monospace;
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
            color: var(--text-light);
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
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>Payment Gateway</h2>
            <p>Complete your reservation with Holiday Inn</p>
        </div>
        <div class="payment-body">
            <div class="payment-summary">
                <div class="summary-item">
                    <span>Room Type:</span>
                    <span><?php echo ucfirst($booking['RoomType']); ?></span>
                </div>
                <div class="summary-item">
                    <span>No. of Rooms:</span>
                    <span><?php echo $booking['NoofRoom']; ?></span>
                </div>
                <div class="summary-item">
                    <span>Meal Plan:</span>
                    <span><?php echo $booking['Meal']; ?></span>
                </div>
                <div class="summary-item">
                    <span>Check-In:</span>
                    <span><?php echo date('M d, Y', strtotime($booking['cin'])); ?></span>
                </div>
                <div class="summary-item">
                    <span>Check-Out:</span>
                    <span><?php echo date('M d, Y', strtotime($booking['cout'])); ?></span>
                </div>
                <div class="summary-item">
                    <span>Nights:</span>
                    <span><?php echo $nights; ?></span>
                </div>
                <div class="summary-item">
                    <span>Total:</span>
                    <span class="total-amount">$<?php echo number_format($amount, 2); ?></span>
                </div>
            </div>

            <div class="payment-card">
                <div class="card-chip">CHIP</div>
                <div class="card-visa">VISA</div>
                <input type="text" id="cardNumber" class="card-number-display" value="0000 0000 0000 0000" readonly>
                <div class="card-details">
                    <span class="card-expiry" id="cardExpiry">MM/YY</span>
                    <span class="card-cvv" id="cardCvv">***</span>
                </div>
            </div>

            <form method="post" action="process_payment.php">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-credit-card"></i>
                        <input type="text" class="form-input" name="card_number" id="cardNumberInput" placeholder="Card Number" pattern="\d{16}" maxlength="16" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="month" class="form-input" name="expiry" id="expiryInput" placeholder="MM/YY" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="text" class="form-input" name="cvv" id="cvvInput" placeholder="CVV" pattern="\d{3}" maxlength="3" required>
                    </div>
                </div>
                <button type="submit" class="pay-button">
                    <i class="fas fa-lock"></i>
                    Pay $<?php echo number_format($amount, 2); ?>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Sync input fields with card display
        const cardNumberInput = document.getElementById('cardNumberInput');
        const cardNumberDisplay = document.getElementById('cardNumber');
        const expiryInput = document.getElementById('expiryInput');
        const cardExpiry = document.getElementById('cardExpiry');
        const cvvInput = document.getElementById('cvvInput');
        const cardCvv = document.getElementById('cardCvv');

        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.match(/.{1,4}/g)?.join(' ') || value;
            cardNumberDisplay.value = value || '0000 0000 0000 0000';
        });

        expiryInput.addEventListener('input', function() {
            const value = this.value; // e.g., "2025-12"
            if (value) {
                const [year, month] = value.split('-');
                cardExpiry.textContent = `${month}/${year.slice(-2)}`;
            } else {
                cardExpiry.textContent = 'MM/YY';
            }
        });

        cvvInput.addEventListener('input', function() {
            cardCvv.textContent = this.value || '***';
        });
    </script>
</body>
</html>