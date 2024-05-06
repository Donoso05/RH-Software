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

$sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario = '" . $_GET['id'] . "'");
$sql->execute();
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_usuario= $_POST['id_usuario'];
      $nombre= $_POST['nombre'];
	  $id_tipo_cargo= $_POST['id_tipo_cargo'];
	  $id_estado= $_POST['id_estado'];
      $correo= $_POST['correo'];
	  $id_tipo_usuario= $_POST['id_tipo_usuario'];
      $contrasena= $_POST['contrasena'];
      $nit_empresa= $_POST['nit_empresa'];
    $updateSQL = $con->prepare("UPDATE usuario SET nombre = '$nombre', id_tipo_cargo = '$id_tipo_cargo', id_estado = '$id_estado', correo = '$correo', id_tipo_usuario = '$id_tipo_usuario', contrasena = '$contrasena', nit_empresa = '$nit_empresa' WHERE id_usuario = '" . $_GET['id'] . "'");

    $updateSQL->execute();
    echo '<script>alert("Actualización Exitosa");</script>';
} elseif (isset($_POST["delete"])) {
    $id_usuario = $_POST['id_usuario'];

    $deleteSQL = $con->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $deleteSQL->execute([$id_usuario]);
    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    header('Location: tabla_usuarios.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<script>
    function centrar() {
        iz = (screen.width - document.body.clientwidth) / 2;
        de = (screen.height - document.body.clientHeight) / 2;
        moveTo(iz, de);
    }
</script>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <title>Actualizar datos</title>
</head>

<body onload="centrar();">
    <table class="center">
        <form autocomplete="off" name="frm_consulta" method="POST">
            <tr>
                <td>Documento</td>
                <td><input name="id_usuario" value="<?php echo $usua['id_usuario'] ?>" readonly></td>
            </tr>

            <tr>
                <td>Nombre</td>
                <td><input name="nombre" value="<?php echo $usua['nombre'] ?>" ></td>
            </tr>

            <tr>
                <td>Cargo</td>
                <td>
                    <select class="form-control" name="id_tipo_cargo" id="id_tipo_cargo">
                        <option value="">Seleccione un Cargo</option>
                        <?php
                        $control = $con->prepare("select * from tipo_cargo where id_tipo_cargo ");
                        $control->execute();

                        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($fila['id_tipo_cargo'] == $usua['id_tipo_cargo']) ? 'selected' : '';
                            echo "<option value=" . $fila['id_tipo_cargo'] . " $selected>" . $fila['cargo'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Estado</td>
                <td>
                    <select class="form-control" name="id_estado" id="id_estado">
                        <option value="">Seleccione un Estado</option>
                        <?php
                        $control = $con->prepare("select * from estado where id_estado <= 5");
                        $control->execute();

                        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($fila['id_estado'] == $usua['id_estado']) ? 'selected' : '';
                            echo "<option value=" . $fila['id_estado'] . " $selected>" . $fila['estado'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Correo</td>
                <td><input name="correo" value="<?php echo $usua['correo'] ?>"></td>
            </tr>



            <tr>
                <td>Tipo Usuario</td>
                <td>
                    <select class="form-control" name="id_tipo_usuario" id="id_tipo-usuario">
                        <option value="">Seleccione un Tipo de Usuario</option>
                        <?php
                        $control = $con->prepare("select * from tipos_usuarios where id_tipo_usuario");
                        $control->execute();

                        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($fila['id_tipo_usuario'] == $usua['id_tipo_usuario']) ? 'selected' : '';
                            echo "<option value=" . $fila['id_tipo_usuario'] . " $selected>" . $fila['tipo_usuario'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
                
            
            <tr>
                <td>Contrasena</td>
                <td><input name="contrasena" value="<?php echo $usua['contrasena'] ?>"></td>
            </tr>

            <tr>
                <td>NIT Empresa</td>
                <td><input name="nit_empresa" value="<?php echo $usua['nit_empresa'] ?>"></td>
            </tr>


            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td><input type="submit" name="update" value="Actualizar"></td>
                <td><input type="submit" name="delete" value="Eliminar"></td>
            </tr>
        </form>
    </table>
</body>
</html>
