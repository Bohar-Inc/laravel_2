<!DOCTYPE html>
<html>
<head>
    <title>User PDF</title>
    <style>
        body { font-family: sans-serif; }
        .header { font-size: 20px; font-weight: bold; }
        .content { margin-top: 10px; }
    </style>
</head>
<body>
<div class="header">User Details</div>
<div class="content">
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
</div>
</body>
</html>
