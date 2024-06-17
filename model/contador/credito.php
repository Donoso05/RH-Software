<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
$con = $db->conectar();

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta SQL para obtener los datos de la tabla solic_prestamo
$sql = "SELECT sp.id_usuario, sp.monto_solicitado, sp.cant_cuotas, sp.valor_cuotas, sp.mes, sp.anio, e.estado, sp.id_estado, o.observacion AS observacion
        FROM solic_prestamo sp
        LEFT JOIN observaciones o ON sp.motivo_rechazo = o.id_observacion
        JOIN estado e ON sp.id_estado = e.id_estado";
$stmt = $con->prepare($sql);
$stmt->execute();

$creditos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Préstamo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .table thead {
            background-color: #343a40;
            color: white;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function mostrarMotivoRechazo(id_prestamo) {
            document.getElementById('motivoRechazoSelect-' + id_prestamo).style.display = 'block';
            document.getElementById('btnAprobar-' + id_prestamo).disabled = true;
            document.getElementById('btnNoAprobar-' + id_prestamo).style.display = 'none';
        }

        function actualizarEstado(id_prestamo, nuevo_estado) {
            let motivo_rechazo = null;
            if (nuevo_estado == 7) { // Estado 7 corresponde a "No Aprobar"
                const selectElement = document.getElementById('selectMotivoRechazo-' + id_prestamo);
                motivo_rechazo = selectElement.value;
                if (!motivo_rechazo) {
                    alert("Debe seleccionar un motivo de rechazo.");
                    return;
                }
            }

            if (confirm('¿Estás seguro de que deseas cambiar el estado de este préstamo?')) {
                $.ajax({
                    type: 'POST',
                    url: 'actualizar_estado.php',
                    data: { id_prestamo: id_prestamo, nuevo_estado: nuevo_estado, motivo_rechazo: motivo_rechazo },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            }
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid">
        <h3 class="text-center text-secondary my-4">Solicitud de Préstamo</h3>
        <div class="col-12 p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID Usuario</th>
                            <th scope="col">Monto Solicitado</th>
                            <th scope="col">Cantidad de Cuotas</th>
                            <th scope="col">Valor de Cuotas</th>
                            <th scope="col">Mes</th>
                            <th scope="col">Año</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Observaciones</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($creditos)): ?>
                            <?php foreach ($creditos as $credito): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($credito['id_usuario']); ?></td>
                                    <td><?php echo number_format($credito['monto_solicitado'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($credito['cant_cuotas']); ?></td>
                                    <td><?php echo number_format($credito['valor_cuotas'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($credito['mes']); ?></td>
                                    <td><?php echo htmlspecialchars($credito['anio']); ?></td>
                                    <td><?php echo htmlspecialchars($credito['estado']); ?></td>
                                    <td><?php echo htmlspecialchars($credito['observacion']); ?></td>
                                    <td id="acciones-<?php echo $credito['id_usuario']; ?>">
                                        <?php if ($credito['id_estado'] == 5 || $credito['id_estado'] == 7 || $credito['id_estado'] == 9) { ?>
                                            <span class="text-success">Solicitud ya procesada</span>
                                        <?php } else { ?>
                                            <button id="btnAprobar-<?php echo $credito['id_usuario']; ?>" class="btn btn-success" onclick="actualizarEstado(<?php echo $credito['id_usuario']; ?>, 5)">Aprobar</button>
                                            <button id="btnNoAprobar-<?php echo $credito['id_usuario']; ?>" class="btn btn-danger" onclick="mostrarMotivoRechazo(<?php echo $credito['id_usuario']; ?>)">No Aprobar</button>
                                            <div id="motivoRechazoSelect-<?php echo $credito['id_usuario']; ?>" style="display: none;">
                                                <select id="selectMotivoRechazo-<?php echo $credito['id_usuario']; ?>" class="form-select mt-2">
                                                    <option value="">Seleccione un motivo</option>
                                                    <?php
                                                    $observaciones = $con->query("SELECT * FROM observaciones where id_observacion > 5 ");
                                                    while ($obs = $observaciones->fetch()) {
                                                        echo '<option value="'.$obs['id_observacion'].'">'.$obs['observacion'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <button class="btn btn-primary mt-2" onclick="actualizarEstado(<?php echo $credito['id_usuario']; ?>, 7)">Enviar</button>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron registros</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
