<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounterstellung</title>
    <?php include '../php/include/headimport.php' ?>
</head>
<body>
<h1>Accounterstellung</h1>
<p>Bitte füllen Sie das Formular zur Accounterstellung aus.</p>
<?php if (isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>
<form id="registrationForm" method="post" action='registration_process.php' onsubmit="return validateForm(event)">
    <fieldset>
        <label for="vorname">Vorname:
            <input id="name" name="name" type="text" required/>
        </label>
        <label for="nachname">Nachname:
            <input id="surname" name="surname" type="text" required/>
        </label>
        <label for="username">Wählen Sie ihren Nutzernamen:
            <input id="username" name="username" type="text" required/>
        </label>
        <span id="usernameFeedback" style="color: red; display: none;">Username already exists</span>
        <label for="email">Geben Sie ihre Email ein:
            <input id="email" name="email" type="email" required/>
        </label>
        <span id="emailFeedback" style="color: red; display: none;">Email already exists</span>
        <label for="password">Wählen Sie ein neues Passwort:
            <input id="password" name="password" type="password" required/>
        </label>
        <input type="hidden" id="screen_resolution" name="screen_resolution">
        <input type="hidden" id="operating_system" name="operating_system">
        <input type="hidden" id="hashed_password" name="hashed_password">
    </fieldset>
    <label for="geschäftsbedingungen">
        <input class="inline" id="Geschäftsbedingungen" type="checkbox" required name="Geschäftsbedingungen"/>
        Ich habe die <a href="https://www.juraforum.de/lexikon/allgemeine-geschaeftsbedingungen">ABG's</a> gelesen und stimme ihnen zu.
    </label>
    <input type="submit" value="Account erstellen"/>
</form>

<script>
    async function hashPassword(password) {
        const msgUint8 = new TextEncoder().encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-512', msgUint8);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        return hashHex;
    }

    async function validateForm(event) {
        event.preventDefault(); // Stop form submission
        const password = document.getElementById('password').value;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{9,}$/;
        if (!passwordRegex.test(password)) {
            alert('Kennwort muss mindestens 9 Zeichen lang sein und einen Großbuchstaben, Kleinbuchstaben und eine Zahl enthalten.');
            return false;
        }

        // Hash the password before submitting
        const hashedPassword = await hashPassword(password);
        document.getElementById('hashed_password').value = hashedPassword;
        document.getElementById('password').value = '';

        // Resume form submission
        event.target.submit();
        return true;
    }

    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("registrationForm");
        var usernameInput = document.getElementById("username");
        var usernameFeedback = document.getElementById("usernameFeedback");
        var emailInput = document.getElementById("email");
        var emailFeedback = document.getElementById("emailFeedback");

        usernameInput.addEventListener("blur", function () {
            var username = usernameInput.value.trim();
            if (username) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "username_check.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.exists) {
                                usernameFeedback.style.display = "inline";
                            } else {
                                usernameFeedback.style.display = "none";
                            }
                        } catch (e) {
                            console.error("Error parsing JSON response: ", e);
                            console.error("Response received: ", xhr.responseText);
                        }
                    } else {
                        console.error('Error with request:', xhr.statusText);
                    }
                };
                xhr.onerror = function () {
                    console.error('Request error');
                };
                xhr.send("username=" + encodeURIComponent(username));
            }
        });

        emailInput.addEventListener("blur", function () {
            var email = emailInput.value.trim();
            if (email) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "email_check.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.exists) {
                                emailFeedback.style.display = "inline";
                            } else {
                                emailFeedback.style.display = "none";
                            }
                        } catch (e) {
                            console.error("Error parsing JSON response: ", e);
                            console.error("Response received: ", xhr.responseText);
                        }
                    } else {
                        console.error('Error with request:', xhr.statusText);
                    }
                };
                xhr.onerror = function () {
                    console.error('Request error');
                };
                xhr.send("email=" + encodeURIComponent(email));
            }
        });

        form.addEventListener("submit", async function (event) {
            document.getElementById("screen_resolution").value = window.screen.width + "x" + window.screen.height;

            var userAgent = window.navigator.userAgent;
            var os = "Unknown OS";

            if (userAgent.indexOf("Windows NT 10.0") !== -1) os = "Windows 11";
            else if (userAgent.indexOf("Windows NT 10.0") !== -1) os = "Windows 10";
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
    });
</script>
</body>
</html>
