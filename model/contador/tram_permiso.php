<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

if (isset($_POST['id_permiso']) && isset($_POST['accion'])) {
    $id_permiso = $_POST['id_permiso'];
    $accion = $_POST['accion'];
    $motivo_rechazo = isset($_POST['motivo_rechazo']) ? $_POST['motivo_rechazo'] : null;

    // Determinar el nuevo estado
    $nuevoEstado = ($accion === 'aprobar') ? 5 : 7;

    // Actualizar el estado y el motivo de rechazo si es necesario
    $updateSQL = $con->prepare("UPDATE tram_permiso SET id_estado = :nuevo_estado, motivo_rechazo = :motivo_rechazo WHERE id_permiso = :id_permiso");
    $updateSQL->bindParam(':nuevo_estado', $nuevoEstado, PDO::PARAM_INT);
    $updateSQL->bindParam(':motivo_rechazo', $motivo_rechazo, PDO::PARAM_STR);
    $updateSQL->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
    if ($updateSQL->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/tram.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .table-container {
            overflow-x: auto;
        }
        .table th, .table td {
            white-space: nowrap;
        }
    </style>
    <script>
        function abrirModalAprobar(id_permiso) {
            $('#aprobarModal').modal('show');
            $('#id_permiso_aprobar').val(id_permiso);
        }

        function abrirModalRechazar(id_permiso) {
            $('#rechazoModal').modal('show');
            $('#id_permiso_rechazo').val(id_permiso);
        }

        function cambiarEstadoPermiso(id_permiso, accion) {
            let motivo_rechazo = null;
            if (accion === 'no_aprobar') {
                motivo_rechazo = $('#selectMotivoRechazo').val();
                if (!motivo_rechazo) {
                    Swal.fire('Error!', 'Debes seleccionar un motivo de rechazo.', 'error');
                    return;
                }
            }

            $.ajax({
                type: 'POST',
                url: '',
                data: { id_permiso: id_permiso, accion: accion, motivo_rechazo: motivo_rechazo },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Actualizado!', 'El estado del permiso ha sido actualizado.', 'success');
                        document.getElementById('acciones-' + id_permiso).innerHTML = '<span class="text-success">Permiso ya procesado</span>';
                        $('#aprobarModal').modal('hide');
                        $('#rechazoModal').modal('hide');
                    } else {
                        Swal.fire('Error!', 'Hubo un error al actualizar el estado del permiso.', 'error');
                    }
                }
            });
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid">
        <h3 class="text-center text-secondary my-4">Trámite Permiso</h3>
        <div class="col-12 p-4">
            <div class="card">
                
                </div>
                <div class="card-body table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Documento</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Archivo</th>
                                <th scope="col">Tipo Permiso</th>
                                <th scope="col">Fecha Inicio</th>
                                <th scope="col">Fecha Fin</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $consulta = "SELECT tram_permiso.id_permiso, usuario.id_usuario, usuario.nombre, tipo_permiso.tipo_permiso, tram_permiso.fecha_inicio, tram_permiso.fecha_fin, estado.estado, tram_permiso.id_estado, tram_permiso.descripcion, tram_permiso.incapacidad
                                FROM tram_permiso
                                INNER JOIN usuario ON tram_permiso.id_usuario = usuario.id_usuario
                                INNER JOIN tipo_permiso ON tram_permiso.id_tipo_permiso = tipo_permiso.id_tipo_permiso
                                INNER JOIN estado ON tram_permiso.id_estado = estado.id_estado";
                            $resultado = $con->query($consulta);

                            if ($resultado->rowCount() > 0) {
                                while ($fila = $resultado->fetch()) {
                            ?>
                                    <tr>
                                        <td><?php echo $fila["id_usuario"]; ?></td>
                                        <td><?php echo $fila["nombre"]; ?></td>
                                        <td><?php echo htmlspecialchars($fila["descripcion"]); ?></td>
                                        <td><a href="<?php echo htmlspecialchars($fila["incapacidad"]); ?>" target="_blank">Ver Archivo</a></td>
                                        <td><?php echo $fila["tipo_permiso"]; ?></td>
                                        <td><?php echo $fila["fecha_inicio"]; ?></td>
                                        <td><?php echo $fila["fecha_fin"]; ?></td>
                                        <td><?php echo $fila["estado"]; ?></td>
                                        <td id="acciones-<?php echo $fila['id_permiso']; ?>">
                                            <?php if ($fila["id_estado"] == 5 || $fila["id_estado"] == 7 ) { ?>
                                                <span class="text-success">Permiso ya procesado</span>
                                            <?php } else { ?>
                                                <div class="text-center">
                                                    <div class="d-flex justify-content-start">
                                                        <button onclick="abrirModalAprobar(<?php echo $fila['id_permiso']; ?>)" class="btn btn-success ms-2">
                                                            Aprobar
                                                        </button>
                                                        <button onclick="abrirModalRechazar(<?php echo $fila['id_permiso']; ?>)" class="btn btn-danger ms-2">
                                                            No Aprobar
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="9" class="text-center">No se encontraron registros</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para aprobar -->
    <div class="modal fade" id="aprobarModal" tabindex="-1" aria-labelledby="aprobarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aprobarModalLabel">Aprobar Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas aprobar este permiso?</p>
                    <input type="hidden" id="id_permiso_aprobar">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cambiarEstadoPermiso($('#id_permiso_aprobar').val(), 'aprobar')">Aprobar</button>
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
                    <input type="hidden" id="id_permiso_rechazo">
                    <select id="selectMotivoRechazo" class="form-select" required>
                        <option value="" disabled selected>Seleccione un motivo</option>
                        <?php
                        $observaciones = $con->query("SELECT * FROM observaciones");
                        while ($obs = $observaciones->fetch()) {
                            echo '<option value="'.$obs['id_observacion'].'">'.$obs['observacion'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="cambiarEstadoPermiso($('#id_permiso_rechazo').val(), 'no_aprobar')">Enviar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.2.3/js/bootstrap.min.js"></script>
</body>

</html>
