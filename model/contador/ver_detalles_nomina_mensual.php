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
// Conectar a la base de datos
$con = $db->conectar();

// Obtener el id_nomina de la URL
$id_nomina = isset($_GET['id_nomina']) ? intval($_GET['id_nomina']) : 0;

// Consultar los detalles de la liquidación y datos del usuario
$query = "SELECT u.nombre, d.*
          FROM detalle d
          JOIN usuario u ON d.id_usuario = u.id_usuario
          WHERE d.id_nomina = :id_nomina";
$stmt = $con->prepare($query);
$stmt->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
$stmt->execute();
$detalle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$detalle) {
    echo '<script>alert("No hay detalles de liquidación disponibles para esta nómina.");</script>';
    echo '<script>window.location.href = "ver_liquidacion.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Liquidación</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/detalles_nomina.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>
<body>
    <?php include("nav.php"); ?>
    <main class="container mt-5">
        <div class="row">
            <!-- Ingresos y Deducciones -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-money-check-alt"></i> Valores de la Liquidación</h5>
                        
                        <div class="section-title">Ingresos</div>
                        <div class="section-ingresos">
                            <p class="card-text"><strong>Fecha de Liquidación:</strong> <?php echo $detalle['fecha_li']; ?></p>
                            <p class="card-text"><strong>Valor Horas Extras:</strong> $<?php echo number_format($detalle['valor_horas_extras'], 0); ?></p>
                            <p class="card-text"><strong>Monto Solicitado:</strong> $<?php echo number_format($detalle['monto_solicitado'], 2); ?></p>
                            <p class="card-text"><strong>Auxilio Transporte:</strong> 
                                <?php 
                                    echo $detalle['salario_total'] > 2600000 ? "No aplica" : "$" . number_format($detalle['aux_transporte_valor'], 0); 
                                ?>
                            </p>
                            <p class="card-text"><strong>Total Ingresos:</strong> $<?php echo number_format($detalle['total_ingresos'], 0); ?></p>
                        </div>

                        <div class="section-title">Deducciones</div>
                        <div class="section-deducciones">
                            <p class="card-text"><strong>Precio ARL:</strong> $<?php echo number_format($detalle['precio_arl'], 0); ?></p>
                            <p class="card-text"><strong>Deducción Salud:</strong> $<?php echo number_format($detalle['deduccion_salud'], 0); ?></p>
                            <p class="card-text"><strong>Deducción Pensión:</strong> $<?php echo number_format($detalle['deduccion_pension'], 0); ?></p>
                            <p class="card-text"><strong>Valor Cuotas:</strong> $<?php echo number_format($detalle['valor_cuotas'], 0); ?></p>
                            <p class="card-text"><strong>Total Deducciones:</strong> $<?php echo number_format($detalle['total_deducciones'], 0); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Información del Usuario y Total -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user"></i> Datos del Usuario</h5>
                        <p class="card-text"><strong>Nombre:</strong> <?php echo $detalle['nombre']; ?></p>
                        <p class="card-text"><strong>Documento:</strong> <?php echo $detalle['id_usuario']; ?></p>
                        <p class="card-text"><strong>Sueldo Base:</strong> $<?php echo number_format($detalle['salario_total'], 0); ?></p>
                        <p class="card-text"><strong>Días Trabajados:</strong> <?php echo $detalle['dias_trabajados']; ?></p>
                        <p class="card-text"><strong>Horas Extras:</strong> <?php echo $detalle['horas_extras']; ?></p>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-calculator"></i> Total</h5>
                        <div class="section-total">
                            <p class="card-text"><strong>Valor Neto:</strong> $<?php echo number_format($detalle['valor_neto'], 0); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
