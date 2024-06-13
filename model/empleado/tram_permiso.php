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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite de Permisos</title>
    <link rel="stylesheet" href="css/permiso.css">
</head>
<body>
    <?php include("nav.php") ?>
    <div class="container">
        <h1>Solicitud de Permisos</h1>
        <form id="permisoForm" action="procesar_permiso.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="descripcion">Descripción de la excusa:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="form-group">
                <label for="tipo_permiso">Tipo de permiso:</label>
                <select id="tipo_permiso" name="tipo_permiso" required>
                    <?php foreach ($tipos_permiso as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_permiso']; ?>" data-dias="<?php echo $tipo['dias']; ?>"><?php echo $tipo['tipo_permiso']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" readonly required>
            </div>
            <div class="form-group">
                <label for="archivo">Subir archivo (PDF):</label>
                <input type="file" id="archivo" name="archivo" accept="application/pdf" required>
            </div>
            <button type="submit">Solicitar Permiso</button>
        </form>

        <h2>Permisos Solicitados</h2>

        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Archivo</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Tipo de Permiso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="permisoTableBody">
                <?php foreach ($permisos as $permiso): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($permiso['descripcion']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($permiso['incapacidad']); ?>" target="_blank">Ver Archivo</a></td>
                        <td><?php echo htmlspecialchars($permiso['fecha_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($permiso['fecha_fin']); ?></td>
                        <td><?php echo htmlspecialchars($permiso['tipo_permiso']); ?></td>
                        <td><?php echo htmlspecialchars($permiso['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('fecha_inicio').addEventListener('change', calcularFechaFin);
        document.getElementById('tipo_permiso').addEventListener('change', calcularFechaFin);

        function calcularFechaFin() {
            var fechaInicio = document.getElementById('fecha_inicio').value;
            var tipoPermiso = document.getElementById('tipo_permiso');
            var duracionPermiso = tipoPermiso.options[tipoPermiso.selectedIndex].getAttribute('data-dias');
            if (fechaInicio && duracionPermiso) {
                var fechaInicioDate = new Date(fechaInicio);
                fechaInicioDate.setDate(fechaInicioDate.getDate() + parseInt(duracionPermiso));
                var dd = String(fechaInicioDate.getDate()).padStart(2, '0');
                var mm = String(fechaInicioDate.getMonth() + 1).padStart(2, '0'); // January is 0!
                var yyyy = fechaInicioDate.getFullYear();
                var fechaFin = yyyy + '-' + mm + '-' + dd;
                document.getElementById('fecha_fin').value = fechaFin;
            }
        }

        // Populate the select options with data-dias attribute for JavaScript calculation
        var tiposPermiso = <?php echo json_encode($tipos_permiso); ?>;
        var selectTipoPermiso = document.getElementById('tipo_permiso');
        selectTipoPermiso.innerHTML = tiposPermiso.map(function(tipo) {
            return '<option value="' + tipo.id_tipo_permiso + '" data-dias="' + tipo.dias + '">' + tipo.tipo_permiso + '</option>';
        }).join('');
    </script>
</body>
</html>
