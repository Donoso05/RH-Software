<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/tablas.css">
    <title>Tabla Prestamos</title>
</head>
<body>
    <h2>Solicitud Prestamo</h2>
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th>ID Prestamo</th>
                    <th>ID Usuario</th>
                    <th>Monto Solicitado</th>
                    <th>Estado</th>
                    <th>Valor Cuotas</th>
                    <th>Cantidad Cuotas</th>
                    <th><a href="admin.php">REGRESAR</a></th>
                </tr>
            </thead>
            <tbody>

            <?php
            $sql = "SELECT solic_prestamo.id_prestamo, solic_prestamo.id_usuario, solic_prestamo.monto_solicitado, estado.estado, solic_prestamo.valor_cuotas, solic_prestamo.cant_cuotas FROM solic_prestamo 
                    JOIN estado ON solic_prestamo.id_estado = estado.id_estado";

            $result = $con->query($sql);

            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['id_prestamo'] . "</td>";
                    echo "<td>" . $row['id_usuario'] . "</td>";
                    echo "<td>" . $row['monto_solicitado'] . "</td>";
                    echo "<td>" . $row['estado'] . "</td>";  
                    echo "<td>" . $row['valor_cuotas'] . "</td>";
                    echo "<td>" . $row['cant_cuotas'] . "</td>";
                    echo "<td><a href='update_prestamo.php?id={$row['id_usuario']}' class='btn' onclick=\"window.open('update_prestamo.php?id={$row['id_usuario']}','','width=500,height=500,toolbar=NO'); return false;\">Actualizar</a></td>";
                    echo "</tr>"; 
                }
            } 
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
