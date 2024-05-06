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

$sql = $con->prepare("SELECT * FROM solic_prestamo WHERE id_usuario = '" . $_GET['id'] . "'");
$sql->execute();
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_prestamo= $_POST['id_prestamo'];
    $id_usuario= $_POST['id_usuario'];
    $monto_solicitado= $_POST['monto_solicitado'];
    $id_estado= $_POST['id_estado'];
    $valor_cuotas= $_POST['valor_cuotas']; 
    $cant_cuotas= $_POST['cant_cuotas'];
    $updateSQL = $con->prepare("UPDATE solic_prestamo SET id_usuario ='$id_usuario', monto_solicitado = '$monto_solicitado', id_estado = '$id_estado', valor_cuotas = '$valor_cuotas', cant_cuotas = '$cant_cuotas' WHERE id_usuario = '" . $_GET['id'] . "'");

    $updateSQL->execute();
    echo '<script>alert("Actualización Exitosa");</script>';
    echo '<script>window.close();</script>';
} elseif (isset($_POST["delete"])) {
    $id_prestamo = $_POST['id_prestamo'];

    $deleteSQL = $con->prepare("DELETE FROM solic_prestamo WHERE id_prestamo = ?");
    $deleteSQL->execute([$id_prestamo]);
    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    header('Location: tabla_prestamos.php');
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
                <td>ID</td>
                <td><input name="id_prestamo" value="<?php echo $usua['id_prestamo'] ?>" readonly></td>
            </tr>

            <tr>
                <td>Docuemnto</td>
                <td><input name="id_usuario" value="<?php echo $usua['id_usuario'] ?>" readonly></td>
            </tr>

            <tr>
                <td>Monto Solicitado</td>
                <td><input name="monto_solicitado" value="<?php echo $usua['monto_solicitado'] ?>"></td>
            </tr>

            <tr>
                <td>Estado</td>
                <td>
                    <select class="form-control" name="id_estado">
                        <?php
                        $control = $con->prepare("SELECT * FROM estado WHERE id_estado BETWEEN 6 AND 10");
                        $control->execute();
                        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fila['id_estado'] . "'>" . $fila['estado'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Valor Cuota</td>
                <td><input name="valor_cuotas" value="<?php echo $usua['valor_cuotas'] ?>" ></td>
            </tr>

            <tr>
                <td>Cantidad Cuota</td>
                <td><input name="cant_cuotas" value="<?php echo $usua['cant_cuotas'] ?>"></td>
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
