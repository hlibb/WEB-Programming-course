<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jetzt bewerben</title>
    <?php include '../php/include/headimport.php'; ?>
    <style>
        .apply-container {
            background-color: #282847;
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            color: #f5f6f7;
        }
        .apply-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .apply-form {
            display: flex;
            flex-direction: column;
        }
        .apply-form label {
            margin: 10px 0 5px;
        }
        .apply-form input,
        .apply-form textarea {
            background-color: #0a0a23;
            border: 1px solid #0a0a23;
            color: #ffffff;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .apply-form button {
            background-color: #3b3b4f;
            border: none;
            border-radius: 5px;
            color: #ffffff;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .apply-form button:hover {
            background-color: #575768;
        }
    </style>
</head>
<body>
<?php include "include/navimport.php"; ?>

<div class="apply-container">
    <h2 class="apply-header">Bewerbung für <?php echo htmlspecialchars($_GET['job']); ?></h2>
    <form class="apply-form" action="send_application.php" method="post">
        <input type="hidden" name="job" value="<?php echo htmlspecialchars($_GET['job']); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="cover_letter">Anschreiben:</label>
        <textarea id="cover_letter" name="cover_letter" rows="6" required></textarea>

        <button type="submit">Bewerbung absenden</button>
    </form>
</div>

<?php include "include/footimport.php"; ?>
</body>
</html>
