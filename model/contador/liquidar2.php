<?php
require '../../conexion/conexion.php';

$database = new Database();
$con = $database->conectar();

$id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;

// Obtener valores válidos para claves externas
$sql_salud = "SELECT id_salud FROM salud LIMIT 1";
$stmt_salud = $con->prepare($sql_salud);
$stmt_salud->execute();
$id_salud = $stmt_salud->fetchColumn();

$sql_pension = "SELECT id_pension FROM pension LIMIT 1";
$stmt_pension = $con->prepare($sql_pension);
$stmt_pension->execute();
$id_pension = $stmt_pension->fetchColumn();

$sql_arl = "SELECT id_arl FROM arl LIMIT 1";
$stmt_arl = $con->prepare($sql_arl);
$stmt_arl->execute();
$id_arl = $stmt_arl->fetchColumn();

$sql_auxtransporte = "SELECT id_auxtransporte FROM auxtransporte LIMIT 1";
$stmt_auxtransporte = $con->prepare($sql_auxtransporte);
$stmt_auxtransporte->execute();
$id_auxtransporte = $stmt_auxtransporte->fetchColumn();

// Verificar si el usuario ya tiene un registro en la tabla 'nomina'
$sql_check_nomina = "SELECT COUNT(*) FROM nomina WHERE id_usuario = :id_usuario";
$stmt_check_nomina = $con->prepare($sql_check_nomina);
$stmt_check_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_check_nomina->execute();
$user_in_nomina = $stmt_check_nomina->fetchColumn();

if ($user_in_nomina == 0) {
    // Insertar un nuevo registro en la tabla 'nomina' con valores predeterminados
    $sql_insert_nomina = "INSERT INTO nomina (id_usuario, dias_trabajados, horas_extras, sueldo_neto, total_extras, fecha, id_salud, id_pension, id_arl, id_auxtransporte) 
                          VALUES (:id_usuario, 0, 0, 0, 0, NOW(), :id_salud, :id_pension, :id_arl, :id_auxtransporte)";
    $stmt_insert_nomina = $con->prepare($sql_insert_nomina);
    $stmt_insert_nomina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':id_salud', $id_salud, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':id_pension', $id_pension, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':id_arl', $id_arl, PDO::PARAM_INT);
    $stmt_insert_nomina->bindParam(':id_auxtransporte', $id_auxtransporte, PDO::PARAM_INT);
    $stmt_insert_nomina->execute();
}

$sueldo_neto = 0;
$total_extras = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dias_trabajados = isset($_POST['dias_trabajados']) ? (int)$_POST['dias_trabajados'] : 0;
    $horas_extras = isset($_POST['horas_extras']) ? (int)$_POST['horas_extras'] : 0;
    $salario_base = isset($_POST['salario_base']) ? (float)$_POST['salario_base'] : 0;
    $sueldo_neto = isset($_POST['sueldo_neto']) ? (float)$_POST['sueldo_neto'] : 0;
    $total_extras = isset($_POST['total_extras']) ? (float)$_POST['total_extras'] : 0;

    $totalh_extras = $horas_extras * 12300;

    $sql_update = "UPDATE nomina SET dias_trabajados = :dias_trabajados, horas_extras = :horas_extras, 
                   salario_total = :sueldo_neto, valor_horas_extras = :totalh_extras, 
                   total_ingresos = :total_extras, valor_neto = :valor_neto, fecha = NOW() 
                   WHERE id_usuario = :id_usuario";
    $stmt_update = $con->prepare($sql_update);
    $stmt_update->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_update->bindParam(':dias_trabajados', $dias_trabajados, PDO::PARAM_INT);
    $stmt_update->bindParam(':horas_extras', $horas_extras, PDO::PARAM_INT);
    $stmt_update->bindParam(':sueldo_neto', $sueldo_neto, PDO::PARAM_STR);
    $stmt_update->bindParam(':totalh_extras', $totalh_extras, PDO::PARAM_STR);
    $stmt_update->bindParam(':total_extras', $total_extras, PDO::PARAM_STR);
    $stmt_update->bindParam(':valor_neto', $sueldo_neto, PDO::PARAM_STR);
    $stmt_update->execute();
}

