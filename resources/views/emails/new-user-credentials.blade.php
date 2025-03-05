<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Your Account Credentials</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f84525;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: #f9f9f9;
        }

        .credentials {
            background: #ffffff;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .footer {
            font-size: 12px;
            text-align: center;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Platform</h1>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>Your account has been created successfully. Below are your login credentials:</p>

            <div class="credentials">
                <p><strong>Username:</strong> {{ $username }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
            </div>

            <p>For security reasons, we recommend changing your password after logging in for the first time.</p>

            <p>If you have any questions, please feel free to contact our support team.</p>

            <p>Thank you,<br>The Support Team</p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>

</html>