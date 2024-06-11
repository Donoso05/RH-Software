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

$id_usuario = $_SESSION["id_usuario"];

// Consultar los créditos del usuario
$stmtCreditos = $con->prepare("
    SELECT sp.id_prestamo, sp.monto_solicitado, sp.cant_cuotas, sp.valor_cuotas, sp.mes, sp.anio, e.estado AS nombre_estado 
    FROM solic_prestamo sp
    JOIN estado e ON sp.id_estado = e.id_estado
    WHERE sp.id_usuario = :id_usuario
");
$stmtCreditos->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmtCreditos->execute();
$creditos = $stmtCreditos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Préstamo</title>
    <link rel="stylesheet" href="css/presta.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <div class="form-container">
            <h2>Solicitud de Préstamo</h2>
            <form id="creditoForm" method="post" action="procesar_credito.php">
                <div class="form-group">
                    <label for="monto">Monto Solicitado:</label>
                    <input type="number" id="monto" name="monto" required>
                </div>
                <div class="form-group">
                    <label for="cuotas">Cantidad de Cuotas:</label>
                    <input type="number" id="cuotas" name="cuotas" required>
                </div>
                <div class="form-group">
                    <label for="valorCuotas">Valor de cada Cuota:</label>
                    <span id="valorCuotas"></span>
                </div>
                <button type="submit">Enviar Solicitud</button>
            </form>
        </div>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($creditos as $credito): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($credito['id_prestamo']); ?></td>
                            <td><?php echo number_format($credito['monto_solicitado'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($credito['cant_cuotas']); ?></td>
                            <td><?php echo number_format($credito['valor_cuotas'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($credito['mes']); ?></td>
                            <td><?php echo htmlspecialchars($credito['anio']); ?></td>
                            <td><?php echo htmlspecialchars($credito['nombre_estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/credito.js"></script>
</body>

</html>


