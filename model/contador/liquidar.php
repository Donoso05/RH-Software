<?php
session_start(); // Iniciar la sesión

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();
date_default_timezone_set('America/Bogota');  // Establece la zona horaria a Bogotá

$id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : $_SESSION["id_usuario"];
$nit_empresa = $_SESSION["nit_empresa"]; // Obtener el nit_empresa de la sesión

// Función para vaciar la tabla 'nomina' al inicio del mes
function vaciarTablaNominaAlInicioDelMes($con)
{
    $current_date = date('Y-m-d');
    $first_day_of_month = date('Y-m-01'); // Obtener el primer día del mes

    // Verificar si es el primer día del mes
    if ($current_date === $first_day_of_month) {
        // Vaciar la tabla 'nomina'
        $sql_empty_nomina = "TRUNCATE TABLE nomina";
        $con->exec($sql_empty_nomina);
    }
}

// Llamar a la función para vaciar la tabla 'nomina' al inicio del mes
vaciarTablaNominaAlInicioDelMes($con);

// Obtener el id_arl y salario_base del usuario
$sql_get_details = "SELECT tipo_cargo.id_arl, tipo_cargo.salario_base 
                    FROM usuario
                    INNER JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo
                    WHERE usuario.id_usuario = :id_usuario";
$stmt_get_details = $con->prepare($sql_get_details);
$stmt_get_details->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_get_details->execute();
$user_details = $stmt_get_details->fetch(PDO::FETCH_ASSOC);

if (!$user_details) {
    die("Error al obtener los detalles del usuario.");
}

$id_arl = $user_details['id_arl'];
$salario_base = $user_details['salario_base'];

// Obtener el id_prestamo del mes actual y que sea diferente de 9 y 7
$current_mes = date('F'); // Ej: June
$current_anio = date('Y'); // Ej: 2024
$sql_get_prestamo = "SELECT id_prestamo 
                     FROM solic_prestamo 
                     WHERE id_usuario = :id_usuario 
                     AND id_estado NOT IN (9, 7) 
                     AND mes = :mes 
                     AND anio = :anio
                     LIMIT 1";
$stmt_get_prestamo = $con->prepare($sql_get_prestamo);
$stmt_get_prestamo->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_get_prestamo->bindParam(':mes', $current_mes, PDO::PARAM_STR);
$stmt_get_prestamo->bindParam(':anio', $current_anio, PDO::PARAM_STR);
$stmt_get_prestamo->execute();
$id_prestamo = $stmt_get_prestamo->fetchColumn();

// Obtener el valor del aux_transporte
$sql_get_aux_transporte = "SELECT valor FROM auxtransporte WHERE id_auxtransporte = 1"; // Ajusta el ID según sea necesario
$stmt_get_aux_transporte = $con->prepare($sql_get_aux_transporte);
$stmt_get_aux_transporte->execute();
$aux_transporte_valor = $stmt_get_aux_transporte->fetchColumn();

// Verificar si el usuario ya tiene un registro en la tabla 'nomina'
$sql_check_nomina = "SELECT COUNT(*) FROM nomina WHERE id_usuario = :id_usuario";
$stmt_check_nomina = $con->prepare($sql_check_nomina);
$stmt_check_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_check_nomina->execute();
$user_in_nomina = $stmt_check_nomina->fetchColumn();

if ($user_in_nomina == 0) {
    // Generar un id_nomina único aleatorio de 6 cifras
    do {
        $id_nomina = rand(100000, 999999);
        $sql_check_id_nomina = "SELECT COUNT(*) FROM nomina WHERE id_nomina = :id_nomina";
        $stmt_check_id_nomina = $con->prepare($sql_check_id_nomina);
        $stmt_check_id_nomina->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
        $stmt_check_id_nomina->execute();
        $id_nomina_exists = $stmt_check_id_nomina->fetchColumn();
    } while ($id_nomina_exists > 0);

    // Insertar un nuevo registro en la tabla 'nomina' incluyendo el nit_empresa
    $sql_insert_nomina = "INSERT INTO nomina (id_nomina, id_usuario, deduccion_salud, deduccion_pension, precio_arl, aux_transporte_valor, salario_base, nit_empresa) 
                          VALUES (:id_nomina, :id_usuario, 0, 0, 0, :aux_transporte_valor, :salario_base, :nit_empresa)";
    $stmt_insert_nomina = $con->prepare($sql_insert_nomina);
    $stmt_insert_nomina->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':aux_transporte_valor', $aux_transporte_valor, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':salario_base', $salario_base, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR); // Añadir nit_empresa aquí
    $stmt_insert_nomina->execute();
}

