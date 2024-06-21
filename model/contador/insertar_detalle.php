<?php
function insertarDetalle($id_usuario, $id_nomina, $fecha_li, $salario_total, $dias_trabajados, $horas_extras,  $valorarl, $deduccion_salud, $deduccion_pension, $total_deducciones, $valorHorasExtras, $valorAuxTransporte, $total_ingresos, $valor_neto, $valor_cuotas, $nit_empresa, $mes, $anio, $monto_solicitado, $con, &$show_error_message, &$error_message) {
    // Obtener el mes y el año de la fecha de liquidación
    $mes = date('m', strtotime($fecha_li));
    $anio = date('Y', strtotime($fecha_li));
    
    // Verificar si ya existe una liquidación para el usuario en el mes y año especificados
    $sql_check = "SELECT COUNT(*) FROM detalle WHERE id_usuario = :id_usuario AND MONTH(fecha_li) = :mes AND YEAR(fecha_li) = :anio";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_check->bindParam(':mes', $mes, PDO::PARAM_INT);
    $stmt_check->bindParam(':anio', $anio, PDO::PARAM_INT);
    $stmt_check->execute();
    $exists = $stmt_check->fetchColumn();

    $id_estado = 4; // Suponiendo que el estado inicial es 4

    $sql_insert_detalle = "INSERT INTO detalle (id_usuario, id_nomina, fecha_li, salario_total, dias_trabajados, horas_extras,  precio_arl, deduccion_salud, deduccion_pension, total_deducciones, valor_horas_extras, aux_transporte_valor, total_ingresos, valor_neto, valor_cuotas, nit_empresa, mes, anio, monto_solicitado) 
                           VALUES (:id_usuario, :id_nomina, :fecha_li, :salario_total, :dias_trabajados, :horas_extras, :precio_arl, :deduccion_salud, :deduccion_pension, :total_deducciones, :valor_horas_extras, :aux_transporte_valor, :total_ingresos, :valor_neto, :valor_cuotas,:nit_empresa, :mes, :anio, :monto_solicitado)";

    $stmt_insert_detalle = $con->prepare($sql_insert_detalle);
    $stmt_insert_detalle->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_insert_detalle->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
    $stmt_insert_detalle->bindParam(':fecha_li', $fecha_li, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':salario_total', $salario_total, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':dias_trabajados', $dias_trabajados, PDO::PARAM_INT);
    $stmt_insert_detalle->bindParam(':horas_extras', $horas_extras, PDO::PARAM_INT);
    $stmt_insert_detalle->bindParam(':precio_arl', $valorarl, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':deduccion_salud', $deduccion_salud, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':deduccion_pension', $deduccion_pension, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':valor_cuotas', $valor_cuotas, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':total_deducciones', $total_deducciones, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':valor_horas_extras', $valorHorasExtras, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':aux_transporte_valor', $valorAuxTransporte, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':total_ingresos', $total_ingresos, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':valor_neto', $valor_neto, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam('nit_empresa',$nit_empresa, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam('mes',$mes, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam('anio',$anio, PDO::PARAM_STR);
    $stmt_insert_detalle->bindParam(':monto_solicitado', $monto_solicitado, PDO::PARAM_STR);
    $stmt_insert_detalle->execute();
    return true; // Indicar que la inserción se realizó correctamente
}
?>
