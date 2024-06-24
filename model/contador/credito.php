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
$sql = "SELECT sp.id_prestamo, sp.id_usuario, sp.monto_solicitado, sp.cant_cuotas, sp.valor_cuotas, sp.mes, sp.anio, e.estado, sp.id_estado, o.observacion AS observacion
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        function mostrarMotivoRechazo(id_prestamo) {
            $('#rechazoModal').modal('show');
            $('#id_prestamo_rechazo').val(id_prestamo);
        }

        function abrirModalAprobar(id_prestamo) {
            $('#aprobarModal').modal('show');
            $('#id_prestamo_aprobar').val(id_prestamo);
        }

        function actualizarEstado(id_prestamo, nuevo_estado) {
            let motivo_rechazo = null;
            if (nuevo_estado == 7) { // Estado 7 corresponde a "No Aprobar"
                motivo_rechazo = $('#selectMotivoRechazo').val();
                if (!motivo_rechazo) {
                    Swal.fire('Error!', 'Debes seleccionar un motivo de rechazo.', 'error');
                    return;
                }
            }

            $.ajax({
                type: 'POST',
                url: 'actualizar_estado.php',
                data: { id_prestamo: id_prestamo, nuevo_estado: nuevo_estado, motivo_rechazo: motivo_rechazo },
                success: function(response) {
                    Swal.fire('Actualizado!', 'El estado del préstamo ha sido actualizado.', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }

        function aprobarPrestamo() {
            const id_prestamo = $('#id_prestamo_aprobar').val();
            actualizarEstado(id_prestamo, 5);
        }

        function rechazarPrestamo() {
            const id_prestamo = $('#id_prestamo_rechazo').val();
            actualizarEstado(id_prestamo, 7);
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
                            <th scope="col">Documento</th>
                            <th scope="col">Monto Solicitado</th>
                            <th scope="col">Cant. Cuotas</th>
                            <th scope="col">Valor Cuotas</th>
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
                                    <td id="acciones-<?php echo $credito['id_prestamo']; ?>">
                                        <?php if ($credito['id_estado'] == 5 || $credito['id_estado'] == 7 || $credito['id_estado'] == 9 || $credito['id_estado'] == 8) { ?>
                                            <span class="text-success">Solicitud ya procesada</span>
                                        <?php } else { ?>
                                            <button id="btnAprobar-<?php echo $credito['id_prestamo']; ?>" class="btn btn-success" onclick="abrirModalAprobar(<?php echo $credito['id_prestamo']; ?>)">Aprobar</button>
                                            <button id="btnNoAprobar-<?php echo $credito['id_prestamo']; ?>" class="btn btn-danger" onclick="mostrarMotivoRechazo(<?php echo $credito['id_prestamo']; ?>)">No Aprobar</button>
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

    <!-- Modal para aprobar -->
    <div class="modal fade" id="aprobarModal" tabindex="-1" aria-labelledby="aprobarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aprobarModalLabel">Aprobar Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas aprobar este préstamo?</p>
                    <input type="hidden" id="id_prestamo_aprobar">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="aprobarPrestamo()">Aprobar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para rechazo -->
    <div class="modal fade" id="rechazoModal" tabindex="-1" aria-labelledby="rechazoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechazoModalLabel">Motivo de Rechazo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_prestamo_rechazo">
                    <select id="selectMotivoRechazo" class="form-select" required>
                        <option value="" disabled selected>Seleccione un motivo</option>
                        <?php
                        $observaciones = $con->query("SELECT * FROM observaciones WHERE id_observacion");
                        while ($obs = $observaciones->fetch()) {
                            echo '<option value="'.$obs['id_observacion'].'">'.$obs['observacion'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="rechazarPrestamo()">Enviar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.2.3/js/bootstrap.min.js"></script>
</body>

</html>
