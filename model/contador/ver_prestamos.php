<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
$con = $db->conectar();

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta SQL para obtener los datos de la tabla solic_prestamo
$sql = "SELECT sp.id_usuario, sp.monto_solicitado, sp.cant_cuotas, sp.valor_cuotas, sp.mes, sp.anio, e.estado, sp.id_estado, o.observacion AS observacion
        FROM solic_prestamo sp
        LEFT JOIN observaciones o ON sp.motivo_rechazo = o.id_observacion
        JOIN estado e ON sp.id_estado = e.id_estado";
$stmt = $con->prepare($sql);
$stmt->execute();

$creditos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .table thead {
            background-color: #343a40;
            color: white;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid">
        <h3 class="text-center text-secondary my-4">Préstamos</h3>
        <div class="col-12 p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID Usuario</th>
                            <th scope="col">Monto Solicitado</th>
                            <th scope="col">Cantidad de Cuotas</th>
                            <th scope="col">Valor de Cuotas</th>
                            <th scope="col">Mes</th>
                            <th scope="col">Año</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($creditos)): ?>
                            <?php foreach ($creditos as $credito): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($credito['id_usuario']); ?></td>
                                    <td><?php echo number_format($credito['monto_solicitado'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($credito['cant_cuotas']); ?></td>
                                    <td><?php echo number_format($credito['valor_cuotas'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($credito['mes']); ?></td>
                                    <td><?php echo htmlspecialchars($credito['anio']); ?></td>
                                    <td><?php echo htmlspecialchars($credito['estado']); ?></td>
                                    <td><?php echo htmlspecialchars($credito['observacion']); ?></td>   
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron registros</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
