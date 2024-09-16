<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style for centering the loader */
        .spinner-container {
            display: none;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .result-container {
            display: none;
        }
    </style>
</head>
<body>

<?php include "includes/nav.php"; ?>

<div class="container mt-5">
    <div id="spinner" class="spinner-container">
        <div class="text-center">
            <h4>Checking payment status...</h4>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div id="result" class="result-container text-center mt-5">
        <!-- This area will display the payment status -->
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show the spinner initially and hide the result container
document.getElementById('spinner').style.display = 'flex';

let retryCount = 0;
const maxRetries = 5; // We'll check the payment status up to 5 times (every 10 seconds)

// Function to simulate checking payment status via AJAX
function fetchPaymentStatus() {
    const checkoutRequestId = "<?php echo $_GET['CheckoutRequestID'] ?? ''; ?>";
    
    if (!checkoutRequestId) {
        displayResult('Error: No payment ID provided.', 'danger');
        return;
    }

    // Send an AJAX request to check payment status
    fetch(`check_payment_status.php?CheckoutRequestID=${checkoutRequestId}`)
        .then(response => response.json())
        .then(data => {
            // Hide the spinner
            document.getElementById('spinner').style.display = 'none';

            // Handle the payment status based on the API response
            if (data.ResultCode === 0) {
                // Payment completed successfully
                displayResult(`Payment completed successfully! <br> Amount: ${data.Amount} KES`, 'success');
            } else if (data.ResultCode === 1) { 
                // Pending status
                displayResult('Payment is pending. Please complete the payment on your phone.', 'warning');
                retryPaymentStatus(); // Retry after a delay to check the status again
            } else if (data.ResultCode === 1032) {
                // Payment cancelled by the user
                displayResult(
                    'Payment was cancelled. Please try again.', 
                    'danger', 
                    true // Show retry button
                );
            } else {
                // General failure
                displayResult('Payment failed. Please try again.', 'danger');
            }
        })
        .catch(error => {
            document.getElementById('spinner').style.display = 'none';
            displayResult('An error occurred while checking the payment status. Please try again.', 'danger');
        });
}

// Retry payment status after 10 seconds if pending and retry count is below the limit
function retryPaymentStatus() {
    retryCount++;
    if (retryCount < maxRetries) {
        document.getElementById('spinner').style.display = 'flex'; // Show loading again for retry
        setTimeout(fetchPaymentStatus, 10000); // Retry after 10 seconds
    } else {
        displayResult('Payment pending. Please check your M-Pesa for confirmation.', 'warning');
    }
}

// Function to display the result, with an optional retry button
function displayResult(message, type, showRetry = false) {
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    
    if (showRetry) {
        resultDiv.innerHTML += `
            <button class="btn btn-primary mt-3" onclick="retryPayment()">Retry Payment</button>
        `;
    }

    resultDiv.style.display = 'block';  // Show the result container
    document.getElementById('spinner').style.display = 'none'; // Hide the spinner
}

// Function to retry the payment when the retry button is clicked
function retryPayment() {
    // Redirect the user back to the payment form or handle the retry logic here
    window.location.href = 'payment.php';
}

// Initial call to check the payment status
fetchPaymentStatus();
</script>

</body>
</html>
