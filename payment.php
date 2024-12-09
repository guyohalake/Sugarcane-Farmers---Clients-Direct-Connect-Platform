<?php
session_start();
require_once '../config/database.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.html");
    exit();
}
if (empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit();
}
error_log("Cart contents before calculating total: " . print_r($_SESSION['cart'], true));

$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    if (isset($item['total_price'])) {
        $total_amount += $item['total_price'];
    } else {
        error_log("Missing total_price in cart item: " . print_r($item, true));
    }
}
error_log("Calculated total amount: KSH " . $total_amount); 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $payment_successful = processPayment($total_amount); 

    if ($payment_successful) {
       
        try {
            $conn->beginTransaction();

            foreach ($_SESSION['cart'] as $item) {
                $stmt = $conn->prepare("
                    UPDATE orders 
                    SET status = 'processed' 
                    WHERE customer_id = ? AND product_id = ? AND status = 'pending'
                ");
                $stmt->execute([$_SESSION['user_id'], $item['product_id']]);
            }

            $conn->commit();
           
            unset($_SESSION['cart']);
            header("Location: confirmation.php");
            exit();
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            echo "Error updating order status: " . $e->getMessage();
        }
    } else {
        echo "Payment failed. Please try again.";
    }
}
function processPayment($amount) {
    
    return true; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - CaneLink</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .main-content {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2E7D32;
        }

        p {
            font-size: 18px;
            text-align: center;
            color: #333;
        }

        .payment-option {
            margin: 20px 0;
            text-align: center;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #2E7D32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1B5E20;
        }

        .mpesa-form, .card-form {
            display: none; /* Hidden by default */
            margin-top: 20px;
        }

        .mpesa-form input, .card-form input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .mpesa-form button, .card-form button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Payment</h1>
        <p>Total Amount: KSH <?php echo number_format($total_amount, 2); ?></p>

        <div class="payment-option">
            <button id="mpesa-button">Lipa na M-Pesa</button>
            <button id="card-button">Pay with Card</button>
        </div>

        <div class="mpesa-form" id="mpesa-form">
            <h2>M-Pesa Payment</h2>
            <input type="text" id="name" placeholder="Enter your name" required>
            <input type="text" id="phone" placeholder="+254 xxx xxx xxx" required pattern="^\+254[0-9]{9}$">
            <button id="submit-mpesa">Submit Payment</button>
        </div>

        <div class="card-form" id="card-form">
            <h2>Card Payment</h2>
            <div id="paypal-button-container"></div>
        </div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=ASjOH8N5KwdesEpzpatdXWRmQk7oMhc5wL1EEitLYYoXkXwcXl7KGCi7wcQUKeddyH06Y5nHkgwHyYIF"></script>
    <script>
        document.getElementById('mpesa-button').addEventListener('click', function() {
            document.getElementById('mpesa-form').style.display = 'block';
            document.getElementById('card-form').style.display = 'none';
        });

        document.getElementById('card-button').addEventListener('click', function() {
            document.getElementById('card-form').style.display = 'block';
            document.getElementById('mpesa-form').style.display = 'none';
        });

        //  M-Pesa payment submission
        document.getElementById('submit-mpesa').addEventListener('click', function() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;

            
            alert(`M-Pesa payment initiated for ${name} at ${phone}`);
            
        });

        // PayPal 
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total_amount / 100; ?>' 
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name);
                   
                });
            },
            onError: function(err) {
                console.error(err);
                alert('An error occurred during the transaction. Please try again.');
            }
        }).render('#paypal-button-container'); 
    </script>
</body>
</html>
