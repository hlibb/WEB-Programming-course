<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounterstellung</title>
    <link rel="stylesheet" href="../../assets/css/styles.css"/>
</head>
<body>
<h1>Accounterstellung</h1>
<p>Bitte füllen Sie das Formular zur Accounterstellung aus.</p>
<form method="post" action='registration_process.php'>
    <fieldset>
        <label for="username">Wählen Sie ihren Nutzernamen: <input id="username" name="username" type="text" required/></label>
        <label for="email">Geben Sie ihre Email ein: <input id="email" name="email" type="email" required/></label>
        <label for="new-password">Wählen Sie ein neues Passwort: <input id="new-password" name="new-password"
                                                                        type="password" pattern="[a-z0-9]{8,}"
                                                                        required/></label>
        <input type="hidden" id="screen_resolution" name="screen_resolution">
        <input type="hidden" id="operating_system" name="operating_system">
    </fieldset>
    <label for="geschäftsbedingungen">
        <input class="inline" id="Geschäftsbedingungen" type="checkbox" required name="Geschäftsbedingungen"/> Ich habe
        die <a href="https://www.juraforum.de/lexikon/allgemeine-geschaeftsbedingungen">ABG's</a> gelesen und stimme
        ihnen zu.
        <!-- required could be avoided by changing html in browser: simply deleted -> possible to avoid checking AGB's -->
    </label>
    <input type="submit" value="Account erstellen" onclick=""/>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("registrationForm");
        document.getElementById("screen_resolution").value = window.screen.width + "x" + window.screen.height;

        var userAgent = window.navigator.userAgent;
        var os = "Unknown OS";

        if (userAgent.indexOf("Windows NT 10.0") !== -1) os = "Windows 10";
        else if (userAgent.indexOf("Windows NT 11.0") !== -1) os = "Windows 11";
        else if (userAgent.indexOf("Windows NT 6.2") !== -1) os = "Windows 8";
        else if (userAgent.indexOf("Windows NT 6.1") !== -1) os = "Windows 7";
        else if (userAgent.indexOf("Windows NT 6.0") !== -1) os = "Windows Vista";
        else if (userAgent.indexOf("Windows NT 5.1") !== -1) os = "Windows XP";
        else if (userAgent.indexOf("Mac OS X") !== -1) os = "Mac OS X";
        else if (userAgent.indexOf("Linux") !== -1) os = "Linux";
        else if (userAgent.indexOf("Android") !== -1) os = "Android";
        else if (userAgent.indexOf("like Mac") !== -1) os = "iOS";

        document.getElementById("operating_system").value = os;
    });
</script>
</body>
</html>
