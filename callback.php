<?php
// Log the callback response for debugging purposes
$callbackData = file_get_contents('php://input');
$logFile = 'mpesa_callback.log';
$log = fopen($logFile, 'a');
fwrite($log, $callbackData . "\n");
fclose($log);

// Convert the JSON callback data to an associative array
$callbackData = json_decode($callbackData, true);

// Process the response based on the ResultCode from M-Pesa
if ($callbackData && isset($callbackData['Body']['stkCallback']['ResultCode'])) {
    $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
    $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];
    $merchantRequestID = $callbackData['Body']['stkCallback']['MerchantRequestID'];
    $checkoutRequestID = $callbackData['Body']['stkCallback']['CheckoutRequestID'];

    // Check if the payment was successful
    if ($resultCode == 0) {
        // Payment successful, retrieve the transaction details
        $amount = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $mpesaReceiptNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        $transactionDate = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
        $phoneNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

        // Insert the payment details into your database
        // (Assume you have a database connection already set up)
        // Example of inserting into a payments table:
        $conn = new mysqli('localhost', 'root', '', 'mpesa_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO payments (merchant_request_id, checkout_request_id, amount, mpesa_receipt_number, transaction_date, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsss", $merchantRequestID, $checkoutRequestID, $amount, $mpesaReceiptNumber, $transactionDate, $phoneNumber);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // You can also send a response back to Safaricom confirming receipt of the callback
        echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    } else {
        // Payment failed or was cancelled
        // Log or handle the failure/cancellation as needed
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Payment Failed or Cancelled']);
    }
} else {
    // Invalid callback or no result code
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid Request']);
}
