<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Password Reset</h2>

        <p>To reset your password, <a href="{{ url('password/reset/'.$token) }}">click here.</a></p>
        <p>Or point your browser to this address: <br /> {{ url('password/reset/'.$token) }}</p>
        <p>Thank you, <br />
            ~The Admin Team</p>
    </body>
</html>