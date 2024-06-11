<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

$id_usuario = $_SESSION["id_usuario"];

// Consultar tipos de permiso  
$consultaTipos = $con->prepare("SELECT id_tipo_permiso, tipo_permiso, dias FROM tipo_permiso");
$consultaTipos->execute();
$tipos_permiso = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $tipo_permiso = $_POST['tipo_permiso'];

    // Obtener la duración del permiso según el tipo seleccionado
    foreach ($tipos_permiso as $tipo) {
        if ($tipo['id_tipo_permiso'] == $tipo_permiso) {
            $duracion_permiso = $tipo['dias'];
            break;
        }
    }

    // Calcular fecha fin
    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' + ' . $duracion_permiso . ' days'));

    // Insertar en la base de datos
    $insertSQL = $con->prepare("INSERT INTO tabla_permisos (id_usuario, descripcion, fecha_inicio, fecha_fin, tipo_permiso, duracion) 
                        VALUES (:id_usuario, :descripcion, :fecha_inicio, :fecha_fin, :tipo_permiso, :duracion)");

    $insertSQL->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $insertSQL->bindParam(':descripcion', $descripcion);
    $insertSQL->bindParam(':fecha_inicio', $fecha_inicio);
    $insertSQL->bindParam(':fecha_fin', $fecha_fin);
    $insertSQL->bindParam(':tipo_permiso', $tipo_permiso);
    $insertSQL->bindParam(':duracion', $duracion_permiso, PDO::PARAM_INT);

    if ($insertSQL->execute()) {
        echo '<script>alert("Permiso solicitado correctamente.");</script>';
        echo '<script>window.location="";</script>';
    } else {
        echo '<script>alert("Error al solicitar el permiso.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite de Permisos</title>
    <link rel="stylesheet" href="css/permiso.css">
    <script>
        function updateFechaFin() {
            var tipo_permiso = document.getElementById('tipo_permiso').value;
            var fecha_inicio = document.getElementById('fecha_inicio').value;
            
            // Realizar una solicitud AJAX para obtener la duración del permiso según el tipo seleccionado
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var dias_permiso = parseInt(xhr.responseText);
                    var fecha_fin = new Date(fecha_inicio);
                    fecha_fin.setDate(fecha_fin.getDate() + dias_permiso);
                    var dd = fecha_fin.getDate();
                    var mm = fecha_fin.getMonth() + 1;
                    var yyyy = fecha_fin.getFullYear();
                    if (dd < 10) {
                        dd = '0' + dd;
                    }
                    if (mm < 10) {
                        mm = '0' + mm;
                    }
                    fecha_fin = yyyy + '-' + mm + '-' + dd;
                    document.getElementById('fecha_fin').value = fecha_fin;
                }
            };
            xhr.open('POST', 'obtener_duracion_permiso.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send('tipo_permiso=' + tipo_permiso);
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <h1>Solicitud de Permisos</h1>
        <form id="permisoForm" action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="descripcion">Descripción de la excusa:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="archivo">Subir archivo (PDF):</label>
                <input type="file" id="archivo" name="archivo" accept="application/pdf" required>
            </div>
            <div class="form-group">
                <label for="tipo_permiso">Tipo de permiso:</label>
                <select id="tipo_permiso" name="tipo_permiso" required onchange="updateFechaFin()">
                    <?php foreach ($tipos_permiso as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_permiso']; ?>"><?php echo $tipo['tipo_permiso']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" readonly required>
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
                
            </tbody>
        </table>
    </div>
</body>

</html>
