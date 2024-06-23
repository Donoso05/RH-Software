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

// Obtener el nit_empresa del usuario logueado
$id_usuario_sesion = $_SESSION["id_usuario"];
$sqlUsuario = $con->prepare("SELECT nit_empresa FROM usuario WHERE id_usuario = ?");
$sqlUsuario->execute([$id_usuario_sesion]);
$nit_empresa = $sqlUsuario->fetchColumn();

$sql = $con->prepare("SELECT * FROM tipo_cargo, arl WHERE tipo_cargo.id_arl = arl.id_arl AND tipo_cargo.id_tipo_cargo = ? AND tipo_cargo.nit_empresa = ?");
$sql->execute([$_GET['id'], $nit_empresa]);
$usua = $sql->fetch();

if (!$usua) {
    echo '<script>alert("No se encontraron resultados para el tipo de cargo especificado.");</script>';
    echo '<script>window.location.href = "../listado_cargos.php";</script>';
    exit();
}

if (isset($_POST["update"])) {
    $id_tipo_cargo = $_POST['id_tipo_cargo'];
    $cargo = trim($_POST['cargo']);
    $salario_base = trim($_POST['salario_base']);
    $id_arl = trim($_POST['id_arl']);

    if (empty($cargo) || empty($salario_base) || empty($id_arl)) {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
    } elseif (!preg_match('/[a-zA-Z]/', $cargo) || !is_numeric($salario_base) || !is_numeric($id_arl)) {
        echo '<script>alert("Datos inválidos. Verifique que el cargo contenga letras y que el salario y ARL sean números válidos.");</script>';
    } else {
        $insertSQL = $con->prepare("UPDATE tipo_cargo SET cargo = ?, salario_base = ?, id_arl = ? WHERE id_tipo_cargo = ? AND nit_empresa = ?");
        $insertSQL->execute([$cargo, $salario_base, $id_arl, $id_tipo_cargo, $nit_empresa]);
        echo '<script>alert("Actualización Exitosa");</script>';
        echo '<script>window.close();</script>';
    }
} elseif (isset($_POST["delete"])) {
    $id_tipo_cargo = $_POST['id_tipo_cargo'];

    $deleteSQL = $con->prepare("DELETE FROM tipo_cargo WHERE id_tipo_cargo = ? AND nit_empresa = ?");
    $deleteSQL->execute([$id_tipo_cargo, $nit_empresa]);
    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    echo '<script>window.close();</script>';
    exit;
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
    <link rel="stylesheet" type="text/css" href="../css/ingreso2.css">
    <!-- DATA TABLE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

    <script>
        function validateLetters(input) {
            input.value = input.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '').trimStart();
        }

        function validateNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, '').trimStart();
        }
    </script>
</head>
<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h4>Actualizar Tipo de Cargo</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" method="post" role="form" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Id_Tipo_Cargo</label>
                        <div class="col-lg-9">
                            <input name="id_tipo_cargo" value="<?php echo htmlspecialchars($usua['id_tipo_cargo'], ENT_QUOTES, 'UTF-8') ?>" class="form-control" type="text" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Cargo</label>
                        <div class="col-lg-9">
                            <input name="cargo" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="No es posible ingresar números en el nombre" value="<?php echo htmlspecialchars($usua['cargo'], ENT_QUOTES, 'UTF-8') ?>" class="form-control" type="text" oninput="validateLetters(this)" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Salario Base</label>
                        <div class="col-lg-9">
                            <input name="salario_base" type="text" pattern="[0-9]*" title="Ingrese solo números" value="<?php echo htmlspecialchars($usua['salario_base'], ENT_QUOTES, 'UTF-8') ?>" class="form-control" oninput="validateNumbers(this)" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">ARL</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="id_arl" required>
                                <option value="">Seleccione uno</option>
                                <?php
                                $control = $con->prepare("SELECT * FROM arl WHERE nit_empresa = ?");
                                $control->execute([$nit_empresa]);
                                while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($fila['id_arl'] == $usua['id_arl']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($fila['id_arl'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($fila['tipo'], ENT_QUOTES, 'UTF-8') . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <script>
                        function confirmarEliminacion() {
                            return confirm("¿Estás seguro de que deseas eliminar este Cargo?");
                        }
                    </script>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input name="update" type="submit" class="btn btn-primary" value="Actualizar">
                            <button class="btn btn-danger" name="delete" onclick="return confirmarEliminacion()">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
    </main>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6jty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
