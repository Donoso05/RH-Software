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
      $id_usuario= $_POST['id_usuario'];
      $nombre= $_POST['nombre'];
	  $id_tipo_cargo= $_POST['id_tipo_cargo'];
	  $id_estado= $_POST['id_estado'];
      $correo= $_POST['correo'];
	  $id_tipo_usuario= $_POST['id_tipo_usuario'];
      $contrasena= $_POST['contrasena'];
      $nit_empresa= $_POST['nit_empresa'];

      $sql = $con -> prepare ("SELECT * FROM usuario where id_usuario='$id_usuario'");
      $sql -> execute();
      $fila = $sql -> fetchAll(PDO::FETCH_ASSOC);
      
    
    
      if($id_usuario=="" || $nombre=="" || $id_tipo_cargo=="" || $id_estado=="" || $correo=="" || $contrasena=="" || $nit_empresa=="")
      {
        echo '<script>alert ("EXISTEN CAMPOS VACIOS"); </script>';
        echo '<script>window.location="usuario.php"</script>';
      }
      else if($fila){
        echo '<script>alert ("USUARIO YA REGISTRADO"); </script>';
        echo '<script>window.location="usuario.php"</script>';
      }

            
      else
      {
        $password=password_hash($contrasena,PASSWORD_DEFAULT,array("pass"=>12));
        $insertSQL = $con->prepare ("INSERT INTO usuario(id_usuario,nombre,id_tipo_cargo,id_estado,correo,id_tipo_usuario,contrasena,nit_empresa) 
        VALUES ('$id_usuario','$nombre', '$id_tipo_cargo', '$id_estado', '$correo', '$id_tipo_usuario', '$pass_cifrado','$nit_empresa')");
        $insertSQL->execute();
        echo '<script>alert ("Usuario Creado con Exito"); </script>';
        echo '<script>window.location="usuario.php"</script>';
      }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Usuarios</title>
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
						Usuarios
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Ingrese su documento	">
						<input class="input100" type="number" name="id_usuario" id="id_usuario" placeholder="Documento">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Ingrese su Nombre">
    					<input class="input100" type="text" name="nombre" id="nombre" placeholder="Nombre" pattern="[A-Za-zÑñÁáÉéÍíÓóÚú\s]+" title="Solo se permiten letras y espacios">
    					<span class="focus-input100"></span>
    					<span class="symbol-input100">
        					<i class="fa fa-envelope" aria-hidden="true"></i>
    					</span>
					</div>


					<div class="wrap-input100 validate-input">
					<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					<select class="input100" name="id_tipo_cargo">
							<?php
							$control = $con->prepare("SELECT * FROM tipo_cargo");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_tipo_cargo'] . "'>" . $fila['cargo'] . "</option>";
							}
							?>
						</select>
					</div>

					<div class="wrap-input100 validate-input">
					<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					<select class="input100" name="id_estado">
							<?php
							$control = $con->prepare("SELECT * FROM estado where id_estado <= 5");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
							}
							?>
						</select>
					</div>


					<div class="wrap-input100 validate-input" data-validate="Ingrese su Correo">
						<input class="input100" type="text" name="correo" id="correo" placeholder="Correo Electronico">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input">
					<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					<select class="input100" name="id_tipo_usuario">
							<?php
							$control = $con->prepare("SELECT * FROM tipos_usuarios");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_tipo_usuario'] . "'>" . $fila['tipo_usuario'] . "</option>";
							}
							?>
						</select>
					</div>


					<div class="wrap-input100 validate-input" data-validate="Ingrese su Contraseña">
    					<input class="input100" type="password" name="contrasena" id="contrasena" placeholder="Contraseña" pattern="^(?=.*\d)(?=.*[a-zA-Z]).{10,}$" title="La contraseña debe tener al menos 10 caracteres alfanuméricos">
    					<span class="focus-input100"></span>
    					<span class="symbol-input100">
        					<i class="fa fa-lock" aria-hidden="true"></i>
    					</span>
					</div>


					<div class="wrap-input100 validate-input" data-validate = "Ingrese NIT Empresa">
						<input class="input100" type="number" name="nit_empresa" id="nit_empresa" placeholder="NIT">
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
	<script>
function validarContrasena() {
    var contrasena = document.getElementById('contrasena').value;

    if (contrasena.length < 10) {
        alert('La contraseña debe tener al menos 10 caracteres alfanuméricos');
        return false;
    }

    return true;
}
</script>
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
