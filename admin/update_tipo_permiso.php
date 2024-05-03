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

$sql = $con -> prepare ("SELECT * FROM tipo_permiso WHERE id_tipo_permiso = '".$_GET['id']."'");
$sql -> execute();
$usua = $sql -> fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_tipo_permiso = $_POST['id_tipo_permiso'];
    $tipo_permiso = $_POST['tipo_permiso'];
    $updateSQL = $con->prepare("UPDATE tipo_permiso SET tipo_permiso = '$tipo_permiso' WHERE id_tipo_permiso = '".$_GET['id']."'");

    $updateSQL->execute();
    echo '<script>alert ("Actualización Exitosa");</script>';
} elseif (isset($_POST["delete"])) { 
    $id_tipo_permiso = $_POST['id_tipo_permiso'];
    
    $deleteSQL = $con->prepare("DELETE FROM tipo_permiso WHERE id_tipo_permiso = ?");
    $deleteSQL->execute([$id_tipo_permiso]);
    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    header('Location: tabla_permisos.php');
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
                <td><input name="id_tipo_permiso" value="<?php echo $usua['id_tipo_permiso']; ?>" readonly> </td>
            </tr>

            <tr>
                <td>Tipo Usuario</td>
                <td><input name ="tipo_permiso" value="<?php echo $usua['tipo_permiso']?>" ></td>
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
                            