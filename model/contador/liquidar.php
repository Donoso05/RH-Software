<?php
require '../../conexion/conexion.php';

$database = new Database();
$con = $database->conectar();

$id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : 0;

// Verificar si el usuario está en la tabla nómina
$sql_check_nomina = "SELECT * FROM nomina WHERE id_usuario = :id_usuario";
$stmt_check = $con->prepare($sql_check_nomina);
$stmt_check->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_check->execute();
$result_check = $stmt_check->fetchAll(PDO::FETCH_ASSOC);

if (count($result_check) == 0) {
    // Si no está en la tabla nómina, verificar si el usuario existe en la tabla usuarios
    $sql_check_user = "SELECT * FROM usuario WHERE id_usuario = :id_usuario";
    $stmt_user = $con->prepare($sql_check_user);
    $stmt_user->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_user->execute();
    $result_user = $stmt_user->fetchAll(PDO::FETCH_ASSOC);

    if (count($result_user) == 0) {
        // Si el usuario no existe, manejar el error
        echo "El usuario no está registrado.";
        exit;
    } else {
        // Si el usuario existe en la tabla usuarios, insertar en la tabla nómina
        $id_salud = 1; // Ajustar el valor según sea necesario
        $id_pension = 1; // Ajustar el valor según sea necesario
        $id_auxtransporte = 1; // Ajustar el valor según sea necesario y verificar que exista en la tabla auxtransporte

        // Verificar que id_auxtransporte existe en la tabla auxtransporte
        $sql_check_auxtransporte = "SELECT * FROM auxtransporte WHERE id_auxtransporte = :id_auxtransporte";
        $stmt_auxtransporte = $con->prepare($sql_check_auxtransporte);
        $stmt_auxtransporte->bindParam(':id_auxtransporte', $id_auxtransporte, PDO::PARAM_INT);
        $stmt_auxtransporte->execute();
        $result_auxtransporte = $stmt_auxtransporte->fetchAll(PDO::FETCH_ASSOC);

        if (count($result_auxtransporte) == 0) {
            echo "El id_auxtransporte no es válido.";
            exit;
        }

        $sql_insert_nomina = "INSERT INTO nomina (id_usuario, id_salud, id_pension, id_auxtransporte) VALUES (:id_usuario, :id_salud, :id_pension, :id_auxtransporte)";
        $stmt_insert = $con->prepare($sql_insert_nomina);
        $stmt_insert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_salud', $id_salud, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_pension', $id_pension, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_auxtransporte', $id_auxtransporte, PDO::PARAM_INT);

        if ($stmt_insert->execute()) {
            header("Location: liquidar.php?id_usuario=$id_usuario");
            exit;
        } else {
            echo "Error al crear el registro en nómina: " . $con->errorInfo()[2];
            exit;
        }
    }
}

// Continuar con el proceso normal de obtención de datos
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
        INNER JOIN nomina ON usuario.id_usuario = nomina.id_usuario
        INNER JOIN salud ON nomina.id_salud = salud.id_salud
        INNER JOIN pension ON nomina.id_pension = pension.id_pension
        LEFT JOIN solic_prestamo ON usuario.id_usuario = solic_prestamo.id_usuario
        LEFT JOIN auxtransporte ON auxtransporte.id_auxtransporte = 1
        WHERE usuario.id_usuario = :id_usuario";

