<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .navbar-brand {
            color: #fff;
        }
        .navbar-nav .nav-link {
            color: #fff;
        }
        .navbar-nav .nav-link:hover {
            color: #69B1C4;
        }
        .navbar-nav .nav-link.active {
            color: #fff;
            font-weight: bold;
        }
        .navbar-toggler-icon {
            background-color: #fff;
        }
        .navbar-toggler:focus {
            outline: none;
        }
        .navbar-nav .nav-link {
            cursor: pointer;
            background-color: #343a40;
            border-radius: 5px;
            padding: 8px 15px;
            margin-right: 5px;
        }
        .navbar-nav .nav-link:hover {
            background-color: #323131;
        }
    </style>
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
                <a class="nav-link" href="tram_permiso.php">Tramite Permisos</a>
                <a href="credito.php" class="nav-link">Prestamos</a>
                <a href="liquidaciones.php" class="nav-link">Liquidaciones</a>
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
