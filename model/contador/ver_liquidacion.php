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

// Consulta SQL para obtener los datos de la tabla nomina
$sql = "SELECT n.*, u.nombre, e.estado AS estado_nombre, arl.tipo AS arl_tipo, s.porcentaje_s, p.porcentaje_p, at.valor AS auxtransporte_valor
        FROM nomina n
        INNER JOIN usuario u ON n.id_usuario = u.id_usuario
        INNER JOIN estado e ON n.id_estado = e.id_estado
        INNER JOIN arl ON n.id_arl = arl.id_arl
        INNER JOIN salud s ON n.id_salud = s.id_salud
        INNER JOIN pension p ON n.id_pension = p.id_pension
        INNER JOIN auxtransporte at ON n.id_auxtransporte = at.id_auxtransporte";
$stmt = $con->prepare($sql);
$stmt->execute();

$nominas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Nómina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .card {
            margin-bottom: 20px;
        }

        .container {
            max-width: 1200px;
        }
    </style>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <h3 class="text-center text-secondary my-4">Gestión de Nómina</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Mes</th>
                        <th scope="col">Año</th>
                        <th scope="col">Estado</th>
                        <th scope="col">ARL</th>
                        <th scope="col">Salario Base</th>
                        <th scope="col">Salud</th>
                        <th scope="col">Pensión</th>
                        <th scope="col">Total Deducciones</th>
                        <th scope="col">Aux. Transporte</th>
                        <th scope="col">Horas Extras</th>
                        <th scope="col">Salario Total</th>
                        <th scope="col">Días Trabajados</th>
                        <th scope="col">Valor Horas Extras</th>
                        <th scope="col">Total Ingresos</th>
                        <th scope="col">Valor Neto</th>
                        <th scope="col">Fecha de Liquidación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nominas as $nomina): ?>
                        <tr>
                            <td><?php echo $nomina["nombre"]; ?></td>
                            <td><?php echo $nomina["mes"]; ?></td>
                            <td><?php echo $nomina["anio"]; ?></td>
                            <td><?php echo $nomina["estado_nombre"]; ?></td>
                            <td><?php echo $nomina["arl_tipo"]; ?></td>
                            <td><?php echo number_format($nomina["salario_base"], 0, ',', '.'); ?></td>
                            <td><?php echo $nomina["porcentaje_s"]; ?>%</td>
                            <td><?php echo $nomina["porcentaje_p"]; ?>%</td>
                            <td><?php echo number_format($nomina["total_deducciones"], 0, ',', '.'); ?></td>
                            <td>
                                <?php 
                                if ($nomina["salario_base"] >= 2600000) {
                                    echo "No aplica";
                                } else {
                                    echo number_format($nomina["auxtransporte_valor"], 0, ',', '.'); 
                                }
                                ?>
                            </td>
                            <td><?php echo $nomina["horas_extras"]; ?></td>
                            <td><?php echo number_format($nomina["salario_total"], 0, ',', '.'); ?></td>
                            <td><?php echo $nomina["dias_trabajados"]; ?></td>
                            <td><?php echo number_format($nomina["valor_horas_extras"], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($nomina["total_ingresos"], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($nomina["valor_neto"], 0, ',', '.'); ?></td>
                            <td><?php echo $nomina["fecha_li"]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Y3tGSOE2mDkGjm9PYf47FZ5gmpG5" crossorigin="anonymous"></script>
</body>

</html>
