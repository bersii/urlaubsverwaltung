<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Login">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="favicon.png" type="image/png">
    <link href="./css/style.css" rel="stylesheet" media="all">
    <title>Urlaubsverwaltung - BFW Hamburg</title>
</head>
<body>

<div class="header">
    <div><h1>Urlaubsverwaltung</h1></div>
    <div><img src="./img/logo.svg" class="bfw-logo"></div>
</div>

<div id="login-form-div">
    <form id="login-form" action="index.php" method="POST">
        <input name="cmd" type="hidden" value="LoginCheck">
        <p>
            <label>Nutzername:</label>
        </p>
        <p>
            <input type="text" name="username" class="text-input" value="<?= $this->username ?>" autofocus>
        </p>
        <br>
        <p>
            <label>Passwort:</label>
        </p>
        <p>
            <input type="password" name="password" class="text-input">
        </p>
        <br>
        <p>
            <input type="submit" name="login" class="button" value="Anmelden">
        </p>
    </form>
</div>
<noscript>
<div class="meldung">
    <br><br><br>Um die Urlaubsverwaltung korrekt nutzen Sie können, aktivieren Sie bitte JavaScript.<br>
</div>
</noscript>
<div class="meldung"><br><?= $this->meldung ?></div>
<script src="js/main.js"></script>

</body>
</html>