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
    $cargo = trim($_POST['cargo']);
    $salario_base = trim($_POST['salario_base']);
    $id_arl = trim($_POST['id_arl']);

    if (empty($cargo) || empty($salario_base) || empty($id_arl)) {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="tipo_cargo.php"</script>';
    } elseif (!preg_match('/[a-zA-Z]/', $cargo) || !is_numeric($salario_base) || !is_numeric($id_arl)) {
        echo '<script>alert("Datos inválidos. Verifique que el cargo contenga letras y que el salario y ARL sean números válidos.");</script>';
        echo '<script>window.location="tipo_cargo.php"</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO tipo_cargo (cargo, salario_base, id_arl, nit_empresa) VALUES (?, ?, ?, ?)");
        $insertSQL->execute([$cargo, $salario_base, $id_arl, $nit_empresa_session]);
        echo '<script>alert("Registro exitoso");</script>';
        echo '<script>window.location="tipo_cargo.php"</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Cargo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script>
        function validateLetters(input) {
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '').trimStart();
        }

        function validateNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, '').trimStart();
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <div class="col-12 col-md-3 p-3">
            <div class="card">
                <h3 class="text-center text-secondary">Registrar Tipos Cargo</h3>
                <form method="post">
                    <div class="mb-3">
                        <label for="cargo" class="form-label">Tipo Cargo:</label>
                        <input type="text" class="form-control" name="cargo" oninput="validateLetters(this)" pattern=".*[a-zA-Z]+.*" required autocomplete="off" value="">
                    </div>
                    <div class="mb-3">
                        <label for="salario_base" class="form-label">Salario Base:</label>
                        <input type="number" class="form-control" name="salario_base" oninput="validateNumbers(this)" required autocomplete="off" value="">
                    </div>
                    <div class="mb-3">
                        <label for="id_arl" class="form-label">ARL:</label>
                        <select class="form-control" name="id_arl" required>
                            <option value="">Selecciona el Tipo de ARL</option>
                            <?php
                            $control = $con->prepare("SELECT * FROM arl");
                            $control->execute();
                            while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . htmlspecialchars($fila['id_arl'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($fila['tipo'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                    <input type="hidden" name="MM_insert" value="formreg">
                </form>
            </div>
        </div>
        <div class="col-12 col-md-9 p-4">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th scope="col">Tipo Cargo</th>
                            <th scope="col">Salario Base</th>
                            <th scope="col">ARL</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta de cargos filtrando por el mismo nit_empresa del usuario en sesión
                        $consulta = $con->prepare("SELECT tc.cargo, tc.salario_base, a.tipo, tc.id_tipo_cargo 
                                                   FROM tipo_cargo tc
                                                   JOIN arl a ON tc.id_arl = a.id_arl
                                                   WHERE tc.nit_empresa = ?");
                        $consulta->execute([$nit_empresa_session]);
                        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($resultado as $fila) {
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila["cargo"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($fila["salario_base"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($fila["tipo"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-start">
                                            <a href="update_cargo.php?id=<?php echo $fila['id_tipo_cargo']; ?>" onclick="window.open('./update/update_cargo.php?id=<?php echo $fila['id_tipo_cargo']; ?>','','width=500,height=500,toolbar=NO'); return false;" class="btn btn-primary">Editar</a>
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
    </div>
</body>

</html>
