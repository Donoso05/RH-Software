<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;
date_default_timezone_set('America/Bogota');

// Verificar si el usuario existe
$sql_check_user = "SELECT COUNT(*) FROM usuario WHERE id_usuario = :id_usuario";
$stmt_check_user = $con->prepare($sql_check_user);
$stmt_check_user->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_check_user->execute();
$user_exists = $stmt_check_user->fetchColumn();

if ($user_exists == 0) {
    die("El usuario con id_usuario $id_usuario no existe en la tabla usuario.");
}

$sql = "SELECT d.*, u.nombre
        FROM detalle d 
        INNER JOIN usuario u ON d.id_usuario = u.id_usuario
        WHERE d.id_usuario = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

$nominas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Nómina Mensual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/detalles.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <h3 class="text-center text-secondary my-4">Historial de Nominas</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Salario Total</th>   
                        <th scope="col">Mes</th>
                        <th scope="col">Fecha de Liquidación</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nominas)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay registros de nómina para este usuario.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($nominas as $nomina): ?>
                            <tr>
                                <td><?php echo $nomina["nombre"]; ?></td>
                                <td><?php echo number_format($nomina["salario_total"], 0, ',', '.'); ?></td> 
                                <td><?php echo $nomina["mes"]?></td>
                                <td><?php echo $nomina["fecha_li"]; ?></td>
                                <td>
                                    <a href="ver_detalles_nomina_mensual.php?id_nomina=<?php echo $nomina['id_nomina']; ?>" class="btn btn-primary btn-sm">Ver Detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Y3tGSOE2mDkGjm9PYf47FZ5gmpG5" crossorigin="anonymous"></script>
</body>
</html>