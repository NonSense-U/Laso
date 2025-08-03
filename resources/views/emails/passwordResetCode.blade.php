<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 40px; color: #333;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">

        <h2 style="color: #2c3e50;">Hello {{ $first_name }},</h2>

        <p style="font-size: 16px; margin-top: 20px;">
            We received a request to reset your password. You can set a new password by providing the code below.
        </p>

        <p style="font-size: 16px;">
            If you did not request a password reset, please ignore this message. The reset code will expire shortly for your security.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <!-- <a href="{{ $reset_link ?? 'https://example.com/password/reset' }}" style="background-color: #007bff; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                Reset Your Password
            </a> -->
            <p style="background-color: #007bff; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                {{ $passwordResetCode }}
            </p>
        </div>

        <p style=" font-size: 16px;">
            If you have any questions or need help, our support team is here for you.
        </p>

        <p style="font-size: 16px;">Stay secure and take care!</p>

        <p style="margin-top: 30px; font-size: 16px;">
            Best regards,<br>
            <strong>The Pharmacy Team</strong>
        </p>
    </div>

</body>

</html>