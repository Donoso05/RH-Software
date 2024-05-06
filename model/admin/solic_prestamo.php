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
      $id_prestamo= $_POST['id_prestamo'];
      $id_usuario= $_POST['id_usuario'];
      $monto_solicitado= $_POST['monto_solicitado'];
	  $id_estado= $_POST['id_estado'];
      $valor_cuotas= $_POST['valor_cuotas'];
	  $cant_cuotas= $_POST['cant_cuotas'];

      $sql = $con -> prepare ("SELECT * FROM solic_prestamo where id_prestamo='$id_prestamo'");
      $sql -> execute();
      $fila = $sql -> fetchAll(PDO::FETCH_ASSOC);
      
    
    
      if($id_usuario=="" || $monto_solicitado=="" || $id_estado=="" || $valor_cuotas=="" || $cant_cuotas=="")
      {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
        echo '<script>window.location="solic_prestamo.php"</script>';
      }
      else if($fila){
        echo '<script>alert ("USUARIO YA REGISTRADO"); </script>';
        echo '<script>window.location="solic_prestamo.php"</script>';
      }

            
      else
      {
        $insertSQL = $con->prepare ("INSERT INTO solic_prestamo(id_prestamo,id_usuario, monto_solicitado,id_estado,valor_cuotas,cant_cuotas) 
        VALUES ('$id_prestamo','$id_usuario', '$monto_solicitado', '$id_estado', '$valor_cuotas','$cant_cuotas')");
        $insertSQL->execute();
        echo '<script>alert ("Solicitud Prestamo Registrada con Exito"); </script>';
        echo '<script>window.location="solic_prestamo.php"</script>';
      }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Solicitud Prestamo</title>
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
						Solicitud prestamo
					</span>

					<div class="wrap-input100" style="display: none;">
    					<input class="input100" type="text" name="id_prestamo" id="id_prestamo" placeholder="ID" readonly>
   						<span class="focus-input100"></span>
    					<span class="symbol-input100">
        					<i class="fa fa-envelope" aria-hidden="true"></i>
    					</span>
					</div>


					<div class="wrap-input100 validate-input" data-validate = "Ingrese Documento">
						<input class="input100" type="number" name="id_usuario" id="id_usuario" placeholder="Documento">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Ingrese Monto">
    					<input class="input100" type="number" name="monto_solicitado" id="monto_solicitado" placeholder="Monto Solicitado" oninput="calcular()">
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
					<select class="input100" name="id_estado">
							<?php
							$control = $con->prepare("SELECT * FROM estado WHERE id_estado BETWEEN 6 AND 10");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
							}
							?>
						</select>
					</div>


                    <div class="wrap-input100" ="Ingrese Valor Cuotas">
    					<input class="input100" type="number" name="valor_cuotas" id="valor_cuotas" placeholder="Valor Cuotas" readonly>
    					<span class="focus-input100"></span>
    					<span class="symbol-input100">
        					<i class="fa fa-envelope" aria-hidden="true"></i>
    					</span>
					</div>

                    <div class="wrap-input100 validate-input" data-validate="Cantidad de Cuotas">
    					<input class="input100" type="number" name="cant_cuotas" id="cant_cuotas" placeholder="Cantidad Cuotas" oninput="calcular()">
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
	<script type="text/javascript">
    function calcular() {
        try {
            var monto_solicitado = parseInt(document.getElementById("monto_solicitado").value) || 0;
            var cant_cuotas = parseInt(document.getElementById("cant_cuotas").value) || 0;
            var valor_cuotas = monto_solicitado / cant_cuotas;

            // Formatea el número con puntos para representar miles y millones
            document.getElementById("valor_cuotas").value = numberWithCommas(valor_cuotas.toFixed(0));
        } catch (e) {}
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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