$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$salario_diario = 0;
$mensaje_prestamo = "sin préstamos";
if (count($result) > 0) {
    $salario_diario = $result[0]['salario_base'] / 30;
    $porcentaje_s = $result[0]['porcentaje_s'];
    $porcentaje_p = $result[0]['porcentaje_p'];
    if (!is_null($result[0]['valor_cuotas'])) {
        $mensaje_prestamo = number_format($result[0]['valor_cuotas'], 0, '.', ',');
    }
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
                <script>
                    const salarioDiario = <?php echo $salario_diario; ?>;
                    const porcentajeSalud = <?php echo $porcentaje_s; ?>;
                    const porcentajePension = <?php echo $porcentaje_p; ?>;
                    const precioArl = <?php echo $row['precio_arl']; ?>;
                    let valorCuotas = <?php echo is_null($row['valor_cuotas']) ? 'null' : $row['valor_cuotas']; ?>;
                    const valorAuxTransporte = <?php echo isset($row['valor_aux_transporte']) ? $row['valor_aux_transporte'] : 0; ?>;
                    let ingresosExtras = 0;

                    function calcularNomina() {
                        const diasTrabajados = parseInt(document.getElementById('dias_trabajados').value);

                        if (diasTrabajados > 30) {
                            document.getElementById('salario_total').innerText = "0 COP";
                            document.getElementById('porcentaje_p').innerText = "0 COP";
                            document.getElementById('porcentaje_s').innerText = "0 COP";
                            document.getElementById('precio_arl').innerText = "0 COP";
                            document.getElementById('total_deducciones').innerText = "0 COP";
                        } else {
                            const salarioTotal = diasTrabajados * salarioDiario;
                            const precioSalud = salarioTotal * porcentajeSalud / 100;
                            const precioPension = salarioTotal * porcentajePension;
                            const totalDeducciones = precioArl + precioSalud + precioPension + (valorCuotas ? parseFloat(valorCuotas) : 0);

                            document.getElementById('salario_total').innerText = salarioTotal.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            document.getElementById('porcentaje_s').innerText = precioSalud.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            document.getElementById('porcentaje_p').innerText = precioPension.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            document.getElementById('total_deducciones').innerText = totalDeducciones.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }

                        actualizarTotalLiquidacion();
                    }

                    function calcularIngresosExtras() {
                        const horasExtras = document.getElementById('horas_extras').value;

                        if (horasExtras > 48 || horasExtras < 0) {
                            document.getElementById('ingresos_extras').innerText = "0 COP";
                            document.getElementById('ingresos_totales').innerText = "0 COP";
                            document.getElementById('valor_aux_transporte').innerText = "0 COP";
                        } else {
                            ingresosExtras = horasExtras * 12300;
                            const ingresosConAuxilio = ingresosExtras + (valorAuxTransporte ? parseFloat(valorAuxTransporte) : 0);

                            document.getElementById('ingresos_extras').innerText = ingresosExtras.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });

                            document.getElementById('valor_aux_transporte').innerText = valorAuxTransporte.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });

                            document.getElementById('ingresos_totales').innerText = ingresosConAuxilio.toLocaleString('es-ES', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }

                    }

                    function actualizarTotalLiquidacion() {
    // Obtener los elementos del DOM
    const totalDeduccionesElement = document.getElementById('total_deducciones');
    const ingresosTotalesElement = document.getElementById('ingresos_totales');
    const totalLiquidacionElement = document.getElementById('total_liquidacion');

    if (totalDeduccionesElement && ingresosTotalesElement && totalLiquidacionElement) {
        // Obtener y parsear los valores
        const totalDeducciones = parseFloat(totalDeduccionesElement.innerText.replace(/[^0-9.-]+/g, ""));
        const ingresosTotales = parseFloat(ingresosTotalesElement.innerText.replace(/[^0-9.-]+/g, ""));

        // Verificar que los valores obtenidos sean válidos
        if (!isNaN(totalDeducciones) && !isNaN(ingresosTotales)) {
            // Calcular el total de liquidación
            const totalLiquidacion = totalDeducciones + ingresosTotales;

            // Mostrar el resultado formateado
            totalLiquidacionElement.innerText = totalLiquidacion.toLocaleString('es-ES', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        } else {
            console.error('Los valores de deducciones o ingresos no son válidos.');
        }
    } else {
        console.error('No se encontraron los elementos necesarios en el DOM.');
    }
}

                </script>

                <div class="nomina-header">
                    <h4>Nómina</h4>
                    <form>
                        <div class="mb-3">
                            <label for="dias_trabajados" class="form-label"><strong>Días Trabajados</strong></label>
                            <input type="number" id="dias_trabajados" name="dias_trabajados" class="form-control" min="1" max="30" required oninput="calcularNomina()">
                        </div>
                    </form>
                    <div>
                        <p><strong>Salario Total: </strong><span id="salario_total"></span></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="nomina-section">
                            <form name="deducciones">
                                <div class="mb-3">
                                    <h2>DEDUCCIONES</h2>
                                    <p><strong>ARL: </strong><span><?php echo number_format($row['precio_arl'], 0, '.', ','); ?> COP</span></p>
                                    <p><strong>Salud: </strong><span id="porcentaje_s"></span></p>
                                    <p><strong>Pensión: </strong><span id="porcentaje_p"></span></p>
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
                                    <label for="horas_extras" class="form-label">Horas Extras (máximo 48 horas)</label>
                                    <input type="number" id="horas_extras" name="horas_extras" class="form-control" min="0" max="48" required oninput="calcularIngresosExtras()">
                                    <p><strong>Ingresos Extras: </strong><span id="ingresos_extras"></span></p>
                                    <p><strong>Auxilio de Transporte: </strong><span id="valor_aux_transporte"></span></p>
                                    <p><strong>Ingresos Totales: </strong><span id="ingresos_totales"></span></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Nueva sección para Total de Liquidación -->
                <div class="liquidacion-section mt-4">
                    <h4>Total de Liquidación</h4>
                    <p><strong>Total: </strong><span id="total_liquidacion"></span></p>
                    <button class="btn btn-primary" onclick="alert('Liquidación procesada!')">Liquidar</button>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>
