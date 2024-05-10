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
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
	$estado = $_POST['estado'];


	if ($estado == "") {
		echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
		echo '<script>window.location="estado.php"</script>';
	} else {
		$insertSQL = $con->prepare("INSERT INTO estado(estado) 
VALUES ('$estado')");
		$insertSQL->execute();
		echo '<script>alert ("Registro exitoso");</script>';
		echo '<script>window.location="estado.php"</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary">Agregar Estados</h3>
            <div class="mb-3">
                <label for="usuario" class="form-label">Agregar Estado Nuevo:</label>
                <input type="text" class="form-control" name="estado" >

            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">ID </th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Consulta de armas
                    $consulta = "SELECT * FROM estado ";
                    $resultado = $con->query($consulta);

                    while ($fila = $resultado->fetch()) {
                    ?>
                        <tr>
                            <td><?php echo $fila["id_estado"]; ?></td>
                            <td><?php echo $fila["estado"]; ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="edit_rol.php?id_rol=<?php echo $fila["id_estado"]; ?>" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="elim_rol.php?id_rol=<?php echo $fila["id_estado"]; ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-user-xmark"></i></a>
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