// Obtener el id_nomina para el usuario
$sql_get_nomina = "SELECT id_nomina FROM nomina WHERE id_usuario = :id_usuario";
$stmt_get_nomina = $con->prepare($sql_get_nomina);
$stmt_get_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_get_nomina->execute();
$id_nomina = $stmt_get_nomina->fetchColumn();

// Función para actualizar el estado a pagado al final del mes
function actualizarEstadoAPagado($con)
{
    $current_date = date('Y-m-d');
    $last_day_of_month = date('Y-m-t'); // Obtener el último día del mes

    // Verificar si es el último día del mes
    if ($current_date === $last_day_of_month) {
        $current_mes = date('m');
        $current_anio = date('Y');

        // Actualizar la tabla 'nomina'
        $sql_update_estado_nomina = "UPDATE nomina 
                                     SET id_estado = 8 
                                     WHERE mes = :mes AND anio = :anio";
        $stmt_update_estado_nomina = $con->prepare($sql_update_estado_nomina);
        $stmt_update_estado_nomina->bindParam(':mes', $current_mes, PDO::PARAM_INT);
        $stmt_update_estado_nomina->bindParam(':anio', $current_anio, PDO::PARAM_INT);
        $stmt_update_estado_nomina->execute();
    }
}

// Verificar si la llamada es desde JavaScript
if (isset($_GET['update'])) {
    actualizarEstadoAPagado($con);
    exit(); // Terminar la ejecución después de la actualización
}

// Verificar si es uno de los primeros días del mes
function esPrimerosDiasDelMes()
{
    $current_day = date('d'); // Obtener el día del mes
    return $current_day <= 5; // Considerar los primeros 5 días del mes
}

$sql = "SELECT usuario.id_usuario, usuario.nombre, tipo_cargo.cargo, tipo_cargo.salario_base, solic_prestamo.cant_cuotas,
                tipo_cargo.salario_base < 2600000 AS aplica_aux_transporte,
                tipo_cargo.salario_base * arl.porcentaje / 100 AS precio_arl,
                salud.porcentaje_s, pension.porcentaje_p, solic_prestamo.valor_cuotas, solic_prestamo.monto_solicitado, solic_prestamo.id_estado AS estado_prestamo,
            CASE
                WHEN tipo_cargo.salario_base < 2600000 THEN auxtransporte.valor
                ELSE NULL
            END AS valor_aux_transporte
        FROM 
            usuario
        INNER JOIN 
            tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo
        INNER JOIN 
            arl ON tipo_cargo.id_arl = arl.id_arl
        INNER JOIN 
            nomina ON usuario.id_usuario = nomina.id_usuario
        INNER JOIN 
            salud ON 1 = 1 -- Asume el porcentaje de salud es el mismo para todos
        INNER JOIN 
            pension ON 1 = 1 -- Asume el porcentaje de pensión es el mismo para todos
        LEFT JOIN 
            solic_prestamo ON solic_prestamo.id_prestamo = :id_prestamo
        LEFT JOIN 
            auxtransporte ON auxtransporte.id_auxtransporte = 1
        WHERE 
            usuario.id_usuario = :id_usuario
        GROUP BY 
            usuario.id_usuario, usuario.nombre, tipo_cargo.cargo, tipo_cargo.salario_base, 
            aplica_aux_transporte, precio_arl, salud.porcentaje_s, pension.porcentaje_p, valor_aux_transporte";

