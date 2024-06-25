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

$nit_empresa = $_SESSION['nit_empresa']; // Corrigiendo el uso de la variable de sesión

// Debug: Imprimir el valor de nit_empresa
echo '<script>console.log("NIT Empresa: ' . $nit_empresa . '");</script>';
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
                                <th scope="col">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $consulta = "SELECT tram_permiso.id_permiso, usuario.id_usuario, usuario.nombre, tipo_permiso.tipo_permiso, tram_permiso.fecha_inicio, tram_permiso.fecha_fin, estado.estado, tram_permiso.id_estado, tram_permiso.descripcion, tram_permiso.incapacidad, observaciones.observacion
                                FROM tram_permiso
                                INNER JOIN usuario ON tram_permiso.id_usuario = usuario.id_usuario
                                LEFT JOIN observaciones ON tram_permiso.motivo_rechazo = observaciones.id_observacion
                                INNER JOIN tipo_permiso ON tram_permiso.id_tipo_permiso = tipo_permiso.id_tipo_permiso
                                INNER JOIN estado ON tram_permiso.id_estado = estado.id_estado
                                WHERE tram_permiso.nit_empresa = :nit_empresa";
                            $stmt = $con->prepare($consulta);
                            $stmt->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
                            $stmt->execute();
                            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Debug: Imprimir el resultado de la consulta
                            echo '<script>console.log(' . json_encode($resultado) . ');</script>';

                            if (count($resultado) > 0) {
                                foreach ($resultado as $fila) {
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fila["id_usuario"]); ?></td>
                                        <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                                        <td><?php echo htmlspecialchars($fila["descripcion"]); ?></td>
                                        <td><a href="<?php echo htmlspecialchars($fila["incapacidad"]); ?>" target="_blank">Ver Archivo</a></td>
                                        <td><?php echo htmlspecialchars($fila["tipo_permiso"]); ?></td>
                                        <td><?php echo htmlspecialchars($fila["fecha_inicio"]); ?></td>
                                        <td><?php echo htmlspecialchars($fila["fecha_fin"]); ?></td>
                                        <td><?php echo htmlspecialchars($fila["estado"]); ?></td>
                                        <td><?php echo htmlspecialchars($fila["observacion"]); ?></td>
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
