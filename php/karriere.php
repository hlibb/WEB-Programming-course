<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Karriere</title>
    <?php include '../php/include/headimport.php'; ?>
    <style>
        .career-container {
            background-color: #282847;
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            color: #f5f6f7;
        }
        .career-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .job-listing {
            background-color: #3b3b4f;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #f5f6f7;
        }
        .job-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .job-location {
            font-style: italic;
        }
        .job-description {
            margin-top: 10px;
        }
        .apply-button {
            background-color: #3b3b4f;
            border: none;
            border-radius: 5px;
            color: #ffffff;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .apply-button:hover {
            background-color: #575768;
        }
    </style>
</head>
<body>
<?php include "include/navimport.php"; ?>

<div class="career-container">
    <h2 class="career-header">Karriere bei uns</h2>
    <div class="job-listing">
        <div class="job-title">Softwareentwickler (m/w/d)</div>
        <div class="job-location">Berlin, Deutschland</div>
        <div class="job-description">
            <p>Wir suchen einen erfahrenen Softwareentwickler, der unser Team in Berlin verstärkt. Zu Ihren Aufgaben gehören die Entwicklung und Wartung von Webanwendungen, die Zusammenarbeit mit unseren Design- und Produktteams und die Verbesserung unserer bestehenden Systeme.</p>
        </div>
        <button class="apply-button" onclick="location.href='bewerbung.php?job=softwareentwickler'">Jetzt bewerben</button>
    </div>
    <div class="job-listing">
        <div class="job-title">Marketing Manager (m/w/d)</div>
        <div class="job-location">München, Deutschland</div>
        <div class="job-description">
            <p>Als Marketing Manager sind Sie verantwortlich für die Entwicklung und Umsetzung unserer Marketingstrategien. Sie arbeiten eng mit unserem Vertriebsteam zusammen, um unsere Markenbekanntheit zu steigern und neue Kunden zu gewinnen.</p>
        </div>
        <button class="apply-button" onclick="location.href='bewerbung.php?job=marketingmanager'">Jetzt bewerben</button>
    </div>
    <div class="job-listing">
        <div class="job-title">Produktmanager (m/w/d)</div>
        <div class="job-location">Hamburg, Deutschland</div>
        <div class="job-description">
            <p>Wir suchen einen dynamischen Produktmanager, der unsere Produktentwicklung leitet. Sie sind verantwortlich für die Planung, Umsetzung und Optimierung unserer Produktstrategien und arbeiten eng mit verschiedenen Abteilungen zusammen.</p>
        </div>
        <button class="apply-button" onclick="location.href='bewerbung.php?job=produktmanager'">Jetzt bewerben</button>
    </div>
</div>

<?php include "include/footimport.php"; ?>
</body>
</html>
