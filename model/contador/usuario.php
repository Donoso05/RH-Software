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

if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
    $id_usuario = $_POST['id_usuario'];
    $nombre = trim($_POST['nombre']);
    $id_tipo_cargo = $_POST['id_tipo_cargo'];
    $id_estado = $_POST['id_estado'];
    $correo = $_POST['correo'];
    $id_tipo_usuario = $_POST['id_tipo_usuario'];
    $nit_empresa = $_POST['nit_empresa'];

    // Validación de id_usuario para que solo tenga entre 9 y 10 dígitos y solo números
    if (!preg_match('/^\d{6,11}$/', $id_usuario)) {
        echo '<script>alert("El Número de Documento debe contener entre 9 y 10 dígitos.");</script>';
        echo '<script>window.location="usuario.php"</script>';
        exit();
    }

    // Validación de nombre para que solo contenga letras y espacios, y no solo espacios
    if (!preg_match('/^[a-zA-Z\s]+$/', $nombre) || !preg_match('/[a-zA-Z]/', $nombre)) {
        echo '<script>alert("El Nombre solo puede contener letras y no puede estar compuesto solo por espacios.");</script>';
        echo '<script>window.location="usuario.php"</script>';
        exit();
    }

    // Validación de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("El Correo no es válido.");</script>';
        echo '<script>window.location="usuario.php"</script>';
        exit();
    }

    // Resto de la validación
    $sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario='$id_usuario'");
    $sql->execute();
    $fila = $sql->fetch(PDO::FETCH_ASSOC);

    if ($id_usuario == "" || $nombre == "" || $id_tipo_cargo == "" || $id_estado == "" || $correo == "" || $nit_empresa == "") {
        echo '<script>alert("EXISTEN CAMPOS VACIOS");</script>';
        echo '<script>window.location="usuario.php"</script>';
    } elseif ($fila) {
        echo '<script>alert("USUARIO YA REGISTRADO");</script>';
        echo '<script>window.location="usuario.php"</script>';
    } else {
        $contrasena_fija = "103403sena"; // Contraseña fija
        $password = password_hash($contrasena_fija, PASSWORD_DEFAULT, array("cost" => 12)); // Hash de la contraseña fija
        $insertSQL = $con->prepare("INSERT INTO usuario(id_usuario, nombre, id_tipo_cargo, id_estado, correo, id_tipo_usuario, contrasena, nit_empresa) 
        VALUES ('$id_usuario', '$nombre', '$id_tipo_cargo', '$id_estado', '$correo', '$id_tipo_usuario', '$password', '$nit_empresa')");
        $insertSQL->execute();

        // Enviar correo al empleado
        $to = $correo;
        $subject = "Registro en el Sistema";
        $message = "Hola $nombre,\n\nTu usuario ha sido creado en el sistema.\n\nUsuario: $id_usuario\nContraseña temporal: $contrasena_fija\n\nPor favor, cambia tu contraseña en tu primer inicio de sesión.\n\nSaludos,\nEl equipo de Recursos Humanos";
        $headers = "From: sjuliethws@gmail.com";

        if (mail($to, $subject, $message, $headers)) {
            echo '<script>alert("Usuario Creado con Exito y correo enviado");</script>';
        } else {
            echo '<script>alert("Usuario Creado con Exito, pero no se pudo enviar el correo");</script>';
        }

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
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .table thead {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
    <form class="col-3 p-3" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
            <h3 class="text-center text-secondary">Registrar Usuarios</h3>
            <div class="mb-3">
                <label for="id_usuario" class="form-label">Numero de Documento</label>
                <input type="text" class="form-control" name="id_usuario" id="id_usuario" required pattern="\d{6,11}" minlength="6" maxlength="11" title="El numero de documento debe contener entre 6 y 11 dígitos" autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required pattern="[a-zA-Z\s]+" title="Solo se permiten letras" autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="cargo" class="form-label">Tipo Cargo</label>
                <select class="form-control" name="id_tipo_cargo" required autocomplete="off">
                    <option value="">Selecciona el Tipo de Cargo</option>
                    <?php
                    $control = $con->prepare("SELECT * FROM tipo_cargo WHERE id_tipo_cargo >= 2");
                    $control->execute();
                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $fila['id_tipo_cargo'] . "'>" . $fila['cargo'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-control" name="id_estado" required autocomplete="off">
                    <?php
                    $control = $con->prepare("SELECT * FROM estado WHERE id_estado <= 1");
                    $control->execute();
                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" name="correo" id="exampleInputEmail1" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="tipo_suario" class="form-label">Tipo Usuario</label>
                <select class="form-control" name="id_tipo_usuario" required autocomplete="off">
                    <option value="">Selecciona el Tipo Usuario</option>
                    <?php
                    // Solo mostrar tipos de usuario con id 2 y 3
                    $control = $con->prepare("SELECT * FROM tipos_usuarios WHERE id_tipo_usuario IN (2, 3)");
                    $control->execute();
                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $fila['id_tipo_usuario'] . "'>" . $fila['tipo_usuario'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nit" class="form-label">NIT Empresa</label>
                <input type="number" name="nit_empresa" class="form-control" id="nit_empresa" required autocomplete="off">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-9 p-4">
            <div class="table-container">
                <table class="table">
                    <thead >
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
                                            <a href="liquidar.php?id_usuario=<?php echo $fila['id_usuario']; ?>" class="ms-2">
                                                <i class="btn btn-danger">Liquidar</i>
                                            </a>

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
    </div>
</body>

</html>