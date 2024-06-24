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
$query = $con->prepare("SELECT * FROM detalle WHERE id_usuario = :id_usuario AND nit_empresa = :nit_empresa");
$query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$query->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
$query->execute();
$nominas = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Liquidaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
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
<div class="container my-4">
    <h1 class="text-center">Mis Liquidaciones</h1>
    <div class="table-responsive">
        <table class="table table-striped" id="liquidacionesTable">
            <thead class="bg-dark text-white">
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
                        <td><button class="btn btn-primary btn-sm toggle-details">Ver Detalles</button></td>
                    </tr>
                    <tr class="details">
                        <td colspan="3">
                            <div class="p-3">
                                <p><strong>Año:</strong> <?php echo htmlspecialchars($nomina['anio']); ?></p>
                                <p><strong>ARL:</strong> <?php echo htmlspecialchars($nomina['precio_arl']); ?></p>
                                <p><strong>Salud:</strong> <?php echo htmlspecialchars($nomina['deduccion_salud']); ?></p>
                                <p><strong>Pensión:</strong> <?php echo htmlspecialchars($nomina['deduccion_pension']); ?></p>
                                <p><strong>Total Deducciones:</strong> <?php echo htmlspecialchars($nomina['total_deducciones']); ?></p>
                                <p><strong>Auxilio Transporte:</strong> <?php echo htmlspecialchars($nomina['aux_transporte_valor']); ?></p>
                                <p><strong>Horas Extras:</strong> <?php echo htmlspecialchars($nomina['horas_extras']); ?></p>
                                <p><strong>Salario Total:</strong> <?php echo htmlspecialchars($nomina['salario_total']); ?></p>
                                <p><strong>Días Trabajados:</strong> <?php echo htmlspecialchars($nomina['dias_trabajados']); ?></p>
                                <p><strong>Valor Horas Extras:</strong> <?php echo htmlspecialchars($nomina['valor_horas_extras']); ?></p>
                                <p><strong>Total Ingresos:</strong> <?php echo htmlspecialchars($nomina['total_ingresos']); ?></p>
                                <p><strong>Valor Neto:</strong> <?php echo htmlspecialchars($nomina['valor_neto']); ?></p>
                                <p><strong>Fecha Liquidación:</strong> <?php echo htmlspecialchars($nomina['fecha_li']); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
