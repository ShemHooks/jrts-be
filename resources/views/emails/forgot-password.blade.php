<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Temporary Password</title>
</head>

<body>
    <p>Hello {{ $name }},</p>

    <p>This is your temporary password. Use this to login on your account.</p>

    <p><strong>Temporary Password:</strong></p>
    <h2>{{ $temporary_password }}</h2>

    <p>Please log in and change your password again immediately to keep your account secure.</p>

    <br>

    <p>Thank you,<br>
        File Tracking System Administrator</p>
</body>

</html>
s