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
	$cargo = $_POST['cargo'];
	$salario_base = $_POST['salario_base'];
	$id_arl = $_POST['id_arl'];


	if ($cargo == "" || $salario_base == "" || $id_arl == "") {
		echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
		echo '<script>window.location="tipo_cargo.php"</script>';
	} else {
		$insertSQL = $con->prepare("INSERT INTO tipo_cargo(cargo,salario_base,id_arl) 
VALUES ('$cargo','$salario_base','$id_arl')");
		$insertSQL->execute();
		echo '<script>alert ("Registro exitoso");</script>';
		echo '<script>window.location="tipo_cargo.php"</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary">Registrar Cargos</h3>
            <div class="mb-3">
                <label for="usuario" class="form-label">Tipo Cargo:</label>
                <input type="text" class="form-control" name="cargo" >

            </div>
			<div class="mb-3">
                <label for="usuario" class="form-label">Salario Base:</label>
                <input type="number" class="form-control" name="salario_base">
            </div>
			<div class="mb-3">
                <label for="usuario" class="form-label">ARL:</label>
                <select class="form-control" name="id_arl">
				<option value="">Selecciona el Tipo de ARL</option>
							<?php
							$control = $con->prepare("SELECT * FROM arl");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_arl'] . "'>" . $fila['tipo'] . "</option>";
							}
							?>
						</select>
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">ID </th>
                        <th scope="col">Tipo Cargo</th>
						<th scope="col">Salario Base</th>
						<th scope="col">ARL</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Consulta de armas
                    $consulta = "SELECT * FROM tipo_cargo, arl 
                    where tipo_cargo.id_arl=arl.id_arl ";
                    $resultado = $con->query($consulta);

                    while ($fila = $resultado->fetch()) {
                    ?>
                        <tr>
                            <td><?php echo $fila["id_tipo_cargo"]; ?></td>
                            <td><?php echo $fila["cargo"]; ?></td>
							<td><?php echo $fila["salario_base"]; ?></td>
							<td><?php echo $fila["tipo"]; ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="edit_rol.php?id_rol=<?php echo $fila["id_tipo_cargo"]; ?>" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="elim_rol.php?id_rol=<?php echo $fila["id_tipo_cargo"]; ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-user-xmark"></i></a>
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
