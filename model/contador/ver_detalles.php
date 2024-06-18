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

// Obtener el id de usuario de la sesión
$id_usuario = $_SESSION["id_usuario"];

// Consultar los detalles de la liquidación
$query = "SELECT * FROM detalle WHERE id_usuario = :id_usuario";
$stmt = $con->prepare($query);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$detalle = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Liquidación</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/nav.css">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include("nav.php"); ?>
    <main class="container">
        <div class="grid">
            <section>
                <hgroup>
                    <h2>Detalles de la Liquidación</h2>
                    <h3>Usuario ID: <?php echo $id_usuario; ?></h3>
                </hgroup>
                <?php if ($detalle): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liquidación #<?php echo $detalle['id_detalle']; ?></h5>
                        <p class="card-text"><strong>Fecha de Liquidación:</strong> <?php echo $detalle['fecha_li']; ?></p>
                        <p class="card-text"><strong>Salario Total:</strong> $<?php echo number_format($detalle['salario_total'], 2); ?></p>
                        <p class="card-text"><strong>Días Trabajados:</strong> <?php echo $detalle['dias_trabajados']; ?></p>
                        <p class="card-text"><strong>Horas Extras:</strong> <?php echo $detalle['horas_extras']; ?></p>
                        <p class="card-text"><strong>Valor Horas Extras:</strong> $<?php echo number_format($detalle['valor_horas_extras'], 2); ?></p>
                        <p class="card-text"><strong>Total Deducciones:</strong> $<?php echo number_format($detalle['total_deducciones'], 2); ?></p>
                        <p class="card-text"><strong>Total Ingresos:</strong> $<?php echo number_format($detalle['total_ingresos'], 2); ?></p>
                        <p class="card-text"><strong>Valor Neto:</strong> $<?php echo number_format($detalle['valor_neto'], 2); ?></p>
                        <p class="card-text"><strong>Valor Cuotas:</strong> $<?php echo number_format($detalle['valor_cuotas'], 2); ?></p>
                        <p class="card-text"><strong>Monto Solicitado:</strong> $<?php echo number_format($detalle['monto_solicitado'], 2); ?></p>
                    </div>
                </div>
                <?php else: ?>
                <p>No hay detalles de liquidación disponibles para este usuario.</p>
                <?php endif; ?>
            </section>
        </div>
        <section aria-label="Suscribirse">
            <div class="container">
                <article>
                    <hgroup>
                        <h2>Suscríbete para más información</h2>
                        <h3>Recibe actualizaciones y noticias</h3>
                    </hgroup>
                    <form class="grid">
                        <input type="text" id="firstname" name="firstname" placeholder="Nombre" aria-label="Nombre" required />
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" aria-label="Correo electrónico" required />
                        <button type="submit" onclick="event.preventDefault()">Suscribirse</button>
                    </form>
                </article>
            </div>
        </section>
    </main>
    <footer class="container">
        <small><a href="#">Política de privacidad</a> • <a href="#">Términos de servicio</a></small>
    </footer>
</body>
</html>
