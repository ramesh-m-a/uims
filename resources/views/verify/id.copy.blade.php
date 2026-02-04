<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RGUHS ID Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 40px 20px;
        }

        .card {
            max-width: 420px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,.1);
            padding: 24px;
            text-align: center;
        }

        .logo {
            width: 70px;
            margin-bottom: 12px;
        }

        .photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            margin: 16px auto;
        }

        .verified {
            color: #0a8a3a;
            font-weight: bold;
            font-size: 16px;
            margin: 12px 0;
        }

        .label {
            color: #777;
            font-size: 13px;
        }

        .value {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .footer {
            font-size: 12px;
            color: #aaa;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="card">

    <img src="{{ asset('images/RGUHS-logo-AA.png') }}" class="logo">

    <h2>Official ID Verification</h2>

    <div class="verified">✔ VERIFIED</div>

    <img src="{{ asset('storage/' . ltrim($user->photo_path, '/')) }}" class="photo">

    <div class="label">Name</div>
    <div class="value">{{ $user->name }}</div>

    <div class="label">TIN</div>
    <div class="value">{{ $payload->tin }}</div>

    <div class="label">Issued By</div>
    <div class="value">Rajiv Gandhi University of Health Sciences</div>

    <div class="label">Valid Until</div>
    <div class="value">{{ date('d M Y', $payload->exp) }}</div>

    <div class="footer">
        This verification was generated directly from the RGUHS official system.
    </div>
</div>

</body>
</html>
