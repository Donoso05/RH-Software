

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>
<body>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar Decorado</title>
  <!-- Agrega los enlaces a Bootstrap CSS y JS si no los tienes -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <style>
    .navbar-brand {
      color: #fff; /* Cambia el color del texto del navbar-brand */
    }

    .navbar-nav .nav-link {
      color: #fff; /* Cambia el color del texto de los enlaces */
    }

    .navbar-nav .nav-link:hover {
      color: #69B1C4; /* Cambia el color del texto al pasar el mouse */
    }

    .navbar-nav .nav-link.active {
      color: #fff;
      font-weight: bold; /* Cambia el estilo del texto del enlace activo */
    }

    .navbar-toggler-icon {
      background-color: #fff; /* Cambia el color del icono del botón de navegación */
    }

    .navbar-toggler:focus {
      outline: none; /* Quita el borde al enfocar el botón de navegación */
    }

    .navbar-nav .nav-link {
      cursor: pointer; /* Cambia el cursor al pasar sobre los enlaces */
    }

    /* Agrega color a los botones */
    .navbar-nav .nav-link {
      background-color: #343a40; /* Cambia el color de fondo de los botones */
      border-radius: 5px; /* Añade bordes redondeados a los botones */
      padding: 8px 15px; /* Ajusta el espaciado interno de los botones */
      margin-right: 5px; /* Ajusta el margen derecho de los botones */
    }

    .navbar-nav .nav-link:hover {
      background-color: #323131; /* Cambia el color de fondo al pasar el mouse */
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">RECURSOS HUMANOS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
        <a class="nav-link" href="usuario.php">Usuarios</a>
        <a class="nav-link" href="tipos_usuario.php">Roles</a>
        <a class="nav-link" href="tipo_permiso.php">Permisos</a>
        <a class="nav-link" href="tram_permiso.php">Tramite Permisos</a>
        <a class="nav-link" href="tipo_cargo.php">Cargo</a>
        <a class="nav-link" href="solic_prestamo.php">Prestamos</a>
        <a class="nav-link" href="estado.php">Estados</a>
        <a class="nav-link" href="roles.php">Roles</a>
        <a class="nav-link" href="../../controller/cerrarcesion.php">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>

</body>
</html>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>