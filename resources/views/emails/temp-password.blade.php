<p>Hello {{ $user->name }},</p>

<p>Your account has been created in the UIMS system.</p>

<p><strong>Login Details:</strong></p>

<ul>
    <li>Email: {{ $user->email }}</li>
    <li>Temporary Password: {{ $tempPassword }}</li>
</ul>

<p>
    Login URL:
    <a href="{{ url('/login') }}">{{ url('/login') }}</a>
</p>

<p>
    Please login and change your password immediately.
</p>

<p>Regards,<br>UIMS Team</p>
