<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pharmacy Staff Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 40px; color: #333;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">

        <h2 style="color: #2c3e50;">Hello {{ $first_name }},</h2>

        <p style="font-size: 16px; margin-top: 20px;">
            We are pleased to invite you to join our pharmacy team. You’ve been selected to become part of a group of professionals committed to excellence in healthcare service.
        </p>

        <p style="font-size: 16px;">
            You may now create your account using this email address. Please follow the registration process to activate your access.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $registration_link ?? 'https://youtu.be/mcYLzu_1cNc?si=0-5fisHpiVAqogVF' }}" style="background-color: #007bff; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                Create Your Account
            </a>
        </div>

        <p style="font-size: 16px;">
            If you have any questions or need assistance, feel free to contact our support team.
        </p>

        <p style="font-size: 16px;">We look forward to having you on board!</p>

        <p style="margin-top: 30px; font-size: 16px;">
            Best regards,<br>
            <strong>The Pharmacy Team</strong>
        </p>
    </div>

</body>
</html>
