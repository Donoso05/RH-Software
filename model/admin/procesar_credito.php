<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

$db = new Database();
$con = $db->conectar();

$idUsuario = filter_var($_POST["id_usuario"], FILTER_SANITIZE_NUMBER_INT);
$monto = filter_var($_POST["monto"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$nitEmpresa = filter_var($_POST["nit_empresa"], FILTER_SANITIZE_STRING);

if ($monto < 500000) {
    echo '<script>alert("El monto mínimo es de 500,000 pesos colombianos.");</script>';
    echo '<script>window.location.href = "solic_prestamo.php";</script>';
    exit();
}

$cuotas = filter_var($_POST["cuotas"], FILTER_SANITIZE_NUMBER_INT);

if ($cuotas > 36) {
    echo '<script>alert("El número máximo de cuotas es de 36.");</script>';
    echo '<script>window.location.href = "solic_prestamo.php";</script>';
    exit();
}

$interesAnual = 12;
$mes = date('F');
$anio = date('Y');

function calcularValorCuotas($monto, $cuotas, $interesAnual) {
    $interesMensual = $interesAnual / 12 / 100;
    if ($interesMensual == 0) {
        return $monto / $cuotas;
    }
    $valorCuota = $monto * ($interesMensual * pow(1 + $interesMensual, $cuotas)) / (pow(1 + $interesMensual, $cuotas) - 1);
    return $valorCuota;
}

$idEstadoEnRevision = 3;
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM solic_prestamo WHERE id_usuario = :idUsuario AND id_estado = :idEstado");
$stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmt->bindParam(':idEstado', $idEstadoEnRevision, PDO::PARAM_INT);
$stmt->execute();
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if ($datos["total"] > 0) {
    echo '<script>alert("Ya tienes un préstamo pendiente.");</script>';
    echo '<script>window.location.href = "solic_prestamo.php";</script>';
    exit();
}

$stmtSalario = $con->prepare("SELECT tc.salario_base 
                              FROM usuario u 
                              INNER JOIN tipo_cargo tc ON u.id_tipo_cargo = tc.id_tipo_cargo 
                              WHERE u.id_usuario = :idUsuario");
$stmtSalario->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmtSalario->execute();
$salarioUsuario = $stmtSalario->fetch(PDO::FETCH_ASSOC)['salario_base'];

$salarioDoble = $salarioUsuario * 2;

if ($monto > $salarioDoble) {
    echo '<script>alert("El monto solicitado supera su capacidad de endeudamiento.");</script>';
    echo '<script>window.location.href = "solic_prestamo.php";</script>';
    exit();
}

do {
    $randomNumber = rand(1000, 9999);
    $idPrestamo = $idUsuario . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);
    $stmtCheck = $con->prepare("SELECT COUNT(*) FROM solic_prestamo WHERE id_prestamo = :idPrestamo");
    $stmtCheck->bindParam(':idPrestamo', $idPrestamo, PDO::PARAM_STR);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();
} while ($count > 0);

$valorCuotas = calcularValorCuotas($monto, $cuotas, $interesAnual);

$montoFormateado = number_format($monto, 0, ',', '.');
$valorCuotasFormateado = number_format($valorCuotas, 0, ',', '.');

try {
    $stmt = $con->prepare("INSERT INTO solic_prestamo (id_prestamo, id_usuario, monto_solicitado, id_estado, valor_cuotas, cant_cuotas, mes, anio, nit_empresa) 
                           VALUES (:idPrestamo, :idUsuario, :monto, :idEstado, :valorCuotas, :cuotas, :mes, :anio, :nitEmpresa)");
    $stmt->bindParam(':idPrestamo', $idPrestamo, PDO::PARAM_STR);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
    $stmt->bindParam(':idEstado', $idEstadoEnRevision, PDO::PARAM_INT);
    $stmt->bindParam(':valorCuotas', $valorCuotas, PDO::PARAM_STR);
    $stmt->bindParam(':cuotas', $cuotas, PDO::PARAM_INT);
    $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_STR);
    $stmt->bindParam(':nitEmpresa', $nitEmpresa, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo '<script>alert("Solicitud de préstamo enviada con éxito.");</script>';
        echo '<script>window.location.href = "solic_prestamo.php";</script>';
    } else {
        throw new Exception("Error al insertar el préstamo en la base de datos.");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo '<script>alert("Hubo un error al enviar la solicitud de préstamo. Inténtalo de nuevo.");</script>';
    echo '<script>window.location.href = "solic_prestamo.php";</script>';
}
exit();
?>
