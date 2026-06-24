<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #1a5f2a; color: #fff; padding: 20px 30px; text-align: center; }
        .header h2 { margin: 0; font-size: 20px; }
        .body { padding: 30px; }
        .footer { background: #f8f9fa; padding: 15px 30px; text-align: center; font-size: 12px; color: #888; }
        .btn { display: inline-block; padding: 12px 24px; background: #1a5f2a; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 15px; }
        .highlight { background: #fffbeb; padding: 15px; border-left: 4px solid #f59e0b; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $school?->name ?? 'School Management System' }}</h2>
        </div>
        <div class="body">
            {!! $body !!}
        </div>
        <div class="footer">
            <p>This email was sent from the school management system.</p>
            <p>If you have questions, contact the bursary at {{ $school?->email ?? 'bursary@school.edu' }}</p>
        </div>
    </div>
</body>
</html>
