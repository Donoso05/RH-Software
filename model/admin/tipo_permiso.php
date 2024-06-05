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
        echo '<script>alert ("TIPO DE PERMISO YA CREADO"); </script>';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary">Registrar Permisos</h3>
            <div class="mb-3">
                <label for="usuario" class="form-label">Tipo Permiso:</label>
                <input type="text" class="form-control" name="tipo_permiso" required>

            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                <input type="hidden" name="MM_insert" value="formreg" required>
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Tipo Permiso</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Consulta de armas
                    $consulta = "SELECT * FROM tipo_permiso ";
                    $resultado = $con->query($consulta);

                    while ($fila = $resultado->fetch()) {
                    ?>
                        <tr>
                            <td><?php echo $fila["tipo_permiso"]; ?></td>
                            <td>
                            <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                    <a href="update_tipo_permiso.php?id=<?php echo $fila['id_tipo_permiso']; ?>" onclick="window.open('./update/update_tipo_permiso.php?id=<?php echo $fila['id_tipo_permiso']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
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
</body>

</html>