$stmt = $con->prepare($sql);    
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    $row = $result[0];
    $salario_diario = $row['salario_base'] / 30;
    $deduccion_salud = $row['salario_base'] * $row['porcentaje_s'] / 100;
    $deduccion_pension = $row['salario_base'] * $row['porcentaje_p'] / 100;
    $valorArl = $row['precio_arl'];
    $valorCuotas = 0;
    $montoSolicitado = 0;
    $estadoPrestamo = $row['estado_prestamo'];
    $cantCuotas = $row['cant_cuotas'];

    // Asignar valores según el estado del préstamo
    if ($estadoPrestamo == 3) { // Estado Aprobado
        $valorCuotas = "Prestamo en Espera";
        $montoSolicitado = "Prestamo en espera";
    } elseif ($estadoPrestamo == 5) { // Estado Aprobado
        $valorCuotas = "Cuota en próxima liquidación";
        $montoSolicitado = $row['monto_solicitado'];
    } elseif ($estadoPrestamo == 8) { // Estado Pagado
        $valorCuotas = $row['valor_cuotas'];
        $montoSolicitado = "Ya ha sido cargado el monto";
    } 

    $valorAuxTransporte = isset($row['valor_aux_transporte']) ? $row['valor_aux_transporte'] : 0;

    // Valores iniciales para la vista
    $dias_trabajados = 0;
    $horas_extras = 0;
    $salario_total = 0;
    $total_deducciones = $valorArl + $deduccion_salud + $deduccion_pension + (is_numeric($valorCuotas) ? $valorCuotas : 0);
    $total_ingresos = 0;
    $valor_neto = 0;
} else {
    die("Error al obtener los detalles del usuario.");
}

