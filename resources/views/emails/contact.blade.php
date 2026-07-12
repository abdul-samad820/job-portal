<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        body        { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper    { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header     { background: #1a56db; padding: 28px 32px; text-align: center; }
        .header h1  { color: #ffffff; margin: 0; font-size: 22px; letter-spacing: .5px; }
        .body       { padding: 32px; }
        .field      { margin-bottom: 20px; }
        .label      { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .value      { font-size: 15px; color: #111827; background: #f9fafb; border-left: 3px solid #1a56db; padding: 10px 14px; border-radius: 4px; word-break: break-word; }
        .message-box{ white-space: pre-wrap; line-height: 1.7; }
        .footer     { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center; font-size: 12px; color: #9ca3af; }
        .reply-note { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 12px 16px; font-size: 13px; color: #1e40af; margin-top: 24px; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>📬 New Contact Form Message</h1>
    </div>

    <div class="body">

        <div class="field">
            <div class="label">From</div>
            <div class="value">{{ $senderName }}</div>
        </div>

        <div class="field">
            <div class="label">Email</div>
            <div class="value">{{ $senderEmail }}</div>
        </div>

        <div class="field">
            <div class="label">Subject</div>
            <div class="value">{{ $contactSubject }}</div>
        </div>

        <div class="field">
            <div class="label">Message</div>
            <div class="value message-box">{{ $userMessage }}</div>
        </div>

        <div class="reply-note">
            💡 <strong>Tip:</strong> Reply directly to this email — it will go straight to
            <strong>{{ $senderEmail }}</strong>.
        </div>

    </div>

    <div class="footer">
        This message was submitted via the Job Hub contact form on {{ now()->format('d M Y, h:i A') }}.
    </div>

</div>
</body>
</html>