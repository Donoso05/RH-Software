<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php"); // Ruta correcta al archivo de conexión

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

$idUsuario = $_SESSION["id_usuario"];
$nitEmpresa = $_SESSION["nit_empresa"];
$monto = filter_var($_POST["monto"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

// Verificación de que el monto no sea menor a 500,000
if ($monto < 500000) {
    echo '<script>alert("El monto mínimo es de 500,000 pesos colombianos.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
    exit();
}

$cuotas = filter_var($_POST["cuotas"], FILTER_SANITIZE_NUMBER_INT);

// Verificación de que el número de cuotas no supere las 36 o sea menor a 2
if ($cuotas > 36) {
    echo '<script>alert("El número máximo de cuotas es de 36.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
    exit();
}

if ($cuotas < 2) {
    echo '<script>alert("El número minimo de cuotas es de 2.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
    exit();
}

$mes = date('F'); // Mes actual
$anio = date('Y'); // Año actual

// Función para calcular el valor de las cuotas sin interés
function calcularValorCuotas($monto, $cuotas) {
    return $monto / $cuotas;
}

// Verificar si el usuario tiene más créditos pendientes
$idEstadoEnRevision = 3; // ID del estado "En revisión"
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM solic_prestamo WHERE id_usuario = :idUsuario AND id_estado = :idEstado");
$stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmt->bindParam(':idEstado', $idEstadoEnRevision, PDO::PARAM_INT);
$stmt->execute();
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if ($datos["total"] > 0) {
    echo '<script>alert("Ya tienes un préstamo pendiente.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
    exit();
}

// Consultar el salario base del usuario
$stmtSalario = $con->prepare("SELECT tc.salario_base 
                              FROM usuario u 
                              INNER JOIN tipo_cargo tc ON u.id_tipo_cargo = tc.id_tipo_cargo 
                              WHERE u.id_usuario = :idUsuario");
$stmtSalario->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmtSalario->execute();
$salarioUsuario = $stmtSalario->fetch(PDO::FETCH_ASSOC)['salario_base'];

// Calcular el salario multiplicado por dos
$salarioDoble = $salarioUsuario * 2;

// Verificar si el monto solicitado supera la capacidad de endeudamiento
if ($monto > $salarioDoble) {
    echo '<script>alert("El monto solicitado supera su capacidad de endeudamiento.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
    exit();
}

// Calcular el valor de las cuotas
$valorCuotas = calcularValorCuotas($monto, $cuotas);

// Verificar si el valor de la cuota no supera el 40% del salario base
$maxValorCuota = $salarioUsuario * 0.40;
if ($valorCuotas > $maxValorCuota) {
    echo '<script>alert("El valor de la cuota no puede superar el 40% de su salario base.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
    exit();
}

// Generar un id_prestamo único basado en el id_usuario y un número aleatorio de 4 dígitos
do {
    $randomNumber = rand(1000, 9999);
    $idPrestamo = $idUsuario . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);

    // Verificar si el id_prestamo ya existe en la base de datos
    $stmtCheck = $con->prepare("SELECT COUNT(*) FROM solic_prestamo WHERE id_prestamo = :idPrestamo");
    $stmtCheck->bindParam(':idPrestamo', $idPrestamo, PDO::PARAM_STR);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();
} while ($count > 0);

// Formatear el monto y valor de las cuotas en pesos colombianos
$montoFormateado = number_format($monto, 0, ',', '.');
$valorCuotasFormateado = number_format($valorCuotas, 0, ',', '.');

// Insertar el préstamo en la base de datos
try {
    $stmt = $con->prepare("INSERT INTO solic_prestamo (id_prestamo, id_usuario, monto_solicitado, id_estado, valor_cuotas, cant_cuotas, mes, anio, nit_empresa) 
                           VALUES (:idPrestamo, :idUsuario, :monto, :idEstado, :valorCuotas, :cuotas, :mes, :anio, :nitEmpresa)");
    $stmt->bindParam(':idPrestamo', $idPrestamo, PDO::PARAM_STR); // Usamos PDO::PARAM_STR para id_prestamo
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindParam(':monto', $monto, PDO::PARAM_STR); // PDO::PARAM_STR es adecuado para decimal
    $stmt->bindParam(':idEstado', $idEstadoEnRevision, PDO::PARAM_INT);
    $stmt->bindParam(':valorCuotas', $valorCuotas, PDO::PARAM_STR); // PDO::PARAM_STR es adecuado para decimal
    $stmt->bindParam(':cuotas', $cuotas, PDO::PARAM_INT);
    $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_STR);
    $stmt->bindParam(':nitEmpresa', $nitEmpresa, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo '<script>alert("Solicitud de préstamo enviada con éxito.");</script>';
        echo '<script>window.location.href = "credito.php";</script>';
    } else {
        throw new Exception("Error al insertar el préstamo en la base de datos.");
    }
} catch (Exception $e) {
    error_log($e->getMessage()); // Registrar el error
    echo '<script>alert("Hubo un error al enviar la solicitud de préstamo. Inténtalo de nuevo.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
}
exit();
?>
