<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  </head>
  <body>
  <form method="POST" action="menu_logica.php">
  <div class="card mb-3 mt-5 ms-5" style="max-width: 540px;">
  <div class="row g-0">
    <div class="col-md-4">
      <img src="img/small.jpeg" class="img-fluid rounded-start" alt="...">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title">Cafe pequeño</h5>
        <p class="card-text">Descipcion Cafe</p>
        <p class="card-text"><small class="text-body-secondary">$1.50</small></p>
        <input type="hidden" value="Cafe pequeño" name="descripcion">
        <input type="hidden" value="1.50" name="precio">
        <button type="submit" name="submit">Agregar al carrito</button>
      </div>
    </div>
  </div>
</div>
</form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  </body>
</html>