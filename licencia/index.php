<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}

require_once("../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
$con = $db->conectar();

// Obtener el id de usuario de la sesión
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

    $sql = $con->prepare("SELECT * FROM licencia");
    $sql->execute();
    $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($nit_empresa == "" || $licencia == "") {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO licencia(nit_empresa, licencia, fecha_inicio, fecha_final) 
        VALUES ('$nit_empresa','$licencia', '$fecha_inicio', '$fecha_final')");
        $insertSQL->execute();
        echo '<script>alert ("licencia asignada con exito"); </script>';
        echo '<script>window.location="index.php"</script>';
    }
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formemp")) {
    $nit = $_POST['nit'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    $sql = $con->prepare("SELECT * FROM empresas");
    $sql->execute();
    $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($nit == "" || $nombre == "" || $correo == "") {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO empresas(nit_empresa, nombre, correo) 
        VALUES ('$nit','$nombre', '$correo')");
        $insertSQL->execute();
        echo '<script>alert ("Empresa creada con exito"); </script>';
        echo '<script>window.location="index.php"</script>';
    }
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formemp")) {
    $nit = $_POST['nit'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    $sql = $con->prepare("SELECT * FROM empresas");
    $sql->execute();
    $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($nit == "" || $nombre == "" || $correo == "") {
        echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO empresas(nit_empresa, nombre, correo) 
        VALUES ('$nit','$nombre', '$correo')");
        $insertSQL->execute();
        echo '<script>alert ("Empresa creada con exito"); </script>';
        echo '<script>window.location="index.php"</script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <div class="form-wrapper">
            <form class="form" method="post">
                <h2>Crear Empresas</h2>
                <div class="form-group">
                    <label for="nit">NIT</label>
                    <input type="text" name="nit" id="nit" placeholder="NIT">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre Empresa</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre Empresa">
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" placeholder="Correo Electrónico">
                </div>
                <input type="submit" name="validar" value="Registrar" class="btn">
                <input type="hidden" name="MM_insert" value="formemp">
            </form>

            <form class="form" method="post">
                <h2>Crear Licencia</h2>
                <div class="form-group">
                    <label for="serial">Serial</label>
                    <input type="text" name="licencia" id="serial" value="<?php echo $licencia ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="fechainicio">Fecha Inicio</label>
                    <input type="text" name="fecha_inicio" id="fechainicio" value="<?php echo $f_hoy ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="fechafin">Fecha Fin</label>
                    <input type="text" name="fecha_final" id="fechafin" value="<?php echo $fin ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa</label>
                    <select name="nit_empresa" id="empresa">
                        <?php
                        $control = $con->prepare("SELECT * FROM empresas");
                        $control->execute();
                        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fila['nit_empresa'] . "'>" . $fila['nombre'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn">Crear</button>
                <input type="hidden" name="MM_insert" value="formreg">
            </form>
            <form class="form" method="post" action="camind.php">
                <h2>Crear Administrador</h2>
                <div class="form-group">
                    <label for="docuemnto">Documento</label>
                    <input type="text" name="nombre" id="documento" placeholder="Documento">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre">
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" placeholder="Correo Electrónico">
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa</label>
                    <select name="empresa" id="empresa">
                        <?php
                        $control = $con->prepare("SELECT * FROM empresas");
                        $control->execute();
                        while ($fila = $control->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fila['nit_empresa'] . "'>" . $fila['nombre'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" name="validar" value="Registrar" class="btn">
                <input type="hidden" name="MM_insert" value="formemp">
            </form>
        </div>
    </div>
</body>

</html>
