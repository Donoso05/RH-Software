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
// Conectar a la base de datos
$con = $db->conectar();

$id_usuario = $_SESSION["id_usuario"];
$nit_empresa = $_SESSION["nit_empresa"]; // Obtener el nit_empresa de la sesión

// Consultar tipos de permiso  
$consultaTipos = $con->prepare("SELECT id_tipo_permiso, tipo_permiso, dias FROM tipo_permiso");
$consultaTipos->execute();
$tipos_permiso = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

// Consultar permisos solicitados por el usuario que coincidan con el id_usuario y el nit_empresa
$consultaPermisos = $con->prepare("SELECT tp.descripcion, tp.incapacidad, tp.fecha_inicio, tp.fecha_fin, e.estado, tperm.tipo_permiso 
                                   FROM tram_permiso tp 
                                   JOIN tipo_permiso tperm ON tp.id_tipo_permiso = tperm.id_tipo_permiso 
                                   JOIN estado e ON tp.id_estado = e.id_estado
                                   WHERE tp.id_usuario = :id_usuario AND tp.nit_empresa = :nit_empresa");
$consultaPermisos->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$consultaPermisos->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
$consultaPermisos->execute();
$permisos = $consultaPermisos->fetchAll(PDO::FETCH_ASSOC);
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

        <table class="table table-striped">
            <thead class="bg-dark text-white" >
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
