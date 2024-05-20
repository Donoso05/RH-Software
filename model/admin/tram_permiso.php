<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tramite Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary">Tramite Permiso</h3>
            <div class="mb-3">

			<div class="mb-3">
                <label for="usuario" class="form-label">Empleado:</label>
                <select class="form-control" name="id_usuario">
				<option value="">Seleccione el Empleado</option>
							<?php
							$control = $con->prepare("SELECT id_usuario FROM usuario");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $fila['id_usuario'] . "'>" . $fila['id_usuario'] . "</option>";
							}
							?>
						</select>
            </div>
                <label for="usuario" class="form-label">Tipo Permiso:</label>
                <select class="form-control" name="id_tipo_permiso">
                <option value="">Seleccione el Permiso</option>
							<?php
							$control = $con->prepare("SELECT * FROM tipo_permiso");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_tipo_permiso'] . "'>" . $fila['tipo_permiso'] . "</option>";
							}
							?>
						</select>

            </div>

			<div class="mb-3">
                <label for="usuario" class="form-label">Fecha Inicio:</label>
                <input type="date" class="form-control" name="fecha_inicio">
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Fecha Fin:</label>
                <input type="date" class="form-control" name="fecha_fin">
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Incapacidad:</label>
                <input type="file" class="form-control" name="incapacidad">
            </div>



            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                       
                        <th scope="col">Documento</th>
                        <th scope="col">Nombre</th>
						<th scope="col">Tipo Permiso</th>
						<th scope="col">Fecha Inicio</th>
                        <th scope="col">Fecha Fin</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
$consulta = "SELECT tram_permiso.id_permiso, usuario.id_usuario, usuario.nombre, tipo_permiso.tipo_permiso, tram_permiso.fecha_inicio, tram_permiso.fecha_fin, estado.estado
             FROM tram_permiso
             INNER JOIN usuario ON tram_permiso.id_usuario = usuario.id_usuario
             INNER JOIN tipo_permiso ON tram_permiso.id_tipo_permiso = tipo_permiso.id_tipo_permiso
             INNER JOIN estado ON tram_permiso.id_estado = estado.id_estado";
$resultado = $con->query($consulta);

while ($fila = $resultado->fetch()) {
?>
    <tr>
      
        <td><?php echo $fila["id_usuario"]; ?></td>
        <td><?php echo $fila["nombre"]; ?></td>
        <td><?php echo $fila["tipo_permiso"]; ?></td>
        <td><?php echo $fila["fecha_inicio"]; ?></td>
        <td><?php echo $fila["fecha_fin"]; ?></td>
        <td><?php echo $fila["estado"]; ?></td>
        <td>
        <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                    <a href="update_tram.php?id_rol=<?php echo $fila['id_permiso']; ?>" onclick="window.open('./update/update_tram.php?id=<?php echo $fila['id_permiso']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
                                    </div>
                                </div>
            </div>
        </td>
    </tr>
<?php
}
?>
</tbody>
</table>



        </div>




    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>
