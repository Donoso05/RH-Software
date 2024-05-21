<?php
    require_once("conexion/conexion.php");
    $db = new Database();
    $con = $db->conectar();

    if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
        $id_usuario = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $id_tipo_cargo = $_POST['id_tipo_cargo'];
        $correo = $_POST['correo'];
        $id_tipo_usuario = $_POST['id_tipo_usuario'];
        $contrasena = $_POST['contrasena'];
        $nit_empresa = $_POST['nit_empresa'];
        
        // Establecer id_estado a 2 (inactivo)
        $id_estado = 2;

        $sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario=:id_usuario");
        $sql->bindParam(':id_usuario', $id_usuario);
        $sql->execute();
        $fila = $sql->fetch(PDO::FETCH_ASSOC);

        if ($id_usuario == "" || $nombre == "" || $id_tipo_cargo == "" || $correo == "" || $contrasena == "" || $nit_empresa == "") {
            echo '<script>alert("EXISTEN CAMPOS VACIOS");</script>';
            echo '<script>window.location="usuario.php"</script>';
        } elseif ($fila) {
            echo '<script>alert("USUARIO YA REGISTRADO");</script>';
            echo '<script>window.location="usuario.php"</script>';
        } else {
            $password = password_hash($contrasena, PASSWORD_DEFAULT, array("cost" => 12)); // Hash de la contraseña
            $insertSQL = $con->prepare("INSERT INTO usuario (id_usuario, nombre, id_tipo_cargo, id_estado, correo, id_tipo_usuario, contrasena, nit_empresa) 
                                        VALUES (:id_usuario, :nombre, :id_tipo_cargo, :id_estado, :correo, :id_tipo_usuario, :contrasena, :nit_empresa)");
            $insertSQL->bindParam(':id_usuario', $id_usuario);
            $insertSQL->bindParam(':nombre', $nombre);
            $insertSQL->bindParam(':id_tipo_cargo', $id_tipo_cargo);
            $insertSQL->bindParam(':id_estado', $id_estado);
            $insertSQL->bindParam(':correo', $correo);
            $insertSQL->bindParam(':id_tipo_usuario', $id_tipo_usuario);
            $insertSQL->bindParam(':contrasena', $password);
            $insertSQL->bindParam(':nit_empresa', $nit_empresa);
            $insertSQL->execute();
            echo '<script>alert("Usuario Creado con Exito");</script>';
            echo '<script>window.location="usuario.php"</script>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Iniciar Sesión</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->    
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->    
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <!--===============================================================================================-->    
    <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
</head>
<body>
    
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-form-title" style="background-image: url(images/ini.jpg);">
                    <span class="login100-form-title-1">
                        Iniciar Sesión
                    </span>
                </div>

                <form action="controller/inicio.php" method="POST" class="login100-form validate-form">
                    <div class="wrap-input100 validate-input m-b-26" data-validate="Ingrese su Cedula">
                        <span class="label-input100">Cedula</span>
                        <input class="input100" type="text" name="id_usuario" placeholder="Ingrese su Cedula">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-26" data-validate="Ingrese su Nombre">
                        <span class="label-input100">Nombre</span>
                        <input class="input100" type="text" name="nombre" placeholder="Ingrese su Nombre">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-26">
                        <span class="label-input100">Tipo Cargo</span>
                        <select class="input100" name="id_cargo">
                            <option value="">Selecciona el Tipo de Cargo</option>
                            <?php
                            $control = $con->prepare("SELECT * FROM tipo_cargo");
                            $control->execute();
                            while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $fila['id_cargo'] . "'>" . $fila['cargo'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="wrap-input100 validate-input m-b-26" data-validate="Ingrese su Correo">
                        <span class="label-input100">Correo</span>
                        <input class="input100" type="email" name="correo" placeholder="Ingrese su Correo">
                        <span class="focus-input100"></span>
                    </div>

					<div class="wrap-input100 validate-input m-b-26">
    <span class="label-input100">Tipo Usuario</span>
    <select class="input100" name="id_tipo_usuario">
        <option value="">Selecciona el Tipo Usuario</option>
        <?php
        // Modificar la consulta para que solo seleccione los tipos de usuario con id_tipo_usuario 1
        $control = $con->prepare("SELECT * FROM tipos_usuarios WHERE id_tipo_usuario = 1");
        $control->execute();
        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . $fila['id_tipo_usuario'] . "'>" . $fila['tipo_usuario'] . "</option>";
        }
        ?>
    </select>
</div>


                    <div class="wrap-input100 validate-input m-b-18" data-validate="Ingrese su Contraseña">
                        <span class="label-input100">Contraseña</span>
                        <input class="input100" type="password" name="contrasena" placeholder="Ingrese su Contraseña">
                        <span class="focus-input100"></span>
                    </div>

                    <div>
                        <a href="rec_contraseña/recuperar.html" class="txt1">
                            <!-- Enlace para recuperar contraseña (opcional) -->
                        </a>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">Ingresar</button>
                        <input type="hidden" name="MM_insert" value="formreg">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!--===============================================================================================-->
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/daterangepicker/moment.min.js"></script>
    <script src="vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/countdowntime/countdowntime.js"></script>
    <!--===============================================================================================-->
    <script src="js/main.js"></script>

</body>
</html>
