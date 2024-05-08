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

    if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
        $id_usuario = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $id_tipo_cargo = $_POST['id_tipo_cargo'];
        $id_estado = $_POST['id_estado'];
        $correo = $_POST['correo'];
        $id_tipo_usuario = $_POST['id_tipo_usuario'];
        $contrasena = $_POST['contrasena'];
        $nit_empresa = $_POST['nit_empresa'];

        $sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario='$id_usuario'");
        $sql->execute();
        $fila = $sql->fetch(PDO::FETCH_ASSOC);

        if ($id_usuario == "" || $nombre == "" || $id_tipo_cargo == "" || $id_estado == "" || $correo == "" || $contrasena == "" || $nit_empresa == "") {
            echo '<script>alert ("EXISTEN CAMPOS VACIOS"); </script>';
            echo '<script>window.location="usuario.php"</script>';
        } elseif ($fila) {
            echo '<script>alert ("USUARIO YA REGISTRADO"); </script>';
            echo '<script>window.location="usuario.php"</script>';
        } else {
            $password = password_hash($contrasena, PASSWORD_DEFAULT, array("cost" => 12)); // Hash de la contraseña
            $insertSQL = $con->prepare("INSERT INTO usuario(id_usuario, nombre, id_tipo_cargo, id_estado, correo, id_tipo_usuario, contrasena, nit_empresa) 
            VALUES ('$id_usuario', '$nombre', '$id_tipo_cargo', '$id_estado', '$correo', '$id_tipo_usuario', '$password', '$nit_empresa')");
            $insertSQL->execute();
            echo '<script>alert ("Usuario Creado con Exito"); </script>';
            echo '<script>window.location="usuario.php"</script>';
        }
    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post" enctype="multipart/form-data">
            <h3 class="text-center text-secondary">Registrar Usuarios</h3>
            <div class="mb-3">
                <label for="usuario" class="form-label">Numero de Documento</label>
                <input type="number" class="form-control" name="id_usuario" id="id_usuario">

            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre">

            </div>
			<div class="mb-3">
                <label for="cargo" class="form-label">Tipo Cargo</label>
                <select class="form-control" name="id_tipo_cargo">
                <option value="">Selecciona el Tipo de Cargo</option>
                            <?php
                            $control = $con->prepare("SELECT * FROM tipo_cargo");
                            $control->execute();
                            while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $fila['id_tipo_cargo'] . "'>" . $fila['cargo'] . "</option>";
                            }
                            ?>
                        </select>
            </div>
			
			<div class="mb-3">
			<label for="estado" class="form-label">Estado</label>
			<select class="form-control" name="id_estado">
            <option value="">Selecciona el estado</option>
							<?php
							$control = $con->prepare("SELECT * FROM estado where id_estado <= 2");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
							}
							?>
						</select>
			</div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" name="correo" id="exampleInputEmail1">
            </div>

			<div class="mb-3">
			<label for="tipo_suario" class="form-label">Tipo Usuario</label>
			<select class="form-control" name="id_tipo_usuario">
            <option value="">Selecciona el Tipo Usuario</option>
							<?php
							$control = $con->prepare("SELECT * FROM tipos_usuarios");
							$control->execute();
							while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='" . $fila['id_tipo_usuario'] . "'>" . $fila['tipo_usuario'] . "</option>";
							}
							?>
						</select>
            </div>

            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" name="contrasena" class="form-control" id="exampleInputPassword1">
            </div>

			<div class="mb-3">
                <label for="nit" class="form-label">NIT Empresa</label>
                <input type="number" name="nit_empresa" class="form-control" id="nit_empresa">
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
                        <th scope="col">Cargo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Tipo Usuario</th>
                        <th scope="col">NIT empresa</th>
						<th scope="col">Acciones</th>

                    </tr>
                </thead>
                <tbody>
                <?php
    // Consulta de armas
    $consulta = "SELECT usuario.id_usuario, usuario.nombre, tipo_cargo.cargo AS tipo_cargo, estado.estado AS estado, usuario.correo, tipos_usuarios.tipo_usuario, usuario.contrasena, usuario.nit_empresa 
                 FROM usuario 
                 INNER JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo 
                 INNER JOIN tipos_usuarios ON usuario.id_tipo_usuario = tipos_usuarios.id_tipo_usuario 
                 INNER JOIN estado ON usuario.id_estado = estado.id_estado";
    $resultado = $con->query($consulta);

    while ($fila = $resultado->fetch()) {
?>
                        <tr>
                            <td><?php echo $fila["id_usuario"]; ?></td>
                            <td><?php echo $fila["nombre"]; ?></td>
                            <td><?php echo $fila["tipo_cargo"]; ?></td>
                            <td><?php echo $fila["estado"]; ?></td>
                            <td><?php echo $fila["correo"]; ?></td>
							<td><?php echo $fila["tipo_usuario"]; ?></td>
							<td><?php echo $fila["nit_empresa"]; ?></td>
							
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="edit_usu.php?id=<?php echo $fila["id_usuario"]; ?>" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="elim_usu.php?id=<?php echo $fila["id_usuario"]; ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-user-xmark"></i></a>
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