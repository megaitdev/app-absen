<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Verification Code</title>
    </head>

    <body>
        <h2>Hello, {{ $username }}!</h2>
        <p>You have requested a verification code. Here is your code:</p>

        <h1 style="color: #000000;">{{ $code }}</h1>

        <p>This code is valid for the next 5 minutes. Please do not share this code with anyone.</p>

        <p>Thank you for using our service!</p>
    </body>

</html>
