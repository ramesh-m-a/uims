<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Failed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff3f3;
            padding: 50px;
            text-align: center;
        }
        .box {
            background: #fff;
            border-radius: 14px;
            padding: 30px;
            max-width: 420px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,.1);
        }
        .error { color: #c00000; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Verification Failed</h2>
    <p class="error">{{ $reason }}</p>
    <p>This QR code is not valid or has been tampered.</p>
</div>

</body>
</html>
