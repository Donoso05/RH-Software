<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$id_permiso = $_GET['id_permiso'] ?? null;

if (!$id_permiso) {
    echo '<script>alert("ID de permiso no especificado.");</script>';
    echo '<script>window.location.href = "../listado_permisos.php";</script>';
    exit();
}

// Obtener el nit_empresa del usuario logueado
$id_usuario_sesion = $_SESSION["id_usuario"];
$sqlUsuario = $con->prepare("SELECT nit_empresa FROM usuario WHERE id_usuario = ?");
$sqlUsuario->execute([$id_usuario_sesion]);
$nit_empresa = $sqlUsuario->fetchColumn();

$sql = $con->prepare("SELECT * FROM tram_permiso tp
                      JOIN tipo_permiso tperm ON tp.id_tipo_permiso = tperm.id_tipo_permiso
                      JOIN estado e ON tp.id_estado = e.id_estado
                      WHERE tp.id_permiso = ? AND tp.nit_empresa = ?");
$sql->execute([$id_permiso, $nit_empresa]);
$usua = $sql->fetch();

if (!$usua) {
    echo '<script>alert("No se encontraron resultados para el permiso especificado.");</script>';
    echo '<script>window.location.href = "../listado_permisos.php";</script>';
    exit();
}

// Consultar los tipos de permiso según el nit_empresa del usuario al que se va a editar
$consultaTipos = $con->prepare("SELECT tp.id_tipo_permiso, tp.tipo_permiso, tp.dias
                                FROM tipo_permiso tp
                                JOIN usuario u ON tp.nit_empresa = u.nit_empresa
                                WHERE u.id_usuario = ?");
$consultaTipos->execute([$usua['id_usuario']]);
$tipos_permiso = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["update"])) {
        $id_usuario = $_POST['id_usuario'];
        $id_tipo_permiso = $_POST['id_tipo_permiso'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $id_estado = $_POST['id_estado'];
        $descripcion = $_POST['descripcion'];

        // Manejo de archivo subido
        $archivo = $_FILES['incapacidad'];
        $rutaArchivo = $usua['incapacidad']; // Ruta del archivo actual

        if (!empty($archivo['tmp_name'])) {
            $nombreArchivo = basename($archivo['name']); // Obtener el nombre del archivo sin la ruta
            $rutaArchivo = '../../../uploads/' . $nombreArchivo;

            // Mover el archivo subido a la ubicación deseada
            if (!move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
                echo '<script>alert("Error al subir el archivo.");</script>';
                exit();
            }
        }

        $updateSQL = $con->prepare("UPDATE tram_permiso SET id_usuario = ?, id_tipo_permiso = ?, fecha_inicio = ?, fecha_fin = ?, id_estado = ?, descripcion = ?, incapacidad = ? WHERE id_permiso = ? AND nit_empresa = ?");
        $updateSQL->execute([$id_usuario, $id_tipo_permiso, $fecha_inicio, $fecha_fin, $id_estado, $descripcion, $rutaArchivo, $id_permiso, $nit_empresa]);

        echo '<script>alert("Actualización Exitosa");</script>';
        echo '<script>window.close();</script>';
    } elseif (isset($_POST["delete"])) {
        $deleteSQL = $con->prepare("DELETE FROM tram_permiso WHERE id_permiso = ? AND nit_empresa = ?");
        $deleteSQL->execute([$id_permiso, $nit_empresa]);

        echo '<script>alert("Registro Eliminado Exitosamente");</script>';
        echo '<script>window.close();</script>';
        exit();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Permiso</title>
    <link rel="stylesheet" href="../css/ingreso2.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/solid.css">
    <script src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <main>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Información del Permiso</h4>
                </div>
                <div class="card-body">
                    <form action="" method="post" role="form" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" name="id_permiso" value="<?php echo htmlspecialchars($usua['id_permiso'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Documento</label>
                            <div class="col-lg-9">
                                <input name="id_usuario" value="<?php echo htmlspecialchars($usua['id_usuario'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Tipo Permiso</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="id_tipo_permiso" id="id_tipo_permiso" required>
                                    <option value="">Seleccione uno</option>
                                    <?php
                                    foreach ($tipos_permiso as $tipo) {
                                        $selected = ($tipo['id_tipo_permiso'] == $usua['id_tipo_permiso']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($tipo['id_tipo_permiso'], ENT_QUOTES, 'UTF-8') . "' data-dias='" . htmlspecialchars($tipo['dias'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($tipo['tipo_permiso'], ENT_QUOTES, 'UTF-8') . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Fecha Inicio</label>
                            <div class="col-lg-9">
                                <input name="fecha_inicio" value="<?php echo htmlspecialchars($usua['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" id="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Fecha Fin</label>
                            <div class="col-lg-9">
                                <input name="fecha_fin" value="<?php echo htmlspecialchars($usua['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" id="fecha_fin" readonly required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Descripción</label>
                            <div class="col-lg-9">
                                <textarea id="descripcion" name="descripcion" class="form-control" required><?php echo htmlspecialchars($usua['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Archivo</label>
                            <div class="col-lg-9">
                                <input type="file" id="incapacidad" name="incapacidad" class="form-control" accept="application/pdf">
                                <small class="form-text text-muted">Dejar en blanco para mantener el archivo actual.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Estado</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="id_estado" required>
                                    <option value="">Seleccione uno</option>
                                    <?php
                                    $control = $con->prepare("SELECT * FROM estado WHERE id_estado IN (3, 5)");
                                    $control->execute();
                                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($fila['id_estado'] == $usua['id_estado']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($fila['id_estado'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($fila['estado'], ENT_QUOTES, 'UTF-8') . "</option>";
                                    }
                                    ?>
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
        </div>
    </main>
    <script>
        document.getElementById('fecha_inicio').addEventListener('change', calcularFechaFin);
        document.getElementById('id_tipo_permiso').addEventListener('change', calcularFechaFin);

        function calcularFechaFin() {
            var fechaInicio = document.getElementById('fecha_inicio').value;
            var tipoPermiso = document.getElementById('id_tipo_permiso');
            var duracionPermiso = tipoPermiso.options[tipoPermiso.selectedIndex].getAttribute('data-dias');
            if (fechaInicio && duracionPermiso) {
                var fechaInicioDate = new Date(fechaInicio);
                fechaInicioDate.setDate(fechaInicioDate.getDate() + parseInt(duracionPermiso));
                var dd = String(fechaInicioDate.getDate()).padStart(2, '0');
                var mm = String(fechaInicioDate.getMonth() + 1).padStart(2, '0');
                var yyyy = fechaInicioDate.getFullYear();
                var fechaFin = yyyy + '-' + mm + '-' + dd;
                document.getElementById('fecha_fin').value = fechaFin;
            }
        }
    </script>
</body>
</html>
