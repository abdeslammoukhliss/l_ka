<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password reset</title>
    <!-- <link rel="stylesheet" href="{{asset('css/first.css')}}"> -->
</head>
<body>
    <h1>Reset password</h1>
        Please reset your password by clicking on the link bellow: 
    <a href="{{ route('reset_form', [$token]) }}">Reset Password</a>
</body>
</html>