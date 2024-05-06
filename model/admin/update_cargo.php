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

$sql = $con->prepare("SELECT * FROM tipo_cargo,arl WHERE tipo_cargo.id_arl = arl.id_arl AND tipo_cargo.id_tipo_cargo = '" . $_GET['id'] . "'");
$sql->execute();
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_tipo_cargo = $_POST['id_tipo_cargo'];
    $cargo= $_POST['cargo'];
    $salario_base = $_POST['salario_base'];
    $id_arl = $_POST['id_arl'];
    $insertSQL = $con->prepare("UPDATE tipo_cargo SET id_tipo_cargo = '$id_tipo_cargo', cargo = '$cargo', salario_base = '$salario_base', id_arl = '$id_arl'
    WHERE id_tipo_cargo = '" . $_GET['id'] . "'");
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
                            <label class="col-lg-3 col-form-label form-control-label">Id_Tipo_Cargo</label>
                            <div class="col-lg-9">
                                <input name="id_tipo_cargo" value="<?php echo $usua['id_tipo_cargo'] ?>" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Cargo</label>
                            <div class="col-lg-9">
                                <input name="cargo" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="No es posible ingresar números en el nombre" value="<?php echo $usua['cargo'] ?>" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Salario Base</label>
                            <div class="col-lg-9">
                                <input name="salario_base" type="text" pattern="[0-9]*" title="Ingrese solo números" value="<?php echo $usua['salario_base'] ?>" class="form-control" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">ARL</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="id_arl">
                                    <option value="">Seleccione uno</option>
                                    <?php
                                    $control = $con->prepare("select * from arl where id_arl ");
                                    $control->execute();
                                    while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($fila['id_arl'] == $usua['id_arl']) ? 'selected' : '';
                                        echo "<option value=" . $fila['id_arl'] . " $selected>" . $fila['tipo'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-center">
                                <input name="update" type="submit" class="btn btn-primary" value="Save Changes" onclick="validarContrasena()" >
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