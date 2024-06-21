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

// Obtener el id de usuario y el nit_empresa de la sesión
$id_usuario = $_SESSION["id_usuario"];
$nit_empresa = $_SESSION["nit_empresa"]; // Obtener el nit_empresa de la sesión

// Consultar los registros de nómina del usuario logueado que coincidan con el id_usuario y el nit_empresa
$query = $con->prepare("SELECT * FROM nomina WHERE id_usuario = :id_usuario AND nit_empresa = :nit_empresa");
$query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$query->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
$query->execute();
$nominas = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Liquidaciones</title>
    <link rel="stylesheet" href="css/liquidacion.css">
    <style>
        .details {
            display: none;
        }
        .details td {
            padding-left: 20px;
        }
    </style>
</head>
<body>
<?php include("nav.php") ?>
<div class="container">
    <h1>Mis Liquidaciones</h1>
    <table id="liquidacionesTable">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Monto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($nominas as $nomina): ?>
                <tr class="summary">
                    <td><?php echo htmlspecialchars($nomina['mes']); ?></td>
                    <td><?php echo htmlspecialchars($nomina['salario_total']); ?></td>
                    <td><button class="toggle-details">Ver Detalles</button></td>
                </tr>
                <tr class="details">
                    <td colspan="3">
                        <div>
                            <p>Año: <?php echo htmlspecialchars($nomina['anio']); ?></p>
                            <p>Estado: <?php echo htmlspecialchars($nomina['id_estado']); ?></p>
                            <p>ARL: <?php echo htmlspecialchars($nomina['precio_arl']); ?></p>
                            <p>Salud: <?php echo htmlspecialchars($nomina['deduccion_salud']); ?></p>
                            <p>Pensión: <?php echo htmlspecialchars($nomina['deduccion_pension']); ?></p>
                            <p>Total Deducciones: <?php echo htmlspecialchars($nomina['total_deducciones']); ?></p>
                            <p>Auxilio Transporte: <?php echo htmlspecialchars($nomina['aux_transporte_valor']); ?></p>
                            <p>Horas Extras: <?php echo htmlspecialchars($nomina['horas_extras']); ?></p>
                            <p>Salario Base: <?php echo htmlspecialchars($nomina['salario_base']); ?></p>
                            <p>Días Trabajados: <?php echo htmlspecialchars($nomina['dias_trabajados']); ?></p>
                            <p>Valor Horas Extras: <?php echo htmlspecialchars($nomina['valor_horas_extras']); ?></p>
                            <p>Total Ingresos: <?php echo htmlspecialchars($nomina['total_ingresos']); ?></p>
                            <p>Valor Neto: <?php echo htmlspecialchars($nomina['valor_neto']); ?></p>
                            <p>Fecha Liquidación: <?php echo htmlspecialchars($nomina['fecha_li']); ?></p>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', function() {
            var detailsRow = this.closest('tr').nextElementSibling;
            if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
                detailsRow.style.display = 'table-row';
                this.textContent = 'Ocultar Detalles';
            } else {
                detailsRow.style.display = 'none';
                this.textContent = 'Ver Detalles';
            }
        });
    });
</script>
</body>
</html>
