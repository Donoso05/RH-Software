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
	$porcentaje_p = $_POST['porcentaje_p'];


	if ($porcentaje_p == "") {
		echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
		echo '<script>window.location="pension.php"</script>';
	} else {
		$insertSQL = $con->prepare("INSERT INTO pension(porcentaje_p) 
VALUES ('$porcentaje_p')");
		$insertSQL->execute();
		echo '<script>alert ("Registro exitoso");</script>';
		echo '<script>window.location="pension.php"</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pension</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">

</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary"> Pension</h3>
            <div class="mb-3">
                <label for="usuario" class="form-label">Porcentaje Pension:</label>
                <input type="number" class="form-control" name="porcentaje_p" >

            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">ID </th>
                        <th scope="col">Porcentaje</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Consulta de armas
                    $consulta = "SELECT * FROM pension";
                    $resultado = $con->query($consulta);

                    while ($fila = $resultado->fetch()) {
                    ?>
                        <tr>
                            <td><?php echo $fila["id_pension"]; ?></td>
                            <td><?php echo $fila["porcentaje_p"]; ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                    <a href="update_pension.php?id=<?php echo $fila['id_pension']; ?>" onclick="window.open('./update/update_pension.php?id=<?php echo $fila['id_pension']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>

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