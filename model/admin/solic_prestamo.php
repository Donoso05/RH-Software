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
	  $mes= $_POST['mes'];
	  $anio= $_POST['anio'];

      $sql = $con -> prepare ("SELECT * FROM solic_prestamo where id_prestamo='$id_prestamo'");
      $sql -> execute();
      $fila = $sql -> fetchAll(PDO::FETCH_ASSOC);
      
    
    
      if($id_usuario=="" || $monto_solicitado=="" || $id_estado=="" || $valor_cuotas=="" || $cant_cuotas=="" || $mes=="" || $anio=="")
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
        $insertSQL = $con->prepare ("INSERT INTO solic_prestamo(id_prestamo,id_usuario, monto_solicitado,id_estado,valor_cuotas,cant_cuotas,mes,anio) 
        VALUES ('$id_prestamo','$id_usuario', '$monto_solicitado', '$id_estado', '$valor_cuotas','$cant_cuotas','$mes','$anio')");
        $insertSQL->execute();
        echo '<script>alert ("Solicitud Prestamo Registrada con Exito"); </script>';
        echo '<script>window.location="solic_prestamo.php"</script>';
      }
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php"); ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary">Solicitud Préstamo</h3>

            <div class="mb-3">
                <label for="usuario" class="form-label">Cédula:</label>
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

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select class="form-control" name="id_estado">
                    <option value="">Seleccione el Estado</option>
                    <?php
                    $control = $con->prepare("SELECT * FROM estado");
                    $control->execute();
                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="monto_solicitado" class="form-label">Monto Solicitado:</label>
                <input type="number" class="form-control" name="monto_solicitado" oninput="calcular()">
            </div>

            <div class="mb-3">
                <label for="valor_cuotas" class="form-label">Valor Cuotas:</label>
                <input type="number" class="form-control" name="valor_cuotas" readonly>
            </div>

            <div class="mb-3">
                <label for="cant_cuotas" class="form-label">Cantidad Cuotas:</label>
                <input type="number" class="form-control" name="cant_cuotas" oninput="calcular()">
            </div>

            <div class="mb-3">
                <label for="mes" class="form-label">Mes:</label>
                <input type="date" class="form-control" name="mes">
            </div>

            <div class="mb-3">
                <label for="anio" class="form-label">Año:</label>
                <input type="date" class="form-control" name="anio">
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
                        <th scope="col">Monto Solicitado</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Valor Cuotas</th>
                        <th scope="col">Cantidad Cuotas</th>
                        <th scope="col">Mes</th>
                        <th scope="col">Año</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $consulta = "SELECT sp.id_usuario, u.nombre, sp.monto_solicitado, e.estado, sp.valor_cuotas, sp.cant_cuotas, sp.mes, sp.anio 
				FROM solic_prestamo sp 
				JOIN usuario u ON sp.id_usuario = u.id_usuario 
				JOIN estado e ON sp.id_estado = e.id_estado";
                $resultado = $con->query($consulta);

                while ($fila = $resultado->fetch()) {
                ?>
                    <tr>
                        <td><?php echo $fila["id_usuario"]; ?></td>
                        <td><?php echo $fila["nombre"]; ?></td>
                        <td><?php echo $fila["monto_solicitado"]; ?></td>
                        <td><?php echo $fila["estado"]; ?></td>
                        <td><?php echo $fila["valor_cuotas"]; ?></td>
                        <td><?php echo $fila["cant_cuotas"]; ?></td>
                        <td><?php echo $fila["mes"]; ?></td>
                        <td><?php echo $fila["anio"]; ?></td>
                        <td>
                            <div class="text-center">
                                <div class="d-flex justify-content-start">
                                    <a href="edit_rol.php?id_rol=<?php echo $fila["id_prestamo"]; ?>" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="elim_rol.php?id_rol=<?php echo $fila["id_prestamo"]; ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-user-xmark"></i></a>
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

    <script type="text/javascript">
        function calcular() {
            var montoSolicitado = parseFloat(document.querySelector("input[name='monto_solicitado']").value) || 0;
            var cantCuotas = parseFloat(document.querySelector("input[name='cant_cuotas']").value) || 0;
            
            if (cantCuotas > 0) {
                var valorCuotas = montoSolicitado / cantCuotas;
                document.querySelector("input[name='valor_cuotas']").value = numberWithCommas(valorCuotas.toFixed(2));
            } else {
                document.querySelector("input[name='valor_cuotas']").value = '';
            }
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>

</html>
		