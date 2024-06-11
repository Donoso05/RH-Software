<?php
require '../../conexion/conexion.php';

$database = new Database();
$con = $database->conectar();

$id_nomina = isset($_POST['id_nomina']) ? (int)$_POST['id_nomina'] : 0;
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
$salario_total = isset($_POST['salario_total_hidden']) ? (float)str_replace(',', '', $_POST['salario_total_hidden']) : 0;
$dias_trabajados = isset($_POST['dias_trabajados_hidden']) ? (int)$_POST['dias_trabajados_hidden'] : 0;
$valor_horas_extras = isset($_POST['valor_horas_extras_hidden']) ? (float)str_replace(',', '', $_POST['valor_horas_extras_hidden']) : 0;
$total_deducciones = isset($_POST['total_deducciones_hidden']) ? (float)str_replace(',', '', $_POST['total_deducciones_hidden']) : 0;
$total_ingresos = isset($_POST['total_ingresos_hidden']) ? (float)str_replace(',', '', $_POST['total_ingresos_hidden']) : 0;
$valor_neto = isset($_POST['valor_neto_hidden']) ? (float)str_replace(',', '', $_POST['valor_neto_hidden']) : 0;
$fecha_li = date('Y-m-d H:i:s');

try {
    $sql_update_nomina = "UPDATE nomina SET 
                            salario_total = :salario_total,
                            dias_trabajados = :dias_trabajados,
                            valor_horas_extras = :valor_horas_extras,
                            total_deducciones = :total_deducciones,
                            total_ingresos = :total_ingresos,
                            valor_neto = :valor_neto,
                            fecha_li = :fecha_li
                          WHERE id_nomina = :id_nomina AND id_usuario = :id_usuario";
                          
    $stmt_update_nomina = $con->prepare($sql_update_nomina);
    $stmt_update_nomina->bindParam(':salario_total', $salario_total);
    $stmt_update_nomina->bindParam(':dias_trabajados', $dias_trabajados);
    $stmt_update_nomina->bindParam(':valor_horas_extras', $valor_horas_extras);
    $stmt_update_nomina->bindParam(':total_deducciones', $total_deducciones);
    $stmt_update_nomina->bindParam(':total_ingresos', $total_ingresos);
    $stmt_update_nomina->bindParam(':valor_neto', $valor_neto);
    $stmt_update_nomina->bindParam(':fecha_li', $fecha_li);
    $stmt_update_nomina->bindParam(':id_nomina', $id_nomina, PDO::PARAM_INT);
    $stmt_update_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

    $stmt_update_nomina->execute();

    echo "Nómina guardada exitosamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
