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

$sql = $con->prepare("SELECT * FROM auxtransporte WHERE auxtransporte.id_auxtransporte = '" . $_GET['id'] . "'");
$sql->execute();
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_auxtransporte = $_POST['id_auxtransporte'];
    $valor = $_POST['valor'];

    if (!preg_match('/\d/', $valor)) {
        echo '<script>alert("El campo \'Auxilio Transporte\' debe contener al menos un número.");</script>';
    } else {
        $insertSQL = $con->prepare("UPDATE auxtransporte SET id_auxtransporte = ?, valor = ? WHERE id_auxtransporte = ?");
        $insertSQL->execute([$id_auxtransporte, $valor, $_GET['id']]);
        echo '<script>alert("Actualización Exitosa");</script>';
        echo '<script>window.close();</script>';
    }
} elseif (isset($_POST["delete"])) {
    $id_auxtransporte = $_POST['id_auxtransporte'];

    $deleteSQL = $con->prepare("DELETE FROM auxtransporte WHERE id_auxtransporte = ?");
    $deleteSQL->execute([$id_auxtransporte]);
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
    <title>Actualizar Auxilio Transporte</title>

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
                <h4>Actualizar Auxilio Transporte</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" name="frm_consulta" method="POST" autocomplete="off" onsubmit="return validarFormulario()">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">ID</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_auxtransporte" value="<?php echo $usua['id_auxtransporte']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Auxilio Transporte</label>
                        <div class="col-lg-9">
                            <input class="form-control" id="valor" name="valor" value="<?php echo $usua['valor']; ?>" required>
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
            return confirm("¿Estás seguro de que deseas eliminar este Auxilio Transporte?");
        }

        function validarFormulario() {
            const valor = document.forms["frm_consulta"]["valor"].value.trim();

            // Verificar que el campo no esté vacío y contenga al menos un número
            if (valor === "" || !/\d/.test(valor)) {
                alert("El campo 'Auxilio Transporte' debe contener al menos un número.");
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const valorInput = document.getElementById('valor');

            // Función para validar entrada solo de números
            function soloNumeros(e) {
                const char = String.fromCharCode(e.which);
                if (!/[0-9]/.test(char)) {
                    e.preventDefault();
                }
            }

            // Función para eliminar espacios iniciales
            function sinEspaciosIniciales(e) {
                if (e.target.value === '' && e.which === 32) {
                    e.preventDefault();
                }
            }

            // Asignar eventos para validar la entrada
            valorInput.addEventListener('keypress', function(e) {
                sinEspaciosIniciales(e);
                soloNumeros(e);
            });
        });
    </script>
</body>
</html>
