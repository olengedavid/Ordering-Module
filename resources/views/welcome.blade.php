<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome to IPOS</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: #fff;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .header {
                text-align: center;
                padding: 20px 0;
                border-bottom: 2px solid #2263AB;
            }
            .content {
                padding: 20px 0;
            }
            .credentials {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                padding-top: 20px;
                color: #666;
                font-size: 0.9em;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #2263AB;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="{{ asset('images/logo.png') }}" alt="IPOS Logo" height="50">
                <h1>Welcome to IPOS</h1>
            </div>
            
            <div class="content">
                <p>Hello {{ $name }},</p>
                
                <p>Welcome to IPOS! Your account has been successfully created. Below are your login credentials:</p>
                
                <div class="credentials">
                    <p><strong>Email:</strong> {{ $email }}</p>
                    <p><strong>Password:</strong> {{ $password }}</p>
                </div>
                
                <p>For security reasons, we recommend changing your password after your first login.</p>
                
                <a href="{{ config('app.url') }}/login" class="button">Login to IPOS</a>
                
                <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            </div>
            
            <div class="footer">
                <p>© {{ date('Y') }} IPOS. All rights reserved.</p>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
</html>
