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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/tablas.css">
    <title>Tabla de Permisos</title>
</head>
<body>
    <h2>Tabla Permisos</h2>
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th>ID Permiso</th>
                    <th>Tipo Permiso</th>
                    <th>
                        <a href="admin.php">REGRESAR</a>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
                $query = $con->prepare("SELECT * FROM tipo_permiso");
                $query->execute();
                $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

                foreach($resultados as $fila){
            ?>
            <tr>
                <td><?php echo $fila['id_tipo_permiso'] ?></td>
                <td><?php echo $fila['tipo_permiso'] ?></td>
                <td>
                    <a href='update_tipo_permiso.php?id=<?php echo $fila['id_tipo_permiso'] ?>' class='btn' onclick="window.open('update_tipo_permiso.php?id=<?php echo $fila['id_tipo_permiso'] ?>','','width=500,height=500,toolbar=NO'); return false;">Actualizar</a>
                </td>
            </tr>
            <?php
                }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
