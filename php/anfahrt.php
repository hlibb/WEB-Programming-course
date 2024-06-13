<?php
include_once 'include/logged_in.php';
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>About Us</title>
        <?php include '../php/include/headimport.php' ?>
    </head>
    <body>
    <?php include '../php/include/navimport.php' ?>
    <h3>Unser Firmenstandort</h3>
    <div id="map">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2748.406402797624!2d9.184498915663138!3d48.48184007925196!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4799f187ce597645%3A0xe1be6a5d6e02bb27!2sHochschule%20Reutlingen!5e0!3m2!1sen!2sde!4v1624538434250!5m2!1sen!2sde"
            width="100%"
            height="400"
            style="border:0;"
            allowfullscreen=""
            loading="lazy">
        </iframe>
    </div>
    <?php include '../php/include/footimport.php' ?>
    </body>
    </html>
<?php