$show_success_message = false;
$show_error_message = false;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si es uno de los primeros días del mes
    if (esPrimerosDiasDelMes()) {
        $show_error_message = true;
        $error_message = "No se puede realizar la liquidación en los primeros días del mes.";
    } else {
        // Verificar si ya se ha realizado una liquidación en el mes actual
        $current_mes = date('m');
        $current_anio = date('Y');

        $sql_check_nomina = "SELECT COUNT(*) FROM nomina WHERE id_usuario = :id_usuario AND mes = :mes AND anio = :anio";
        $stmt_check_nomina = $con->prepare($sql_check_nomina);
        $stmt_check_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_check_nomina->bindParam(':mes', $current_mes, PDO::PARAM_INT);
        $stmt_check_nomina->bindParam(':anio', $current_anio, PDO::PARAM_INT);
        $stmt_check_nomina->execute();
        $exists_nomina = $stmt_check_nomina->fetchColumn();

        $sql_check_detalle = "SELECT COUNT(*) FROM detalle WHERE id_usuario = :id_usuario AND mes = :mes AND anio = :anio";
        $stmt_check_detalle = $con->prepare($sql_check_detalle);
        $stmt_check_detalle->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_check_detalle->bindParam(':mes', $current_mes, PDO::PARAM_INT);
        $stmt_check_detalle->bindParam(':anio', $current_anio, PDO::PARAM_INT);
        $stmt_check_detalle->execute();
        $exists_detalle = $stmt_check_detalle->fetchColumn();

        $exists_current_month = $exists_nomina + $exists_detalle;

        $dias_trabajados = isset($_POST['dias_trabajados']) ? (int)$_POST['dias_trabajados'] : 0;
        $horas_extras = isset($_POST['horas_extras']) ? (int)$_POST['horas_extras'] : 0;

        if ($dias_trabajados < 20 || !is_numeric($dias_trabajados)) {
            $show_error_message = true;
            $error_message = "El campo 'Días Trabajados' es obligatorio y debe ser mayor o igual a 20.";
        } elseif ($horas_extras < 0 || !is_numeric($horas_extras)) {
            $show_error_message = true;
            $error_message = "El campo 'Horas Extras' no puede ser negativo.";
        } elseif ($exists_current_month == 0) {
            $salario_total = $salario_diario * $dias_trabajados;
            $valorHorasExtras = $horas_extras * 12300;

            $total_ingresos = $valorAuxTransporte + $valorHorasExtras + (is_numeric($montoSolicitado) ? $montoSolicitado : 0);
            $valor_neto = $salario_total + $total_ingresos - $total_deducciones;

            $fecha_li = date('Y-m-d H:i:s');  // Obtener la fecha y hora actual
            $mes = date('m');  // Obtener el mes actual
            $anio = date('Y');  // Obtener el año actual

            // Actualizar la tabla 'nomina' con los valores calculados, la fecha de liquidación, mes y año
            $sql_update_nomina = "UPDATE nomina 
                                  SET dias_trabajados = :dias_trabajados, horas_extras = :horas_extras, salario_total = :salario_total, 
                                      total_deducciones = :total_deducciones, total_ingresos = :total_ingresos, valor_neto = :valor_neto, 
                                      valor_horas_extras = :valor_horas_extras, fecha_li = :fecha_li, mes = :mes, anio = :anio, salario_base = :salario_base,
                                      deduccion_salud = :deduccion_salud, deduccion_pension = :deduccion_pension, precio_arl = :precio_arl,
                                      valor_cuotas = :valor_cuotas, monto_solicitado = :monto_solicitado, id_estado = 4, nit_empresa = :nit_empresa
                                  WHERE id_usuario = :id_usuario AND id_nomina = :id_nomina";
            $stmt_update_nomina = $con->prepare($sql_update_nomina);
            $stmt_update_nomina->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':dias_trabajados', $dias_trabajados, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':horas_extras', $horas_extras, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':salario_total', $salario_total, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':total_deducciones', $total_deducciones, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':total_ingresos', $total_ingresos, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':valor_neto', $valor_neto, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':valor_horas_extras', $valorHorasExtras, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':fecha_li', $fecha_li, PDO::PARAM_STR);
            $stmt_update_nomina->bindParam(':mes', $mes, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':salario_base', $salario_base, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':deduccion_salud', $deduccion_salud, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':deduccion_pension', $deduccion_pension, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':precio_arl', $valorArl, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':valor_cuotas', $valorCuotas, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':monto_solicitado', $montoSolicitado, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt_update_nomina->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR); // Añadir nit_empresa aquí
            $stmt_update_nomina->execute();

            // Actualizar el estado del préstamo a pagado si se ha liquidado
            if ($estadoPrestamo == 5) {
                $sql_update_prestamo = "UPDATE solic_prestamo SET id_estado = 8 WHERE id_prestamo = :id_prestamo";
                $stmt_update_prestamo = $con->prepare($sql_update_prestamo);
                $stmt_update_prestamo->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
                $stmt_update_prestamo->execute();
            }

            // Restar una cuota del préstamo si el estado es pagado
if ($estadoPrestamo == 8 && $cantCuotas > 0) {
    $cantCuotas -= 1;
    if ($cantCuotas == 0) {
        $estadoPrestamo = 9;
    }

    // Obtener el mes y año actuales
    $current_mes = date('n'); // Número del mes actual (1-12)
    $current_anio = date('Y');

    // Incrementar el mes
    $next_mes = $current_mes + 1;
    $next_anio = $current_anio;

    // Ajustar el año si el mes es mayor a 12
    if ($next_mes > 12) {
        $next_mes = 1;
        $next_anio += 1;
    }

    // Convertir el número del mes al nombre del mes en inglés
    $next_mes_name = date('F', mktime(0, 0, 0, $next_mes, 10));

    $sql_update_prestamo_cuotas = "UPDATE solic_prestamo 
                                   SET cant_cuotas = :cant_cuotas, id_estado = :estado, mes = :mes, anio = :anio 
                                   WHERE id_prestamo = :id_prestamo";
    $stmt_update_prestamo_cuotas = $con->prepare($sql_update_prestamo_cuotas);
    $stmt_update_prestamo_cuotas->bindParam(':cant_cuotas', $cantCuotas, PDO::PARAM_INT);
    $stmt_update_prestamo_cuotas->bindParam(':estado', $estadoPrestamo, PDO::PARAM_INT);
    $stmt_update_prestamo_cuotas->bindParam(':mes', $next_mes_name, PDO::PARAM_STR);
    $stmt_update_prestamo_cuotas->bindParam(':anio', $next_anio, PDO::PARAM_STR);
    $stmt_update_prestamo_cuotas->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
    $stmt_update_prestamo_cuotas->execute();
}

            $show_success_message = true;

            // Incluir el archivo de inserción en detalle
            include 'insertar_detalle.php';

            // Llamar a la función para insertar en detalle
            if (!insertarDetalle($id_usuario, $id_nomina, $fecha_li, $salario_total, $dias_trabajados, $horas_extras, $valorArl, $deduccion_salud, $deduccion_pension, $total_deducciones, $valorHorasExtras, $valorAuxTransporte, $total_ingresos, $valor_neto, $valorCuotas, $nit_empresa, $mes, $anio, $montoSolicitado, $con, $show_error_message, $error_message)) {
                // Si la inserción en detalle falla (ya se ha liquidado este mes), revertir los cambios en la tabla 'nomina'
                $sql_revert_nomina = "UPDATE nomina 
                                      SET dias_trabajados = 0, horas_extras = 0, salario_total = 0, 
                                          total_deducciones = 0, total_ingresos = 0, valor_neto = 0, 
                                          valor_horas_extras = 0, fecha_li = NULL, mes = NULL, anio = NULL, salario_base = :salario_base,
                                          deduccion_salud = 0, deduccion_pension = 0, precio_arl = 0,
                                          valor_cuotas = 0, monto_solicitado = 0, id_estado = 1
                                      WHERE id_usuario = :id_usuario AND id_nomina = :id_nomina";
                $stmt_revert_nomina = $con->prepare($sql_revert_nomina);
                $stmt_revert_nomina->bindParam(':salario_base', $salario_base, PDO::PARAM_INT);
                $stmt_revert_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt_revert_nomina->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
                $stmt_revert_nomina->execute();
            }
        } else {
            $show_error_message = true;
            $error_message = "Ya se ha realizado una liquidación para este mes.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquidación de Nómina</title>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/liquidar.css">
    <link rel="stylesheet" href="../../public/css/bootstrap.min.css">
    <script>
        function calcularNomina() {
            var diasTrabajados = parseInt(document.getElementById('dias_trabajados').value);

            if (diasTrabajados < 20 || diasTrabajados > 30) {
                document.getElementById('error-dias-msg').innerHTML = '<div class="alert alert-danger" role="alert">Días trabajados debe ser entre 20 y 30.</div>';
                diasTrabajados = diasTrabajados < 20 ? 20 : 30;
            } else {
                document.getElementById('error-dias-msg').innerHTML = '';
            }

            var salarioBase = parseFloat(<?php echo $row['salario_base']; ?>);
            var sueldoNeto = Math.floor((salarioBase / 30) * diasTrabajados);

            document.getElementById('salario_total').textContent = sueldoNeto.toLocaleString('es-CO') + ' COP';
            document.getElementById('sueldo_neto').value = sueldoNeto.toLocaleString('es-CO');
        }

        function calcularSalarioExtra() {
            var horasExtras = parseInt(document.getElementById('horas_extras').value);

            if (horasExtras > 48) {
                document.getElementById('error-horas-msg').innerHTML = '<div class="alert alert-danger" role="alert">No puede ingresar más de 48 horas extras.</div>';
                document.getElementById('horas_extras').value = 48;
                horasExtras = 48;
            } else if (horasExtras < 0) {
                document.getElementById('error-horas-msg').innerHTML = '<div class="alert alert-danger" role="alert">Horas extras no puede ser negativo.</div>';
                document.getElementById('horas_extras').value = 0;
                horasExtras = 0;
            } else {
                document.getElementById('error-horas-msg').innerHTML = '';
            }

            var valorHorasExtras = Math.floor(horasExtras * 12300);

            document.getElementById('valor_horas_extras').textContent = valorHorasExtras.toLocaleString('es-CO') + ' COP';

            var auxilioTransporte = parseFloat(<?php echo is_null($row['valor_aux_transporte']) ? '0' : $row['valor_aux_transporte']; ?>);
            var montoSolicitado = parseFloat(<?php echo is_numeric($montoSolicitado) ? $montoSolicitado : 0; ?>);
            var totalIngresos = Math.floor(valorHorasExtras + auxilioTransporte + montoSolicitado);
            var totalDeducciones = parseFloat(<?php echo $valorArl + $deduccion_salud + $deduccion_pension + (is_numeric($valorCuotas) ? $valorCuotas : 0); ?>);

            document.getElementById('total_ingresos').textContent = totalIngresos.toLocaleString('es-CO') + ' COP';
            document.getElementById('total_deducciones').textContent = totalDeducciones.toLocaleString('es-CO') + ' COP';

            var salarioBase = parseFloat(<?php echo $row['salario_base']; ?>);
            var diasTrabajados = parseInt(document.getElementById('dias_trabajados').value);
            var sueldoNeto = Math.floor((salarioBase / 30) * diasTrabajados);

            var salarioNeto = Math.floor(sueldoNeto + totalIngresos - totalDeducciones);

            document.getElementById('valor_neto').textContent = salarioNeto.toLocaleString('es-CO') + ' COP';
        }
    </script>

</head>
</head>

<body onload="document.getElementById('dias_trabajados').focus();">
    <?php include("nav.php") ?>
    <div class="container mt-5">
        <?php if ($show_success_message) : ?>
            <div class="alert alert-success" role="alert">
                La liquidación se ha realizado con éxito.
            </div>
        <?php endif; ?>
        <?php if ($show_error_message) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <?php if (count($result) > 0) : ?>
            <?php foreach ($result as $row) : ?>
                <div class="card bg-dark text-white">
                    <div class="card-header">
                        Cédula <?php echo $row['id_usuario']; ?>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                        <p class="card-text"><strong><?php echo $row['cargo']; ?></strong></p>
                        <p class="card-text"><strong>$</strong> <?php echo number_format($row['salario_base'], 0, '.', ','); ?></p>
                        <?php if ($row['salario_base'] > 2600000) : ?>
                            <div class="alert alert-warning" role="alert">
                                El salario base de este usuario es mayor a 2,600,000 COP.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="nomina-header">
                    <h4>Nómina</h4>
                </div>

                <div class="nomina-dias">
                    <form id="formNomina" method="post">
                        <div class="mb-4">
                            <p><strong>Salario Total: </strong><span id="salario_total"></span></p>
                            <label for="dias_trabajados" class="form-label"><strong>Días Trabajados</strong></label>
                            <input type="number" id="dias_trabajados" name="dias_trabajados" class="form-control" min="5" max="30" required oninput="calcularNomina()">
                            <div id="error-dias-msg" style="color: red;"></div>
                        </div>
                        <div class="mb-4">
                            <label for="horas_extras" class="form-label"><strong>Horas Extras</strong></label>
                            <input type="number" id="horas_extras" name="horas_extras" class="form-control" min="0" max="48" oninput="calcularSalarioExtra()">
                            <div id="error-horas-msg" style="color: red;"></div>
                        </div>
                        <div class="mb-4">
                            <button type="submit" id="liquidar-button" class="btn btn-primary">Liquidar</button>
                        </div>
                    </form>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="nomina-section">
                            <form name="deducciones">
                                <div class="mb-3">
                                    <h2>DEDUCCIONES</h2>
                                    <p><strong>ARL: </strong><span><?php echo number_format($row['precio_arl'], 0, '.', ','); ?> COP</span></p>
                                    <p><strong>Salud: </strong><span><?php echo number_format($deduccion_salud, 0, '.', ','); ?> COP</span></p>
                                    <p><strong>Pensión: </strong><span><?php echo number_format($deduccion_pension, 0, '.', ','); ?> COP</span></p>
                                    <p><strong>Cuotas Préstamo: </strong><span><?php echo is_numeric($valorCuotas) ? number_format($valorCuotas, 0, '.', ',') : $valorCuotas; ?></span></p>
                                    <p><strong>Total Deducciones: </strong><span id="total_deducciones"><?php echo number_format($total_deducciones, 0, '.', ','); ?> COP</span></p>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="nomina-section">
                            <form name="ingresos" id="formIngresosExtras" method="post">
                                <div class="mb-3">
                                    <h2>INGRESOS</h2>
                                    <p><strong>Auxilio de Transporte: </strong><span id="valor_aux_transporte"><?php echo is_null($row['valor_aux_transporte']) ? 'No aplica' : number_format($row['valor_aux_transporte'], 0, '.', ',') . ' COP'; ?></span></p>
                                    <p><strong>Valor Horas Extras: </strong><span id="valor_horas_extras"><?php echo number_format(0, 0, '.', ','); ?> COP</span></p>
                                    <p><strong>Monto Solicitado: </strong><span id="monto_solicitado"><?php echo is_numeric($montoSolicitado) ? number_format($montoSolicitado, 0, '.', ',') : $montoSolicitado; ?> </span></p>
                                    <p><strong>Total Ingresos: </strong><span id="total_ingresos"><?php echo number_format($total_ingresos, 0, '.', ','); ?> COP</span></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="nomina-total">
                        <form>
                            <div class="mb-4">
                                <h2>TOTAL LIQUIDACIÓN</h2>
                                <p><strong>Valor Neto: </strong><span id="valor_neto"><?php echo number_format($valor_neto, 0, '.', ','); ?> COP</span></p>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Este usuario no tiene un registro de nómina.</p>
        <?php endif; ?>
    </div>
</body>

</html>
