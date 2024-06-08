<!doctype html>
<html lang="en">
  <head>
    <?php
      include "../php/include/headimport.php"
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Herzlich Willkommen!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css"> 
  </head>
  <body>
    <?php
      include '../php/include/navimport.php'
    ?>
    <div class="container">
        <div id="carouselExample" class="carousel slide">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <a href="../php/products/products.php?id=1"><img src="https://images.unsplash.com/photo-1716583731424-45c32c2ad63b?q=80&w=2938&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="d-block w-100" alt="1"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=2"><img src="https://images.unsplash.com/photo-1716583731424-45c32c2ad63b?q=80&w=2938&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="d-block w-100" alt="2"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=3"><img src="https://source.unsplash.com/CC_kzFrwqiA/1600x900" class="d-block w-100" alt="3"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=4"><img src="https://source.unsplash.com/B6yDtYs2IgY/1600x900" class="d-block w-100" alt="4"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=5"><img src="https://source.unsplash.com/BBQ15BncPCc/1600x900" class="d-block w-100" alt="5"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=6"><img src="https://source.unsplash.com/QdRnZlzYJPA/1600x900" class="d-block w-100" alt="6"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=7"><img src="https://source.unsplash.com/ud1pIqYi7Oc/1600x900" class="d-block w-100" alt="7"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=8"><img src="https://source.unsplash.com/y02jEX_B0O0/1600x900" class="d-block w-100" alt="8"></a>
              </div>
              <div class="carousel-item">
                <a href="../php/products/products.php?id=9"><img src="https://source.unsplash.com/SXn-fWj0Ht4/1600x900" class="d-block w-100" alt="9"></a>
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php  
    include '../php/include/footimport.php'
?>
  </body>
</html>
