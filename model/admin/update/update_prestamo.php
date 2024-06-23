<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}
require_once("../../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$id_prestamo = $_GET['id'];

// Obtener los datos actuales del préstamo
$stmt = $con->prepare("SELECT * FROM solic_prestamo WHERE id_prestamo = ?");
$stmt->execute([$id_prestamo]);
$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST["update"])) {
    $id_estado = $_POST['id_estado'];

    // Obtener el mes y año actuales
    $mes = date('m');
    $anio = date('Y');

    // Convertir el número del mes al nombre del mes en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $nombre_mes = $meses[intval($mes)];

    $stmtUpdate = $con->prepare("
        UPDATE solic_prestamo 
        SET mes = ?, anio = ?, id_estado = ?
        WHERE id_prestamo = ?
    ");
    $stmtUpdate->execute([$nombre_mes, $anio, $id_estado, $id_prestamo]);
    echo '<script>alert("Préstamo actualizado correctamente.");</script>';
    echo '<script>window.close();</script>';
    exit();
}

// Obtener los estados disponibles (id_estado 3, 5 y 7)
$stmtEstados = $con->prepare("SELECT id_estado, estado FROM estado WHERE id_estado IN (3, 5, 7)");
$stmtEstados->execute();
$estados = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);

$id_usuario = $prestamo['id_usuario'];

// Obtener el salario del usuario
$stmtSalario = $con->prepare("SELECT tc.salario_base 
                              FROM usuario u 
                              INNER JOIN tipo_cargo tc ON u.id_tipo_cargo = tc.id_tipo_cargo 
                              WHERE u.id_usuario = :idUsuario");
$stmtSalario->bindParam(':idUsuario', $id_usuario, PDO::PARAM_INT);
$stmtSalario->execute();
$salarioUsuario = $stmtSalario->fetch(PDO::FETCH_ASSOC)['salario_base'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Préstamo</title>
    <link rel="stylesheet" href="../../css/presta.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
</head>
<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h4>Actualizar Préstamo</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" name="frm_actualizar" method="POST" autocomplete="off" id="actualizarForm">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">ID Préstamo</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_prestamo" value="<?php echo $prestamo['id_prestamo']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Monto Solicitado</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="monto_solicitado" value="<?php echo $prestamo['monto_solicitado']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Cantidad de Cuotas</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="cant_cuotas" value="<?php echo $prestamo['cant_cuotas']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Valor de Cuotas</label>
                        <div class="col-lg-9">
                            <span id="valorCuotas" class="form-control"><?php echo number_format($prestamo['valor_cuotas'], 2, ',', '.'); ?></span>
                            <input type="hidden" name="valor_cuotas" value="<?php echo $prestamo['valor_cuotas']; ?>">
                        </div>
                    </div>
                    <input type="hidden" name="mes" value="<?php echo $nombre_mes; ?>">
                    <input type="hidden" name="anio" value="<?php echo $anio; ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Estado</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_estado" required>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado['id_estado']; ?>" <?php echo ($estado['id_estado'] == $prestamo['id_estado']) ? 'selected' : ''; ?>>
                                        <?php echo $estado['estado']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input name="update" type="submit" class="btn btn-primary" value="Actualizar">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        const salarioUsuario = <?php echo json_encode($salarioUsuario); ?>;
    </script>
</body>
</html>
