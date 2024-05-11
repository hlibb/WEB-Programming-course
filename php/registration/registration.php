<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Accounterstellung</title>
    <link rel="stylesheet" href="../../assets/css/styles.css" />
  </head>
  <body>
    <h1>Accounterstellung</h1>
    <p>Bitte füllen Sie das Formular zur Accounterstellung aus.</p>
    <form method="post" action='registration_process.php'>
      <fieldset>
        <label for="username">Wählen Sie ihren Nutzernamen: <input id="username" name="username" type="text" required /></label>
        <label for="email">Geben Sie ihre Email ein: <input id="email" name="email" type="email" required /></label>
        <label for="new-password">Wählen Sie ein neues Passwort: <input id="new-password" name="new-password" type="password" pattern="[a-z0-9]{8,}" required /></label>
      </fieldset>
      <label for="geschäftsbedingungen">
        <input class="inline" id="Geschäftsbedingungen" type="checkbox" required name="Geschäftsbedingungen" /> Ich habe die <a href="https://www.juraforum.de/lexikon/allgemeine-geschaeftsbedingungen">ABG's</a> gelesen und stimme ihnen zu. <!-- required could be avoided by changing html in browser: simply deleted -> possible to avoid checking AGB's -->
      </label>
      <input type="submit" value="Account erstellen" onclick=""/>
    </form>
  </body>
</html>
