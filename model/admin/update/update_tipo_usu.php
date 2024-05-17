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

$sql = $con -> prepare ("SELECT * FROM tipos_usuarios WHERE id_tipo_usuario = '".$_GET['id']."'");
$sql -> execute();
$usua = $sql -> fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_tipo_usuario = $_POST['id_tipo_usuario'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $updateSQL = $con->prepare("UPDATE tipos_usuarios SET tipo_usuario = '$tipo_usuario' WHERE id_tipo_usuario = '".$_GET['id']."'");

    $updateSQL->execute();
    echo '<script>alert ("Actualización Exitosa");</script>';
} elseif (isset($_POST["delete"])) { 
    $id_tipo_usuario = $_POST['id_tipo_usuario'];
    
    $deleteSQL = $con->prepare("DELETE FROM tipos_usuarios WHERE id_tipo_usuario = ?");
    $deleteSQL->execute([$id_tipo_usuario]);
    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    header('Location: tabla_tipo_usu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientwidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>    
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Tipo Usuarios</title>
</head>

<body onload="centrar();">
    <table class="center">
        <form autocomplete="off" name="frm_consulta" method="POST">
            <tr>
                <td>ID</td>
                <td><input name="id_tipo_usuario" value="<?php echo $usua['id_tipo_usuario']; ?>" readonly> </td>
            </tr>

            <tr>
                <td>Tipo Usuario</td>
                <td><input name ="tipo_usuario" value="<?php echo $usua['tipo_usuario']?>" ></td>
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
                            