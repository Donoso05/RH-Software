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

$sql = $con->prepare("SELECT * FROM tram_permiso,tipo_permiso,estado WHERE tram_permiso.id_tipo_permiso = tipo_permiso.id_tipo_permiso AND tram_permiso.id_estado = estado.id_estado AND tram_permiso.id_permiso = '" . $_GET['id'] . "'");
$sql->execute();
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_permiso = $_POST['id_permiso'];
    $id_usuario = $_POST['id_usuario'];
    $id_tipo_permiso = $_POST['id_tipo_permiso'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $id_estado = $_POST['id_estado'];
    $incapacidad = $_POST['incapacidad'];
    $insertSQL = $con->prepare("UPDATE tram_permiso SET id_permiso = '$id_permiso', id_usuario = '$id_usuario', id_tipo_permiso = '$id_tipo_permiso', fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin', id_estado = '$id_estado', incapacidad = '$incapacidad'
    WHERE id_permiso = '" . $_GET['id'] . "'");
    $insertSQL->execute();
    echo '<script>alert ("Actualización Exitosa");</script>';
    echo '<script>window.close();</script>';
}


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/ingreso2.css">
    <title>Editar</title>

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
    <link rel="stylesheet" type="text/css" href="../css/ingreso2.css" th:href="@{/css/ingreso2.css}">
    <!-- DATA TABLE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

</head>

<body>
    <main>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h4>User Information</h4>
                </div>
                <?php //foreach ($resultados as $fila) { 
                ?>
                <div class="card-body">
                    <form action="" class="form" method="post" role="form" autocomplete="off">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Id_Permiso</label>
                            <div class="col-lg-9">
                                <input name="id_permiso" value="<?php echo $usua['id_permiso'] ?>" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Documento</label>
                            <div class="col-lg-9">
                                <input name="id_usuario" value="<?php echo $usua['id_usuario'] ?>" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Tipo Permiso</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="id_tipo_permiso">
                                    <option value="">Seleccione uno</option>
                                    <?php
                                    $control = $con->prepare("select * from tipo_permiso where id_tipo_permiso ");
                                    $control->execute();
                                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($fila['id_tipo_permiso'] == $usua['id_tipo_permiso']) ? 'selected' : '';
                                        echo "<option value=" . $fila['id_tipo_permiso'] . " $selected>" . $fila['tipo_permiso'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Fecha Inicio</label>
                            <div class="col-lg-9">
                                <div class="col-lg-9">
                                    <input name="fecha_inicio" value="<?php echo $usua['fecha_inicio'] ?>" class="form-control" type="date">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Fecha Fin</label>
                            <div class="col-lg-9">
                                <div class="col-lg-9">
                                    <input name="fecha_fin" value="<?php echo $usua['fecha_fin'] ?>" class="form-control" type="date">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Estado</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="id_estado" >
                                <option value="">Seleccione uno</option>
                                <?php
                                $control = $con->prepare("SELECT * FROM estado WHERE id_estado > 10");
                                $control->execute();
                                while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($fila['id_estado'] == $usua['id_estado']) ? 'selected' : '';
                                    echo "<option value=" . $fila['id_estado'] . " $selected>" . $fila['estado'] . "</option>";
                                }
                                ?>
                                </select>
                            </div>
                            <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Incapacidad</label>
                            <div class="col-lg-9">
                                <div class="col-lg-9">
                                    <textarea class="input100" cols="50" name="incapacidad"  placeholder="Detalles de incapacidad"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-center">
                                <input name="update" type="submit" class="btn btn-primary" value="Save Changes" onclick="validarContrasena()" >
                            </div>
                        </div>

                        </div>
                        <?php //} 
                        ?>
                    </form>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                        </div>
                    </div>
                </div>
    </main>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>