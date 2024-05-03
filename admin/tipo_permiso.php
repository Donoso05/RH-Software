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
    $con =$db->conectar();
?>
<?php
    if ((isset($_POST["MM_insert"]))&&($_POST["MM_insert"]=="formreg"))
    {
      $tipo_permiso = $_POST['tipo_permiso'];

      $sql = $con -> prepare ("SELECT * FROM tipo_permiso where tipo_permiso ='$tipo_permiso'");
      $sql -> execute();
      $fila = $sql -> fetchAll(PDO::FETCH_ASSOC);

      if ($tipo_permiso=="")
      {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
        echo '<script>window.location="tipo_permiso.php"</script>';
      }
      else if($fila){
        echo '<script>alert ("TIPO DE PERMISO CREADO"); </script>';
        echo '<script>window.location="tipo_permiso.php"</script>';
      } 
      else{
        $insertSQL = $con->prepare ("INSERT INTO tipo_permiso(tipo_permiso) VALUES ('$tipo_permiso')");
        $insertSQL->execute();
        echo '<script>alert ("Permiso Creado con Exitoso"); </script>';
        echo '<script>window.location="tipo_permiso.php"</script>';
      }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Tipo Permisoss</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
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
						Tipo Permiso
					</span>

					<div class="wrap-input100">
						<input class="input100" type="number" name="id_tipo_permiso" id="id_tipo_permiso" placeholder="id_tipo_permiso" readonly>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Ingrese Tipo Permiso">
						<input class="input100" type="text" name="tipo_permiso" id="tipo_permiso" placeholder="Tipo Permiso" require pattern="[A-Za-z]+" title="Por favor, solo ingrese letras">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
					<input class="login100-form-btn" type="submit" name="validar" value="Registrar">
					<input type="hidden" name="MM_insert" value="formreg">
					</div>

				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
	<script src="js/sidebar.js"></script>

</body>
</html>