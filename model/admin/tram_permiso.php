<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

if (isset($_POST["MM_insert"]) && ($_POST["MM_insert"] == "formreg")) {
    // Obtener los datos del formulario
    $id_usuario = $_POST['id_usuario'];
    $id_tipo_permiso = $_POST['id_tipo_permiso'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $id_estado = $_POST['id_estado'];
    $incapacidad = $_POST['incapacidad'];

    // Validar que los campos no estén vacíos
    if (empty($id_usuario) || empty($id_tipo_permiso) || empty($fecha_inicio) || empty($fecha_fin) || empty($id_estado) || empty($incapacidad)) {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="";</script>';
    } else {
        // Preparar la consulta SQL para insertar los datos
        $insertSQL = $con->prepare("INSERT INTO tram_permiso (id_usuario, id_tipo_permiso, fecha_inicio, fecha_fin, id_estado, incapacidad) 
                            VALUES (:id_usuario, :id_tipo_permiso, :fecha_inicio, :fecha_fin, :id_estado, :incapacidad)");

        // Vincular los parámetros
        $insertSQL->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $insertSQL->bindParam(':id_tipo_permiso', $id_tipo_permiso, PDO::PARAM_INT);
        $insertSQL->bindParam(':fecha_inicio', $fecha_inicio);
        $insertSQL->bindParam(':fecha_fin', $fecha_fin);
        $insertSQL->bindParam(':id_estado', $id_estado, PDO::PARAM_INT);
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
	<title>Tramites permiso</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/tipo_usu.css">
	<link rel="stylesheet" type="text/css" href="css/sidebar.css">
	<!--===============================================================================================-->
</head>

<body>
<?php include("sidebar.php") ?>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="images/img-01.png" alt="IMG">
				</div>

				<form class="login100-form validate-form" method="post">
					<span class="login100-form-title">
						TRAMITES PERMISO
					</span>

					<div class="wrap-input100 validate-input" data-validate="Ingrese su documento	">
						<input class="input100" type="number" name="id_usuario" id="id_usuario" placeholder="Documento">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input">
						<select class="input100" name="id_tipo_permiso">
							<?php
							$control = $con->prepare("SELECT * FROM tipo_permiso");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_tipo_permiso'] . "'>" . $fila['tipo_permiso'] . "</option>";
							}
							?>
						</select>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>




					<div class="wrap-input100 validate-input" data-validate="Fecha de inicio">
						<input class="input100" type="date" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha de inicio">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>


					<div class="wrap-input100 validate-input" data-validate="Fecha de fin">
						<input class="input100" type="date" name="fecha_fin" id="fecha_fin" placeholder="Fecha de fin">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Estado del permiso">
						<select class="input100" name="id_estado">
							<?php
							$control = $con->prepare("SELECT * FROM estado where id_estado >= 11");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
							}
							?>
						</select> <span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Detalles de incapacidad">
						<textarea class="input100" cols="50" name="incapacidad" id="incapacidad" placeholder="Detalles de incapacidad"></textarea>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="container-login100-form-btn">
						<input class="login100-form-btn" type="submit" name="validar" value="Registrar">
						<input type="hidden" name="MM_insert" value="formreg">
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#">
							Username / Password?
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>


</body>

</html>