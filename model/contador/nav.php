<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><img src="img/rh.png" alt=""></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
        <div class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsuarios" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Usuarios
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsuarios">
            <li><a class="dropdown-item" href="usuario.php">Usuarios</a></li>
            <li><a class="dropdown-item" href="tram_permiso.php">Tram. Permiso</a></li>
            <li><a class="dropdown-item" href="credito.php">Prestamos</a></li>
            
          </ul>
        </div>
        <div class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRoles" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Procesos
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownRoles">
            <li><a class="dropdown-item" href="ver_liquidacion.php">Liquidaciones</a></li>
          </ul>
        </div>
      </div>
      <div class="navbar-nav ms-auto">
        <a class="nav-link" href="../../controller/cerrarcesion.php">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
