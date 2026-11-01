<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset OTP - StudentHub</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .email-container {
            max-width: 540px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 32px 28px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand {
            font-size: 22px;
            font-weight: 700;
            color: #0d9488;
            letter-spacing: -0.5px;
        }
        .otp-box {
            background: #f0fdfa;
            border: 2px dashed #0d9488;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #0f766e;
            font-family: 'Courier New', Courier, monospace;
        }
        .footer {
            margin-top: 28px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="brand">🎓 StudentHub</div>
            <h2 style="margin: 12px 0 6px; font-size: 19px; color: #0f172a;">Password Reset Verification Code</h2>
            <p style="margin: 0; font-size: 13.5px; color: #64748b;">Academic Registry &amp; Information System</p>
        </div>

        <p style="font-size: 14.5px; line-height: 1.6;">Hello <strong>{{ $userName }}</strong>,</p>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            You requested a password reset for your StudentHub account. Use the following 6-digit verification code (OTP) to complete your password reset:
        </p>

        <div class="otp-box">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #0d9488; margin-bottom: 6px; font-weight: 600;">Verification Code (OTP)</div>
            <div class="otp-code">{{ $otp }}</div>
            <div style="font-size: 12px; color: #0f766e; margin-top: 6px;">Valid for {{ $expiresInMinutes }} minutes</div>
        </div>

        <p style="font-size: 13px; line-height: 1.5; color: #64748b;">
            If you did not request a password reset, please ignore this email or contact your system administrator. Your password will remain unchanged.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} StudentHub Academic Information System. All rights reserved.
        </div>
    </div>
</body>
</html>

