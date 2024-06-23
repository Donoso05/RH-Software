<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$id_usuario = $_SESSION["id_usuario"];

$caracteres = "lkjhsysaASMNB8811AMMaksjyuyysth098765432%#%poiyAZXSDEWQjhhs";
$long = 20;
$licencia = substr(str_shuffle($caracteres), 0, $long);
date_default_timezone_set("America/Mexico_City");
$f_hoy = date('Y-m-d');
$fin = date("Y-m-d", strtotime($f_hoy . "+ 1 year"));

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $nit_empresa = $_POST['nit_empresa'];
    $licencia = $_POST['licencia'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_final = $_POST['fecha_final'];

    if ($nit_empresa == "" || $licencia == "") {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
    } else {
        // Verificar si ya existe una licencia para el nit_empresa
        $checkSQL = $con->prepare("SELECT COUNT(*) FROM licencia WHERE nit_empresa = :nit_empresa");
        $checkSQL->bindParam(':nit_empresa', $nit_empresa);
        $checkSQL->execute();
        $count = $checkSQL->fetchColumn();

        if ($count > 0) {
            echo '<script>alert ("Ya existe una licencia para esta empresa."); </script>';
        } else {
            // Insertar nueva licencia
            $insertSQL = $con->prepare("INSERT INTO licencia (nit_empresa, licencia, fecha_inicio, fecha_final) 
            VALUES (:nit_empresa, :licencia, :fecha_inicio, :fecha_final)");
            $insertSQL->bindParam(':nit_empresa', $nit_empresa);
            $insertSQL->bindParam(':licencia', $licencia);
            $insertSQL->bindParam(':fecha_inicio', $fecha_inicio);
            $insertSQL->bindParam(':fecha_final', $fecha_final);
            $insertSQL->execute();

            // Obtener el ID de la licencia recién insertada
            $id_licencia = $con->lastInsertId();

            // Actualizar la empresa para asignar la licencia
            $updateSQL = $con->prepare("UPDATE empresas SET id_licencia = :id_licencia WHERE nit_empresa = :nit_empresa");
            $updateSQL->bindParam(':id_licencia', $id_licencia);
            $updateSQL->bindParam(':nit_empresa', $nit_empresa);
            $updateSQL->execute();

            echo '<script>alert ("Licencia asignada con éxito"); </script>';
            echo '<script>window.location="index.php"</script>';
        }
    }
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formemp")) {
    $nit = $_POST['nit'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    if ($nit == "" || $nombre == "" || $correo == "") {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
    } else {
        // Verificar si ya existe una empresa con el mismo NIT o correo
        $checkSQL = $con->prepare("SELECT COUNT(*) FROM empresas WHERE nit_empresa = :nit OR correo = :correo OR nombre = :nombre");
        $checkSQL->bindParam(':nit', $nit);
        $checkSQL->bindParam(':correo', $correo);
        $checkSQL->bindParam(':nombre', $nombre);
        $checkSQL->execute();
        $count = $checkSQL->fetchColumn();

        if ($count > 0) {
            echo '<script>alert ("Ya existe una empresa con esos datos de registro"); </script>';
        } else {
            // Insertar nueva empresa
            $insertSQL = $con->prepare("INSERT INTO empresas (nit_empresa, nombre, correo) 
            VALUES (:nit, :nombre, :correo)");
            $insertSQL->bindParam(':nit', $nit);
            $insertSQL->bindParam(':nombre', $nombre);
            $insertSQL->bindParam(':correo', $correo);
            $insertSQL->execute();
            echo '<script>alert ("Empresa creada con éxito"); </script>';
            echo '<script>window.location="index.php"</script>';
        }
    }
}

$empresas = $con->prepare("SELECT e.nit_empresa, e.nombre, l.licencia, e.correo
    FROM empresas e
    LEFT JOIN licencia l ON e.nit_empresa = l.nit_empresa
");
$empresas->execute();
$empresas_data = $empresas->fetchAll(PDO::FETCH_ASSOC);

$administradores = $con->prepare("SELECT u.id_usuario, u.nombre, e.estado, u.correo, tu.tipo_usuario, u.nit_empresa
    FROM usuario u
    LEFT JOIN estado e ON u.id_estado = e.id_estado
    LEFT JOIN tipos_usuarios tu ON u.id_tipo_usuario = tu.id_tipo_usuario
    WHERE u.id_tipo_usuario = 1
");
$administradores->execute();
$administradores_data = $administradores->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Panel Desarrollador</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../controller/cerrarcesion.php">Cerrar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="row g-4">
            <div class="col-12">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card bg-light text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Crear Empresas</h5>
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="nit" class="form-label text-dark">NIT</label>
                                        <input type="text" name="nit" id="nit" class="form-control" placeholder="NIT">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre Empresa</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre Empresa">
                                    </div>
                                    <div class="mb-3">
                                        <label for="correo" class="form-label">Correo Electrónico</label>
                                        <input type="email" name="correo" id="correo" class="form-control" placeholder="Correo Electrónico">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                    <input type="hidden" name="MM_insert" value="formemp">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card bg-light text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Crear Licencia</h5>
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="serial" class="form-label">Serial</label>
                                        <input type="text" name="licencia" id="serial" class="form-control" value="<?php echo htmlspecialchars($licencia, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fechainicio" class="form-label">Fecha Inicio</label>
                                        <input type="text" name="fecha_inicio" id="fechainicio" class="form-control" value="<?php echo htmlspecialchars($f_hoy, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fechafin" class="form-label">Fecha Fin</label>
                                        <input type="text" name="fecha_final" id="fechafin" class="form-control" value="<?php echo htmlspecialchars($fin, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="empresa" class="form-label">Empresa</label>
                                        <select name="nit_empresa" id="empresa" class="form-select">
                                            <option value="">Seleccione una Empresa</option>
                                            <?php
                                            $control = $con->prepare("SELECT * FROM empresas");
                                            $control->execute();
                                            while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='" . htmlspecialchars($fila['nit_empresa'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Crear</button>
                                    <input type="hidden" name="MM_insert" value="formreg">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card bg-light text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Crear Administrador</h5>
                                <form method="post" action="cadmin.php">
                                    <div class="mb-3">
                                        <label for="documento" class="form-label">Documento</label>
                                        <input type="text" name="documento" id="documento" class="form-control" placeholder="Documento">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre">
                                    </div>
                                    <div class="mb-3">
                                        <label for="correo" class="form-label">Correo Electrónico</label>
                                        <input type="email" name="correo" id="correo" class="form-control" placeholder="Correo Electrónico">
                                    </div>
                                    <div class="mb-3">
                                        <label for="empresa" class="form-label">Empresa</label>
                                        <select name="empresa" id="empresa" class="form-select" required>
                                            <option value="">Seleccione una Empresa</option>
                                            <?php
                                            $control = $con->prepare("SELECT * FROM empresas");
                                            $control->execute();
                                            while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='" . htmlspecialchars($fila['nit_empresa'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                    <input type="hidden" name="MM_insert" value="formreg">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Empresas</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>NIT Empresa</th>
                                        <th>Nombre</th>
                                        <th>Licencia</th>
                                        <th>Correo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($empresas_data as $empresa): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($empresa['nit_empresa'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($empresa['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($empresa['licencia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($empresa['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Administradores</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Documento</th>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th>Correo</th>
                                        <th>Tipo Usuario</th>
                                        <th>NIT Empresa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($administradores_data as $admin): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($admin['id_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($admin['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($admin['estado'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($admin['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($admin['tipo_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($admin['nit_empresa'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>
