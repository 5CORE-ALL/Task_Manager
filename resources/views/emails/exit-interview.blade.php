<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exit Interview Form - 5 Core</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .greeting {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .content {
            font-size: 16px;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-link {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .note {
            font-style: italic;
            color: #666;
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .direct-link {
            font-size: 12px;
            color: #666;
            margin-top: 15px;
            word-break: break-all;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">5 Core</div>
            <h2 style="color: #333; margin: 0;">Exit Interview Form</h2>
        </div>
        
        <div class="greeting">👋 Hi there!</div>
        
        <div class="content">
            <p>Thank you for taking the time to share your thoughts 💬. Your feedback is confidential 🤫 and will help us make 5 Core an even better place to work 🌱.</p>
        </div>
        
        <div class="form-link">
            <a href="{{ $formLink }}" class="btn" target="_blank">Access Exit Interview Form</a>
        </div>
        
        <div class="content">
            <p>Please answer honestly — there are no right or wrong answers.</p>
        </div>

        <div class="direct-link">
            <strong>Direct link (copy and paste if button doesn't work):</strong><br>
            {{ $formLink }}
        </div>
        
        <div class="note">
            <strong>Note:</strong> This form is completely confidential. Your responses will be used solely to improve our workplace environment and processes. If you have any technical issues accessing the form, please contact HR.
        </div>
        
        <div class="footer">
            <p>Best regards,<br>
            <strong>HR Team - 5 Core</strong></p>
            <p style="font-size: 12px; color: #999;">
                This is an automated email. If you did not request this form, please contact HR immediately.
            </p>
        </div>
    </div>
</body>
</html>
