<?php
// Include Composer's autoload file
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mpesaConsumerKey = $_ENV['MPESA_CONSUMER_KEY'];
$mpesaConsumerSecret = $_ENV['MPESA_CONSUMER_SECRET'];
$businessShortCode = $_ENV['BUSINESS_SHORT_CODE'];
$lipaNaMpesaOnlinePasskey = $_ENV['LIPA_NA_MPESA_ONLINE_PASSKEY'];
$callbackUrl = $_ENV['CALLBACK_URL'];

// Function to get the access token
function getAccessToken() {
    global $mpesaConsumerKey, $mpesaConsumerSecret;

    $credentials = base64_encode($mpesaConsumerKey . ':' . $mpesaConsumerSecret);

    $ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response);
    return $response->access_token;
}

// Function to initiate the M-Pesa payment
function initiatePayment($phoneNumber, $amount) {
    global $businessShortCode, $lipaNaMpesaOnlinePasskey, $callbackUrl;

    $accessToken = getAccessToken();
    $timestamp = date('YmdHis');
    $password = base64_encode($businessShortCode . $lipaNaMpesaOnlinePasskey . $timestamp);

    $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);

    $data = [
        "BusinessShortCode" => $businessShortCode,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phoneNumber,
        "PartyB" => $businessShortCode,
        "PhoneNumber" => $phoneNumber,
        "CallBackURL" => $callbackUrl,
        "AccountReference" => "Maithy-a",
        "TransactionDesc" => "Payment of services"
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    // Assuming response contains MerchantRequestID or CheckoutRequestID for tracking the payment status
    if (isset($responseData['CheckoutRequestID'])) {
        header("Location: result.php?CheckoutRequestID=" . $responseData['CheckoutRequestID']);
        exit;
    } else {
        echo "Error initiating payment.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneNumber = $_POST['phoneNumber'];
    $amount = $_POST['amount'];

    initiatePayment($phoneNumber, $amount);
}
?>
