<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hi {{ $name }},
    <br>
    Gracias por crear una cuenta en {{ env('APP_NAME', 'HyperCloud') }}. ¡No olvides completar tu registro!
    <br>
    Por favor, haz click en el link inferior o cópialo en la barra de direcciones para confirmar tu corre electrónico:
    <br>

    <a href="{{ url('user/verify', $verificationCode)}}">Confirmar correo electrónico </a>

    <br/>
</div>

</body>
</html>