<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();
$nit_empresa_session = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $valor = $_POST['valor'];

    if ($valor == "") {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="auxtransporte.php"</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO auxtransporte (valor, nit_empresa) VALUES (?, ?)");
        $insertSQL->execute([$valor, $nit_empresa_session]);
        echo '<script>alert("Registro exitoso");</script>';
        echo '<script>window.location="auxtransporte.php"</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxilio de Transporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
            valorInput.addEventListener('keypress', function (e) {
                sinEspaciosIniciales(e);
                soloNumeros(e);
            });
        });
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post">
            <h3 class="text-center text-secondary">Auxilio</h3>
            <div class="mb-3">
                <label for="valor" class="form-label">Auxilio de Transporte:</label>
                <input type="number" class="form-control" id="valor" name="valor" required>
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg" required>
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Auxilio </th>
                        <th scope="col">Valor</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Consulta de auxilios filtrando por el mismo nit_empresa del usuario en sesión
                    $consulta = $con->prepare("SELECT * FROM auxtransporte WHERE nit_empresa = ?");
                    $consulta->execute([$nit_empresa_session]);
                    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($resultado as $fila) {
                ?>
                        <tr>
                            <td><?php echo "Auxilio de Transporte"; ?></td> 
                            <td><?php echo htmlspecialchars($fila["valor"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="update_aux.php?id=<?php echo $fila['id_auxtransporte']; ?>" onclick="window.open('./update/update_aux.php?id=<?php echo $fila['id_auxtransporte']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                <?php   
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
