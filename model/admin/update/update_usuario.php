<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}

require_once("../../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$id_usuario = $_GET['id'];
$sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
$sql->execute([':id' => $id_usuario]);
$usua = $sql->fetch();

if (!$usua) {
    echo '<script>alert("Usuario no encontrado.");</script>';
    echo '<script>window.location.href = "../usuarios.php";</script>';
    exit();
}

// Cargar opciones de cargos
$cargos_sql = $con->prepare("SELECT * FROM tipo_cargo");
$cargos_sql->execute();
$cargos = $cargos_sql->fetchAll(PDO::FETCH_ASSOC);

// Cargar opciones de estados
$estados_sql = $con->prepare("SELECT * FROM estado WHERE id_estado <= 2");
$estados_sql->execute();
$estados = $estados_sql->fetchAll(PDO::FETCH_ASSOC);

// Cargar opciones de tipos de usuarios excluyendo el id_tipo_usuario = 1
$tipos_usuarios_sql = $con->prepare("SELECT * FROM tipos_usuarios WHERE id_tipo_usuario > 1");
$tipos_usuarios_sql->execute();
$tipos_usuarios = $tipos_usuarios_sql->fetchAll(PDO::FETCH_ASSOC);

// Verificar si el usuario de la sesión es un administrador
$isAdmin = $_SESSION['id_tipo_usuario'] == 1;

if (isset($_POST["update"])) {
    $nombre = trim($_POST['nombre']);
    $id_tipo_cargo = $_POST['id_tipo_cargo'];
    $id_estado = $_POST['id_estado'];
    $correo = trim($_POST['correo']);
    $id_tipo_usuario = $_POST['id_tipo_usuario'];

    // Validación de nombre para que solo contenga letras y espacios, y no solo espacios
    if (!preg_match('/^[a-zA-Z\s]+$/', $nombre) || !preg_match('/[a-zA-Z]/', $nombre)) {
        echo '<script>alert("El Nombre solo puede contener letras y no puede estar compuesto solo por espacios.");</script>';
        echo '<script>window.location="update_usuario.php?id=' . $id_usuario . '"</script>';
        exit();
    }

    // Validación de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("El Correo no es válido.");</script>';
        echo '<script>window.location="update_usuario.php?id=' . $id_usuario . '"</script>';
        exit();
    }

    // Verificar si el correo ya existe en la base de datos, excepto para el usuario actual
    $correo_antiguo = $usua['correo'];
    $checkCorreoSQL = $con->prepare("SELECT * FROM usuario WHERE correo = :correo AND id_usuario != :id");
    $checkCorreoSQL->execute([':correo' => $correo, ':id' => $id_usuario]);
    $correoExistente = $checkCorreoSQL->fetch();

    if ($correoExistente) {
        echo '<script>alert("El Correo ya está registrado para otro usuario.");</script>';
        echo '<script>window.location="update_usuario.php?id=' . $id_usuario . '"</script>';
        exit();
    }

    $updateSQL = $con->prepare("UPDATE usuario SET nombre = :nombre, id_tipo_cargo = :id_tipo_cargo, id_estado = :id_estado, correo = :correo, id_tipo_usuario = :id_tipo_usuario WHERE id_usuario = :id");
    $updateSQL->execute([
        ':nombre' => $nombre,
        ':id_tipo_cargo' => $id_tipo_cargo,
        ':id_estado' => $id_estado,
        ':correo' => $correo,
        ':id_tipo_usuario' => $id_tipo_usuario,
        ':id' => $id_usuario
    ]);

    if ($correo != $correo_antiguo) {
        $contrasena_fija = "103403sena"; // Contraseña fija
        $message = "Hola $nombre,\n\nTu usuario ha sido actualizado en el sistema.\n\nUsuario: $id_usuario\nContraseña temporal: $contrasena_fija\n\nPor favor, cambia tu contraseña en tu primer inicio de sesión.\n\nSaludos,\nEl equipo de Recursos Humanos";
        $headers = "From: sjuliethws@gmail.com";

        if (mail($correo, "Actualización de Registro en el Sistema", $message, $headers)) {
            echo '<script>alert("Correo de actualización enviado con éxito.");</script>';
        } else {
            echo '<script>alert("Actualización exitosa, pero no se pudo enviar el correo.");</script>';
        }
    }

    echo '<script>alert("Actualización Exitosa");</script>';
    echo '<script>window.close();</script>';
} elseif (isset($_POST["delete"])) {
    $id_usuario = $_POST['id_usuario'];

    // Verificar si el usuario que intenta eliminar es el mismo que el usuario en sesión
    if ($id_usuario == $_SESSION["id_usuario"]) {
        echo '<script>alert("No puedes eliminar tu propio registro.");</script>';
        echo '<script>window.close();</script>';
        exit();
    }

    // Eliminar dependencias en otras tablas primero
    $tables = ['solic_prestamo', 'triggers', 'nomina', 'tram_permiso'];
    foreach ($tables as $table) {
        $deleteDependenciesSQL = $con->prepare("DELETE FROM $table WHERE id_usuario = :id");
        $deleteDependenciesSQL->execute([':id' => $id_usuario]);
    }

    $deleteSQL = $con->prepare("DELETE FROM usuario WHERE id_usuario = :id");
    $deleteSQL->execute([':id' => $id_usuario]);

    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    echo '<script>window.close();</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/ingreso2.css">
    <title>Actualizar datos</title>

    <!--JQUERY-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- FRAMEWORK BOOTSTRAP para el estilo de la pagina-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7HUIbX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <!-- Los iconos tipo Solid de Fontawesome-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/solid.css">
    <script src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>

    <!-- Nuestro css-->
    <link rel="stylesheet" type="text/css" href="../../css/ingreso2.css">
    <!-- DATA TABLE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
</head>

<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h4>Actualizar usuario</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" name="frm_consulta" method="POST" autocomplete="off" onsubmit="return validarFormulario()">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Documento</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_usuario" value="<?php echo $usua['id_usuario'] ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Nombre</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="nombre" value="<?php echo $usua['nombre'] ?>" required pattern="[A-Za-z\s]+">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Tipo Usuario</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_tipo_usuario" id="id_tipo_usuario" required onchange="actualizarTipoCargo()">
                                <?php if ($isAdmin && $usua['id_tipo_usuario'] == 1): ?>
                                    <option value="1" selected>Administrador</option>
                                <?php else: ?>
                                    <?php foreach ($tipos_usuarios as $tipo_usuario): ?>
                                        <?php if (in_array($tipo_usuario['id_tipo_usuario'], [2, 3])): ?>
                                            <option value="<?php echo $tipo_usuario['id_tipo_usuario'] ?>" <?php echo ($tipo_usuario['id_tipo_usuario'] == $usua['id_tipo_usuario']) ? 'selected' : ''; ?>>
                                                <?php echo $tipo_usuario['tipo_usuario'] ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Cargo</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_tipo_cargo" id="id_tipo_cargo" required>
                                <?php if ($isAdmin && $usua['id_tipo_cargo'] == 1): ?>
                                    <option value="1" selected>Administrador</option>
                                <?php else: ?>
                                    <?php foreach ($cargos as $cargo): ?>
                                        <option value="<?php echo $cargo['id_tipo_cargo'] ?>" <?php echo ($cargo['id_tipo_cargo'] == $usua['id_tipo_cargo']) ? 'selected' : ''; ?>>
                                            <?php echo $cargo['cargo'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Estado</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_estado" id="id_estado" required>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado['id_estado'] ?>" <?php echo ($estado['id_estado'] == $usua['id_estado']) ? 'selected' : ''; ?>>
                                        <?php echo $estado['estado'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Correo</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="correo" value="<?php echo $usua['correo'] ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input name="update" type="submit" class="btn btn-primary" value="Actualizar">
                            <button class="btn btn-danger" name="delete" onclick="return confirmarEliminacion()">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        function validarFormulario() {
            const nombre = document.querySelector('input[name="nombre"]').value.trim();
            const nombreRegex = /^[A-Za-z\s]+$/;

            if (!nombre || !nombreRegex.test(nombre) || !/[a-zA-Z]/.test(nombre)) {
                alert('El nombre solo puede contener letras, no puede estar compuesto solo por espacios.');
                return false;
            }

            const correo = document.querySelector('input[name="correo"]').value.trim();
            const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!correoRegex.test(correo)) {
                alert('El correo no es válido.');
                return false;
            }

            return true;
        }

        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este usuario?");
        }

        function actualizarTipoCargo() {
            const tipoUsuario = document.querySelector('select[name="id_tipo_usuario"]').value;
            const tipoCargo = document.querySelector('select[name="id_tipo_cargo"]');
            const cargos = <?php echo json_encode($cargos); ?>;

            tipoCargo.innerHTML = ''; // Limpiar las opciones actuales

            let opcionesFiltradas = [];
            if (tipoUsuario == 2) {
                // Solo mostrar id_tipo_cargo 4
                opcionesFiltradas = cargos.filter(cargo => cargo.id_tipo_cargo == 4);
            } else if (tipoUsuario == 3) {
                // Excluir id_tipo_cargo 1 y 4
                opcionesFiltradas = cargos.filter(cargo => cargo.id_tipo_cargo != 1 && cargo.id_tipo_cargo != 4);
            } else if (tipoUsuario == 1) {
                // Solo mostrar id_tipo_cargo 1 para Administradores
                opcionesFiltradas = cargos.filter(cargo => cargo.id_tipo_cargo == 1);
            }

            opcionesFiltradas.forEach(cargo => {
                const option = document.createElement('option');
                option.value = cargo.id_tipo_cargo;
                option.textContent = cargo.cargo;
                tipoCargo.appendChild(option);
            });

            // Seleccionar el id_tipo_cargo actual si está en las opciones filtradas
            if (opcionesFiltradas.some(cargo => cargo.id_tipo_cargo == <?php echo $usua['id_tipo_cargo']; ?>)) {
                tipoCargo.value = <?php echo $usua['id_tipo_cargo']; ?>;
            }
        }

        // Ejecutar al cargar para ajustar los selects según sea necesario
        document.addEventListener('DOMContentLoaded', function () {
            actualizarTipoCargo();
        });
    </script>
</body>

</html>
