<!DOCTYPE html>
<html>

<head>
    <title>Application Status Update</title>
</head>

<body style="font-family: Arial, sans-serif;">

    <h2>Hello {{ $user->name }},</h2>

    <p>Your application for the position:</p>

    <h3>{{ $job->title }}</h3>

    <p>Status:
        <strong
            style="color:
            @if ($status == 'hired') green
            @elseif($status == 'shortlisted') orange
            @else red @endif
        ">
            {{ ucfirst($status) }}
        </strong>
    </p>

    <br>

    <p>Thank you for applying.</p>

    <p>Regards,<br>Job Hub Team</p>

</body>

</html>