$sql = "SELECT usuario.id_usuario, usuario.nombre, tipo_cargo.cargo, tipo_cargo.salario_base, 
               tipo_cargo.salario_base < 2600000 AS aplica_aux_transporte,
               tipo_cargo.salario_base * arl.porcentaje / 100 AS precio_arl,
               salud.porcentaje_s, pension.porcentaje_p, solic_prestamo.valor_cuotas,
               CASE
                   WHEN tipo_cargo.salario_base < 2600000 THEN auxtransporte.valor
                   ELSE NULL
               END AS valor_aux_transporte
        FROM usuario
        INNER JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo
        INNER JOIN arl ON tipo_cargo.id_arl = arl.id_arl
        LEFT JOIN nomina ON usuario.id_usuario = nomina.id_usuario
        INNER JOIN salud ON tipo_cargo.id_salud = salud.id_salud
        INNER JOIN pension ON tipo_cargo.id_pension = pension.id_pension
        LEFT JOIN solic_prestamo ON usuario.id_usuario = solic_prestamo.id_usuario
        LEFT JOIN auxtransporte ON auxtransporte.id_auxtransporte = 1
        WHERE usuario.id_usuario = :id_usuario";

$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    $row = $result[0];
    $salario_diario = $row['salario_base'] / 30;
    $deduccion_salud = $row['salario_base'] * $row['porcentaje_s'] / 100;
    $deduccion_pension = $row['salario_base'] * $row['porcentaje_p'] ;
    $valorArl = $row['precio_arl'];
    $valorCuotas = isset($row['valor_cuotas']) ? $row['valor_cuotas'] : 0;
    $valorAuxTransporte = isset($row['valor_aux_transporte']) ? $row['valor_aux_transporte'] : 0;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquidación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/liquidar.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container mt-5">
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
                    <form id="formNomina">
                        <div class="mb-4">
                            <p><strong>Salario Total: </strong><span id="salario_total"></span></p>
                            <label for="dias_trabajados" class="form-label"><strong>Días Trabajados</strong></label>
                            <input type="number" id="dias_trabajados" name="dias_trabajados" class="form-control" min="1" max="30" required oninput="calcularNomina()">
                            <div id="error-msg" style="color: red;"></div>
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
                                    <p><strong>Cuotas Préstamo: </strong><span><?php echo $mensaje_prestamo; ?></span></p>
                                    <p><strong>Total Deducciones: </strong><span id="total_deducciones"></span></p>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="nomina-section">
                            <form name="ingresos" id="formIngresosExtras">
                                <div class="mb-3">
                                    <h2>INGRESOS</h2>
                                    <p><strong>Auxilio de Transporte: </strong><span id="valor_aux_transporte"><?php echo is_null($row['valor_aux_transporte']) ? 'No aplica' : number_format($row['valor_aux_transporte'], 0, '.', ',') . ' COP'; ?></span></p>
                                    <label for="horas_extras" class="form-label"><strong>Horas Extras:</strong></label>
                                    <input type="number" id="horas_extras" name="horas_extras" class="form-control" min="0" oninput="calcularSalarioExtra()">
                                    <p><strong>Valor Horas Extras: </strong><span id="valor_horas_extras"></span></p>
                                    <p><strong>Total Ingresos: </strong><span id="total_ingresos"></span></p>
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
                                <p><strong>Valor Neto: </strong><span id="valor_neto"></span></p>
                                <button type="button" id="liquidar-button" class="btn btn-primary">Liquidar</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Este usuario no tiene un registro de nómina.</p>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('calcularBtn').addEventListener('click', function() {
    const diasTrabajados = parseFloat(document.getElementById('dias_trabajados').value);
    const horasExtras = parseFloat(document.getElementById('horas_extras').value);
    const salarioBase = parseFloat(document.getElementById('salario_base').value.replace(/,/g, ''));
    
    const salarioDiario = salarioBase / 30;
    const sueldoNeto = salarioDiario * diasTrabajados;
    const totalExtras = horasExtras * 12300;

    document.getElementById('sueldo_neto').value = sueldoNeto.toFixed(2);
    document.getElementById('total_extras').value = totalExtras.toFixed(2);
});

    </script>
</body>

</html>
