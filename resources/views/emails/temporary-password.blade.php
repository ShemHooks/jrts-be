<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Temporary Password</title>
</head>

<body>
    <p>Hello {{ $name }},</p>

    <p>Your account has been created by the System Administrator.</p>

    <p><strong>Temporary Password:</strong></p>
    <h2>{{ $temp_password }}</h2>

    <p>Please log in and change your password immediately to keep your account secure.</p>

    <br>

    <p>Thank you,<br>
        File Tracking System Administrator</p>
</body>

</html>