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

require '../../vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
    $id_usuario = $_POST['id_usuario'];
    $nombre = trim($_POST['nombre']);
    $id_tipo_cargo = $_POST['id_tipo_cargo'];
    $id_estado = $_POST['id_estado'];
    $correo = $_POST['correo'];
    $id_tipo_usuario = $_POST['id_tipo_usuario'];
    $nit_empresa = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión

    // Concatenar los datos del usuario en un formato JSON
    $datos_usuario = json_encode([
        'id_usuario' => $id_usuario       
    ]);

    // Generar código de barras con los datos del usuario
    $generator = new BarcodeGeneratorPNG();
    $codigo_barras_imagen = $generator->getBarcode($datos_usuario, $generator::TYPE_CODE_128);

    // Guardar la imagen del código de barras
    $codigo_barras_filename = uniqid() . '.png';
    file_put_contents(__DIR__ . '/../bar_code/' . $codigo_barras_filename, $codigo_barras_imagen);

    // Validación de id_usuario para que solo tenga entre 6 y 10 dígitos y solo números
    if (!preg_match('/^\d{6,10}$/', $id_usuario)) {
        echo '<script>alert("El Número de Documento debe contener entre 6 y 10 dígitos.");</script>';
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

    if ($id_usuario == "" || $nombre == "" || $id_tipo_cargo == "" || $id_estado == "" || $correo == "" || $id_tipo_usuario == "") {
        echo '<script>alert("EXISTEN CAMPOS VACIOS");</script>';
        echo '<script>window.location="usuario.php"</script>';
        exit();
    } elseif ($fila) {
        echo '<script>alert("USUARIO YA REGISTRADO");</script>';
        echo '<script>window.location="usuario.php"</script>';
        exit();
    } else {
        $contrasena_fija = "103403sena"; // Contraseña fija
        $password = password_hash($contrasena_fija, PASSWORD_DEFAULT, array("cost" => 12)); // Hash de la contraseña fija
        $insertSQL = $con->prepare("INSERT INTO usuario(id_usuario, nombre, id_tipo_cargo, id_estado, correo, id_tipo_usuario, contrasena, nit_empresa, codigo_barras) 
        VALUES ('$id_usuario', '$nombre', '$id_tipo_cargo', '$id_estado', '$correo', '$id_tipo_usuario', '$password', '$nit_empresa', '$codigo_barras_filename')");
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
        exit();
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
    <script>
        function validarFormulario() {
            const tipoUsuarioSelect = document.getElementById('id_tipo_usuario');
            const tipoCargoSelect = document.getElementById('id_tipo_cargo');

            if (tipoUsuarioSelect.value == '2' && tipoCargoSelect.value != '4') {
                alert('Si selecciona Tipo Usuario 2, debe seleccionar Tipo Cargo 4.');
                return false;
            }

            if (tipoUsuarioSelect.value == '3' && !['2', '3', '7'].includes(tipoCargoSelect.value)) {
                alert('Si selecciona Tipo Usuario 3, debe seleccionar Tipo Cargo 2, 3 o 7.');
                return false;
            }

            const idUsuario = document.getElementById('id_usuario').value.trim();
            const idUsuarioRegex = /^\d{6,10}$/;
            if (!idUsuarioRegex.test(idUsuario)) {
                alert('El Número de Documento debe contener entre 6 y 10 dígitos y solo números.');
                return false;
            }

            const nombre = document.querySelector('input[name="nombre"]').value.trim();
            const nombreRegex = /^[a-zA-Z\s]+$/;
            if (!nombre || !nombreRegex.test(nombre) || !/[a-zA-Z]/.test(nombre)) {
                alert('El Nombre solo puede contener letras, no puede estar compuesto solo por espacios.');
                return false;
            }

            return true;
        }

        function actualizarTipoCargo() {
            const tipoUsuarioSelect = document.getElementById('id_tipo_usuario');
            const tipoCargoSelect = document.getElementById('id_tipo_cargo');

            // Resetear las opciones del select de tipo cargo
            tipoCargoSelect.innerHTML = '';

            fetch('obtener_cargos.php?tipo_usuario=' + tipoUsuarioSelect.value)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        data.forEach(cargo => {
                            tipoCargoSelect.innerHTML += `<option value="${cargo.id_tipo_cargo}">${cargo.cargo}</option>`;
                        });
                        tipoCargoSelect.value = data[0].id_tipo_cargo; // Seleccionar la primera opción por defecto
                    } else {
                        tipoCargoSelect.innerHTML = '<option value="">No hay cargos disponibles</option>';
                    }
                });
        }

        function soloNumeros(evt) {
            const charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                evt.preventDefault();
            }
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-3 p-3" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
            <h3 class="text-center text-secondary">Registrar Usuarios</h3>
            <div class="mb-3">
                <label for="id_usuario" class="form-label">Numero de Documento</label>
                <input type="text" class="form-control" name="id_usuario" id="id_usuario" required minlength="6" maxlength="10" onkeypress="soloNumeros(event)" autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required pattern="[a-zA-Z\s]+" title="Solo se permiten letras" autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="id_tipo_usuario" class="form-label">Tipo Usuario</label>
                <select class="form-control" name="id_tipo_usuario" id="id_tipo_usuario" onchange="actualizarTipoCargo()" required autocomplete="off">
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
                <label for="id_tipo_cargo" class="form-label">Tipo Cargo</label>
                <select class="form-control" name="id_tipo_cargo" id="id_tipo_cargo" required autocomplete="off">
                    <option value="">Selecciona el Tipo de Cargo</option>
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
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg">
        </form>
        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Documento</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Tipo Usuario</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Cargo</th>
                        <th scope="col">NIT empresa</th>
                        <th scope="col">Cod_barras</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Consulta de usuarios filtrando por el mismo nit_empresa del usuario en sesión
                $nit_empresa_session = $_SESSION['nit_empresa'];
                $consulta = "SELECT usuario.id_usuario, usuario.nombre, tipo_cargo.cargo AS tipo_cargo, estado.estado AS estado, usuario.correo, tipos_usuarios.tipo_usuario, usuario.nit_empresa, usuario.codigo_barras 
                             FROM usuario 
                             INNER JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo 
                             INNER JOIN tipos_usuarios ON usuario.id_tipo_usuario = tipos_usuarios.id_tipo_usuario 
                             INNER JOIN estado ON usuario.id_estado = estado.id_estado
                             WHERE usuario.nit_empresa = :nit_empresa";
                $resultado = $con->prepare($consulta);
                $resultado->bindParam(':nit_empresa', $nit_empresa_session, PDO::PARAM_STR);
                $resultado->execute();
                while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                ?>
                        <tr>
                            <td><?php echo $fila["id_usuario"]; ?></td>
                            <td><?php echo $fila["nombre"]; ?></td>
                            <td><?php echo $fila["tipo_usuario"]; ?></td>
                            <td><?php echo $fila["estado"]; ?></td>
                            <td><?php echo $fila["correo"]; ?></td>
                            <td><?php echo $fila["tipo_cargo"]; ?></td>
                            <td><?php echo $fila["nit_empresa"]; ?></td>
                            <td><img src="../bar_code/<?php echo $fila["codigo_barras"]; ?>" style="max-width: 400px;"></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="update_cargo.php?id_rol=<?php echo $fila['id_usuario']; ?>" onclick="window.open('./update/update_usuario.php?id=<?php echo $fila['id_usuario']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
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
