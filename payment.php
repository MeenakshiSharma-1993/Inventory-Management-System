<?php
// Show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include Stripe SDK
require_once 'stripe-php/init.php';

// Set secret key
\Stripe\Stripe::setApiKey('sk_test_51RfdJ04EbkDFKI7ruhsvcqPDGsZILJdTzJiDMqXmuwNvVRlPH2PexjzkPrjlLEnHIz0OCJcXV5Y4GWM7zsxDVLur00jWEPiw6v');

// Current URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"], '?');
$successUrl = $baseUrl . '?status=success';
$cancelUrl  = $baseUrl . '?status=cancel';

// Read amount from GET param (₹ amount in paise)
$amount = isset($_GET['totalAmount']) ? (int)$_GET['totalAmount'] : 30000;

// Handle POST request to create session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET['status'])) {
  try {
    $session = \Stripe\Checkout\Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [[
        'price_data' => [
          'currency' => 'inr',
          'unit_amount' => $amount,
          'product_data' => ['name' => 'Demo Product'],
        ],
        'quantity' => 1,
      ]],
      'mode' => 'payment',
      'success_url' => $successUrl,
      'cancel_url'  => $cancelUrl,
    ]);

    header('Content-Type: application/json');
    echo json_encode(['sessionId' => $session->id]);
    exit;
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
  }
}

// Handle redirect after payment
if (isset($_GET['status'])) {
  if ($_GET['status'] === 'success') {
    echo "<script>alert('✅ Payment Successful!'); window.location='payment.php';</script>";
  } elseif ($_GET['status'] === 'cancel') {
    echo "<script>alert('❌ Payment Cancelled.'); window.location='payment.php';</script>";
  }
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stripe Payment</title>
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
  <h2>Pay ₹<?= $amount ?> with Stripe</h2>
  <button id="pay-button">Pay with Card</button>

  <script>
    const stripe = Stripe("pk_test_51RfdJ04EbkDFKI7rF0WBwoIzC1kCqQjdklTVhq1GO81jaKEA8DCGhnI6IpuytTcFY02qW7NOTi9kKvGnE2lGRF1u00tpd9l2AN");

    document.getElementById("pay-button").onclick = handlePayment;

    async function handlePayment() {
      try {
        const response = await fetch("payment.php?totalAmount=<?= $amount ?>", {
          method: "POST"
        });
        const result = await response.json();

        if (result.sessionId) {
          await stripe.redirectToCheckout({ sessionId: result.sessionId });
        } else {
          alert("Error: " + (result.error || "Something went wrong"));
        }
      } catch (error) {
        alert("Request failed: " + error.message);
      }
    }
  </script>
</body>
</html>
