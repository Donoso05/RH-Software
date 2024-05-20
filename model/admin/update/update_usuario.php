<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario = '" . $_GET['id'] . "'");
$sql->execute();
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $id_tipo_cargo = $_POST['id_tipo_cargo'];
    $id_estado = $_POST['id_estado'];
    $correo = $_POST['correo'];
    $id_tipo_usuario = $_POST['id_tipo_usuario'];
    $updateSQL = $con->prepare("UPDATE usuario SET nombre = '$nombre', id_tipo_cargo = '$id_tipo_cargo', id_estado = '$id_estado', correo = '$correo', id_tipo_usuario = '$id_tipo_usuario' WHERE id_usuario = '" . $_GET['id'] . "'");

    $updateSQL->execute();
    echo '<script>alert("Actualización Exitosa");</script>';
    echo '<script>window.close();</script>';
} elseif (isset($_POST["delete"])) {
    $id_usuario = $_POST['id_usuario'];

    $deleteSQL = $con->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $deleteSQL->execute([$id_usuario]);
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

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
                <form action="" class="form" name="frm_consulta" method="POST" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Documento</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_usuario" value="<?php echo $usua['id_usuario'] ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Nombre</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="nombre" value="<?php echo $usua['nombre'] ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Cargo</label>
                        <div class="col-lg-9">
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
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Estado</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_estado" id="id_estado">
                                <?php
                                $control = $con->prepare("select * from estado where id_estado <= 2");
                                $control->execute();
                                while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($fila['id_estado'] == $usua['id_estado']) ? 'selected' : '';
                                    echo "<option value=" . $fila['id_estado'] . " $selected>" . $fila['estado'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Correo</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="correo" value="<?php echo $usua['correo'] ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Tipo Usuario</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_tipo_usuario" id="id_tipo_usuario">
                                <?php
                                $control = $con->prepare("select * from tipos_usuarios where id_tipo_usuario");
                                $control->execute();
                                while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($fila['id_tipo_usuario'] == $usua['id_tipo_usuario']) ? 'selected' : '';
                                    echo "<option value=" . $fila['id_tipo_usuario'] . " $selected>" . $fila['tipo_usuario'] . "</option>";
                                }
                                ?>
                            </select>
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
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este usuario?");
        }
    </script>
</body>

</html>