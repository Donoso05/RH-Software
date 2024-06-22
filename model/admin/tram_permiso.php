<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

$db = new Database();
$con = $db->conectar();
$nit_empresa_session = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión

// Consultar tipos de permiso
$consultaTipos = $con->prepare("SELECT id_tipo_permiso, tipo_permiso, dias FROM tipo_permiso WHERE nit_empresa = ?");
$consultaTipos->execute([$nit_empresa_session]);
$tipos_permiso = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los permisos con información del usuario
$consultaPermisos = $con->prepare("SELECT tp.id_permiso, tp.descripcion, tp.incapacidad, tp.fecha_inicio, tp.fecha_fin, e.estado, tperm.tipo_permiso, u.id_usuario, u.nombre 
                                   FROM tram_permiso tp 
                                   JOIN tipo_permiso tperm ON tp.id_tipo_permiso = tperm.id_tipo_permiso 
                                   JOIN estado e ON tp.id_estado = e.id_estado
                                   JOIN usuario u ON tp.id_usuario = u.id_usuario
                                   WHERE tp.nit_empresa = ?");
$consultaPermisos->execute([$nit_empresa_session]);
$permisos = $consultaPermisos->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los usuarios
$consultaUsuarios = $con->prepare("SELECT id_usuario, nombre FROM usuario WHERE nit_empresa = ?");
$consultaUsuarios->execute([$nit_empresa_session]);
$usuarios = $consultaUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite de Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-12 col-md-3 p-3 card" id="permisoForm" action="procesar_permiso.php" method="post" enctype="multipart/form-data">
            <h3 class="text-center text-primary">Trámite Permiso</h3>
            <div class="mb-3">
                <label for="id_usuario" class="form-label">Documento:</label>
                <select id="id_usuario" name="id_usuario" class="form-control" required>
                    <option value="">Seleccione el Usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id_usuario']; ?>"><?php echo $usuario['id_usuario']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre del Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción de la excusa:</label>
                <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="fecha_inicio" class="form-label">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tipo_permiso" class="form-label">Tipo de permiso:</label>
                <select id="tipo_permiso" name="tipo_permiso" class="form-control" required>
                    <option value="">Seleccione el Permiso</option>
                    <?php foreach ($tipos_permiso as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_permiso']; ?>" data-dias="<?php echo $tipo['dias']; ?>"><?php echo $tipo['tipo_permiso']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="fecha_fin" class="form-label">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" readonly required>
            </div>
            <div class="mb-3">
                <label for="archivo" class="form-label">Subir archivo (PDF):</label>
                <input type="file" id="archivo" name="archivo" class="form-control" accept="application/pdf" required>
            </div>
            <button type="submit" class="btn btn-primary">Solicitar Permiso</button>
        </form>

        <div class="col-12 col-md-9 p-3">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th scope="col">ID Usuario</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Archivo</th>
                            <th scope="col">Fecha de Inicio</th>
                            <th scope="col">Fecha de Fin</th>
                            <th scope="col">Tipo de Permiso</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="permisoTableBody">
                        <?php foreach ($permisos as $permiso): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($permiso['id_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($permiso['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($permiso['descripcion']); ?></td>
                                <td><a href="<?php echo htmlspecialchars($permiso['incapacidad']); ?>" target="_blank">Ver Archivo</a></td>
                                <td><?php echo htmlspecialchars($permiso['fecha_inicio']); ?></td>
                                <td><?php echo htmlspecialchars($permiso['fecha_fin']); ?></td>
                                <td><?php echo htmlspecialchars($permiso['tipo_permiso']); ?></td>
                                <td><?php echo htmlspecialchars($permiso['estado']); ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-start">
                                            <a href="update_tram.php?id_permiso=<?php echo $permiso['id_permiso']; ?>" onclick="window.open('update_tram.php?id_permiso=<?php echo $permiso['id_permiso']; ?>','','width=500,height=500,toolbar=NO'); return false;" class="btn btn-primary">Editar</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('id_usuario').addEventListener('change', function() {
            var usuarios = <?php echo json_encode($usuarios); ?>;
            var selectedId = this.value;
            var nombreUsuario = '';
            for (var i = 0; i < usuarios.length; i++) {
                if (usuarios[i]['id_usuario'] == selectedId) {
                    nombreUsuario = usuarios[i]['nombre'];
                    break;
                }
            }
            document.getElementById('nombre_usuario').value = nombreUsuario;
        });

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
                var mm = String(fechaInicioDate.getMonth() + 1).padStart(2, '0');
                var yyyy = fechaInicioDate.getFullYear();
                var fechaFin = yyyy + '-' + mm + '-' + dd;
                document.getElementById('fecha_fin').value = fechaFin;
            }
        }

        var tiposPermiso = <?php echo json_encode($tipos_permiso); ?>;
        var selectTipoPermiso = document.getElementById('tipo_permiso');
        selectTipoPermiso.innerHTML = tiposPermiso.map(function(tipo) {
            return '<option value="' + tipo.id_tipo_permiso + '" data-dias="' + tipo.dias + '">' + tipo.tipo_permiso + '</option>';
        }).join('');
    </script>
</body>

</html>
