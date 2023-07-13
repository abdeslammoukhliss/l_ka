<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email verification</title>
    <!-- <link rel="stylesheet" href="{{asset('css/first.css')}}"> -->
</head>
<body>
    <h1>Email Verification Mail</h1>
        Please verify your email with bellow link: 
    <a href="{{ route('user.verify', [$id ,$token]) }}">Verify Email</a>
</body>
</html>