
# M-Pesa STK Push Integration
Welcome to the M-Pesa STK Push Integration repository! This project demonstrates how to implement the M-Pesa STK (Sim Tool Kit) Push API for initiating payments directly from a user's mobile phone. This integration allows businesses to facilitate secure and convenient payment transactions via M-Pesa, one of the most popular mobile money transfer services.
This repository contains a PHP implementation of Safaricom's M-Pesa STK Push API, which allows users to make payments via M-Pesa directly from your web application. The repository also integrates the `.env` file to securely manage sensitive information like API keys, making the setup more secure and adaptable.

## Requirements

- PHP 7.3 or later
- Composer
- Internet connection for NGROK (for testing the callback functionality)
- Safaricom developer account (to access the M-Pesa API)

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/Maithy-a/Mpesastkpush.git
cd Mpesastkpush
```

### Step 2: Install Dependencies

Install the required dependencies using Composer. Run the following command in the root of your project:

```bash
composer require safaricom/mpesa
```

This will install the required `safaricom/mpesa` package for M-Pesa integration.

### Step 3: Setup `.env` File

After installing the package, create a `.env` file in the root of your project to store your sensitive credentials. The structure should be as follows:

```bash
# .env
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
BUSINESS_SHORT_CODE=your_business_short_code
LIPA_NA_MPESA_ONLINE_PASSKEY=your_mpesa_online_passkey
CALLBACK_URL=https://your-ngrok-url.ngrok.io/callback.php
```

- **MPESA_CONSUMER_KEY**: Your M-Pesa consumer key from the Safaricom developer portal.
- **MPESA_CONSUMER_SECRET**: Your M-Pesa consumer secret from the Safaricom developer portal.
- **BUSINESS_SHORT_CODE**: The Paybill or Till Number provided by Safaricom.
- **LIPA_NA_MPESA_ONLINE_PASSKEY**: The passkey for your M-Pesa account.
- **CALLBACK_URL**: This URL will be triggered by Safaricom once a payment is processed. For testing, you can use NGROK to create a secure tunnel to your local machine.

### Step 4: Ngrok Setup for Callback URL

To test the M-Pesa callback functionality, you can use NGROK to expose your local server to the internet. Follow the steps below:

1. **Install NGROK**: Download and install NGROK from [ngrok.com](https://ngrok.com/).
2. **Run NGROK**: In your terminal, run the following command to create a tunnel:

   ```bash
   ngrok http 80
   ```

   This will generate a URL like `https://your-ngrok-url.ngrok.io`. Replace `CALLBACK_URL` in your `.env` file with this URL followed by the callback route (`/callback.php`).

   Example:

   ``
   CALLBACK_URL=https://your-ngrok-url.ngrok.io/callback.php
   ``

### Step 5: Run the Application

You can now start your PHP server and test the integration. Ensure that you have your NGROK tunnel running for the callback URL to work.

```bash
php -S localhost:8000
```

Open your browser and navigate to `http://localhost:8000` to initiate the payment process.

## How It Works

1. The user enters their phone number and the amount to pay.
2. When they click "Pay", the M-Pesa STK push API is triggered, sending a prompt to the user's phone to approve the payment.
3. Once the user approves, the M-Pesa API sends the payment status back to the `CALLBACK_URL`, which you can view using NGROK.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

Enjoy using this M-Pesa STK Push integration! If you encounter any issues, please reach out via the repository's issue tracker.
