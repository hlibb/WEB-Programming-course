//Nummer holen
function fetchOnlineUsers() {
    // XMLHTTPReq
    var xhr = new XMLHttpRequest();

    // Script zuordnen
    xhr.open('GET', 'fetch_online_users.php', true);

    // Erfolgreichen erhalt bestätigen
    xhr.onload = function() {
        if (xhr.status == 200) {
            // Nutzer updaten
            document.getElementById('online-users-count').innerText = xhr.responseText;
        }
    };

    // Serveranfrage stellen
    xhr.send();
}

// Nutzeranzahl abfragen zu beginn
fetchOnlineUsers();
setInterval(fetchOnlineUsers, 5000); // Anschießend alle x Millisekunden
