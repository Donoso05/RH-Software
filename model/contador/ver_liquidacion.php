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
date_default_timezone_set('America/Bogota'); 

function actualizarEstadoAPagado($con) {
    $current_date = date('Y-m-d'); // Usar la fecha real del servidor
    $last_day_of_month = date('Y-m-t'); // Obtener el último día del mes

    // Añadir depuración
    echo '<script>console.log("Fecha actual: ' . $current_date . '");</script>';
    echo '<script>console.log("Último día del mes: ' . $last_day_of_month . '");</script>';

    if ($current_date == $last_day_of_month) {
        echo '<script>console.log("Actualizando estado a pagado...");</script>';
        $current_mes = date('m');
        $current_anio = date('Y');
        $sql_update_estado = "UPDATE nomina 
                              SET id_estado = 8 
                              WHERE mes = :mes AND anio = :anio";
        $stmt_update_estado = $con->prepare($sql_update_estado);
        $stmt_update_estado->bindParam(':mes', $current_mes, PDO::PARAM_INT);
        $stmt_update_estado->bindParam(':anio', $current_anio, PDO::PARAM_INT);
        $stmt_update_estado->execute();

        // Confirmar que la actualización se ejecutó
        if ($stmt_update_estado->rowCount() > 0) {
            echo '<script>console.log("Estado actualizado correctamente.");</script>';
        } else {
            echo '<script>console.log("No se actualizó ningún registro.");</script>';
        }
    } else {
        echo '<script>console.log("No es el último día del mes.");</script>';
    }
}

if (isset($_GET['update'])) {
    actualizarEstadoAPagado($con);
    exit();
}

function vaciarTablaNominaAlInicioDelMes($con)
{
    $current_date = date('Y-m-d');
    $first_day_of_month = date('Y-m-01'); // Obtener el primer día del mes

    echo '<script>console.log("Fecha actual: ' . $current_date . '");</script>';
    echo '<script>console.log("Primer día del mes: ' . $first_day_of_month . '");</script>';

    // Verificar si es el primer día del mes
    if ($current_date === $first_day_of_month) {
        echo '<script>console.log("Vaciando tabla nomina...");</script>';

        // Vaciar la tabla 'nomina'
        $sql_empty_nomina = "TRUNCATE TABLE nomina";
        $con->exec($sql_empty_nomina);
    } else {
        echo '<script>console.log("No es el primer día del mes.");</script>';
    }
}

// Llamar a la función para vaciar la tabla 'nomina' al inicio del mes
vaciarTablaNominaAlInicioDelMes($con);

// Continuar con la lógica de selección y renderizado de la vista HTML
$sql = "SELECT n.*, u.nombre, e.estado AS estado_nombre
        FROM nomina n
        INNER JOIN usuario u ON n.id_usuario = u.id_usuario
        INNER JOIN estado e ON n.id_estado = e.id_estado";
$stmt = $con->prepare($sql);
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
    <link rel="stylesheet" href="css/ver_liquidacion.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <h3 class="text-center text-secondary my-4">Gestión de Nómina Mensual</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Salario Base</th>   
                        <th scope="col">Fecha de Liquidación</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nominas)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay registros de nómina.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($nominas as $nomina): ?>
                            <tr>
                                <td><?php echo $nomina["nombre"]; ?></td>
                                <td><?php echo $nomina["estado_nombre"]; ?></td>
                                <td><?php echo number_format($nomina["salario_base"], 0, ',', '.'); ?></td> 
                                <td><?php echo $nomina["fecha_li"]; ?></td>
                                <td>
                                    <a href="ver_detalles_nomina_mensual.php?id_nomina=<?php echo $nomina['id_nomina']; ?>" class="btn btn-primary btn-sm">Ver Detalles</a>
                                    <?php if ($nomina['id_estado'] == 3): ?>
                                        <a href="liquidar.php?id_usuario=<?php echo $nomina['id_usuario']; ?>" class="btn btn-danger btn-sm">Liquidar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        window.onload = function() {
            fetch('ver_liquidacion.php?update=true')
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Y3tGSOE2mDkGjm9PYf47FZ5gmpG5" crossorigin="anonymous"></script>
</body>
</html>
