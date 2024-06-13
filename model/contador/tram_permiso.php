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

if (isset($_POST["MM_insert"]) && ($_POST["MM_insert"] == "formreg")) {
    // Obtener los datos del formulario
    $id_usuario = $_POST['id_usuario'];
    $id_tipo_permiso = $_POST['id_tipo_permiso'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $incapacidad = $_POST['incapacidad'];

    // Validar que los campos no estén vacíos
    if (empty($id_usuario) || empty($id_tipo_permiso) || empty($fecha_inicio) || empty($fecha_fin) || empty($incapacidad)) {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="";</script>';
    } else {
        // Preparar la consulta SQL para insertar los datos
        $insertSQL = $con->prepare("INSERT INTO tram_permiso (id_usuario, id_tipo_permiso, fecha_inicio, fecha_fin, incapacidad) 
                            VALUES (:id_usuario, :id_tipo_permiso, :fecha_inicio, :fecha_fin, :incapacidad)");

        // Vincular los parámetros
        $insertSQL->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $insertSQL->bindParam(':id_tipo_permiso', $id_tipo_permiso, PDO::PARAM_INT);
        $insertSQL->bindParam(':fecha_inicio', $fecha_inicio);
        $insertSQL->bindParam(':fecha_fin', $fecha_fin);
        $insertSQL->bindParam(':incapacidad', $incapacidad);

        // Ejecutar la consulta SQL
        if ($insertSQL->execute()) {
            echo '<script>alert("Registro exitoso");</script>';
            echo '<script>window.location="";</script>';
        } else {
            echo '<script>alert("Error al guardar los datos");</script>';
        }
    }
}

if (isset($_POST['id_permiso'])) {
    // Alternar el estado del permiso
    $id_permiso = $_POST['id_permiso'];
    
    // Obtener el estado actual
    $selectSQL = $con->prepare("SELECT id_estado FROM tram_permiso WHERE id_permiso = :id_permiso");
    $selectSQL->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
    $selectSQL->execute();
    $estadoActual = $selectSQL->fetch(PDO::FETCH_ASSOC)['id_estado'];
    
    // Determinar el nuevo estado
    $nuevoEstado = ($estadoActual == 3) ? 5 : 3;
    
    // Actualizar el estado
    $updateSQL = $con->prepare("UPDATE tram_permiso SET id_estado = :nuevo_estado WHERE id_permiso = :id_permiso");
    $updateSQL->bindParam(':nuevo_estado', $nuevoEstado, PDO::PARAM_INT);
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tramite Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/tram.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function aprobarPermiso(id_permiso) {
            if (confirm('¿Estás seguro de que deseas cambiar el estado de este permiso?')) {
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: { id_permiso: id_permiso },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            alert('Estado del permiso actualizado exitosamente.');
                            location.reload();
                        } else {
                            alert('Error al actualizar el estado del permiso.');
                        }
                    }
                });
            } else {
                alert('El cambio de estado ha sido cancelado.');
            }
        }
    </script>
</head>

<body>
    <div class="data-bs-target">
        <?php include("nav.php") ?>
        <div class="container-fluid">
            <h3 class="text-center text-secondary my-4">Trámite Permiso</h3>
            <div class="col-12 p-4">
                <div class="table-responsive">
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
                                        <td>
                                            <div class="text-center">
                                                <div class="d-flex justify-content-start">
                                                    <button onclick="aprobarPermiso(<?php echo $fila['id_permiso']; ?>)" 
                                                            class="btn <?php echo $fila['id_estado'] == 3 ? 'btn-success' : 'btn-danger'; ?> ms-2">
                                                        <?php echo $fila['id_estado'] == 3 ? 'Aprobar' : 'Cancelar'; ?>
                                                    </button>
                                                </div>
                                            </div>
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
</body>

</html>
