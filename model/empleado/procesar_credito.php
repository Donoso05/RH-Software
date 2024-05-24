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
$monto = filter_var($_POST["monto"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$cuotas = filter_var($_POST["cuotas"], FILTER_SANITIZE_NUMBER_INT);
$interesAnual = 12; // Ejemplo de tasa de interés anual
$mes = date('F'); // Mes actual
$anio = date('Y'); // Año actual

// Función para calcular el valor de las cuotas
function calcularValorCuotas($monto, $cuotas, $interesAnual) {
    // Convierte la tasa de interés anual a mensual
    $interesMensual = $interesAnual / 12 / 100;
    
    // Si la tasa de interés es 0, solo divide el monto por el número de cuotas
    if ($interesMensual == 0) {
        return $monto / $cuotas;
    }

    // Fórmula para calcular la cuota mensual con interés compuesto
    $valorCuota = $monto * ($interesMensual * pow(1 + $interesMensual, $cuotas)) / (pow(1 + $interesMensual, $cuotas) - 1);

    return $valorCuota;
}

// Verificar si el usuario tiene más créditos pendientes
$idEstadoEnRevision = 5; // ID del estado "En revisión"
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

// Generar un id_prestamo único basado en el id_usuario y un número aleatorio de 4 dígitos
$randomNumber = rand(1000, 9999);
$idPrestamo = $idUsuario . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);

// Calcular el valor de las cuotas
$valorCuotas = calcularValorCuotas($monto, $cuotas, $interesAnual);

// Formatear el monto y valor de las cuotas en pesos colombianos
$montoFormateado = number_format($monto, 0, ',', '.');
$valorCuotasFormateado = number_format($valorCuotas, 0, ',', '.');

// Insertar el préstamo en la base de datos
$stmt = $con->prepare("INSERT INTO solic_prestamo (id_prestamo, id_usuario, monto_solicitado, id_estado, valor_cuotas, cant_cuotas, mes, anio) 
                       VALUES (:idPrestamo, :idUsuario, :monto, :idEstado, :valorCuotas, :cuotas, :mes, :anio)");
$stmt->bindParam(':idPrestamo', $idPrestamo, PDO::PARAM_STR); // Usamos PDO::PARAM_STR para id_prestamo
$stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmt->bindParam(':monto', $monto, PDO::PARAM_STR); // PDO::PARAM_STR es adecuado para decimal
$stmt->bindParam(':idEstado', $idEstadoEnRevision, PDO::PARAM_INT); // Reusamos idEstadoEnRevision
$stmt->bindParam(':valorCuotas', $valorCuotas, PDO::PARAM_STR); // PDO::PARAM_STR es adecuado para decimal
$stmt->bindParam(':cuotas', $cuotas, PDO::PARAM_INT);
$stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
$stmt->bindParam(':anio', $anio, PDO::PARAM_STR);

if ($stmt->execute()) {
    echo '<script>alert("Solicitud de préstamo enviada con éxito.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
} else {
    echo '<script>alert("Hubo un error al enviar la solicitud de préstamo. Inténtalo de nuevo.");</script>';
    echo '<script>window.location.href = "credito.php";</script>';
}
exit();
?>
