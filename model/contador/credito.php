<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta SQL para obtener los datos de la tabla solic_prestamo
$sql = "SELECT sp.id_prestamo, sp.monto_solicitado, sp.cant_cuotas, sp.valor_cuotas, sp.mes, sp.anio, e.estado, sp.id_estado
        FROM solic_prestamo sp
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
    <title>Solicitud de Préstamo</title>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/presta.css">
    <script>
        function actualizarEstado(id_prestamo, estado_actual) {
            var nuevo_estado = estado_actual == 5 ? 7 : 5;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "actualizar_estado.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText);
                    location.reload();
                }
            };
            xhr.send("id_prestamo=" + id_prestamo + "&nuevo_estado=" + nuevo_estado);
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <div class="table-container">
            <h2>Créditos Actuales</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Préstamo</th>
                        <th>Monto Solicitado</th>
                        <th>Cantidad de Cuotas</th>
                        <th>Valor de Cuotas</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($creditos)): ?>
                        <?php foreach ($creditos as $credito): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($credito['id_prestamo']); ?></td>
                                <td><?php echo number_format($credito['monto_solicitado'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($credito['cant_cuotas']); ?></td>
                                <td><?php echo number_format($credito['valor_cuotas'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($credito['mes']); ?></td>
                                <td><?php echo htmlspecialchars($credito['anio']); ?></td>
                                <td><?php echo htmlspecialchars($credito['estado']); ?></td>
                                <td>
                                    <button class="<?php echo $credito['id_estado'] == 5 ? 'btn-cancel' : 'btn-approve'; ?>"
                                            onclick="actualizarEstado(<?php echo $credito['id_prestamo']; ?>, <?php echo $credito['id_estado']; ?>)">
                                        <?php echo $credito['id_estado'] == 5 ? 'Cancelar' : 'Aprobar'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No hay créditos para mostrar</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
