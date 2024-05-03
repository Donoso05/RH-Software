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
    <title>Tabla Usuarios</title>
</head>
<body>
    <h2>Registos de Usuarios</h2>
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre </th>
                    <th>Cargo</th>
                    <th>Estado</th>
                    <th>Correo</th>
                    <th>Tipo Usuario</th>
                    <th>NIT Empresa</th>
                    <th>
                    <a href="admin.php">REGRESAR</a>
                </th>
                </tr>
            </thead>
            <tbody>

                <?php
                $query = $con->prepare("SELECT usuario.id_usuario,usuario.nombre,tipo_cargo.cargo,estado.estado,usuario.correo,tipos_usuarios.tipo_usuario,
                usuario.nit_empresa FROM usuario JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo JOIN estado ON usuario.id_estado = estado.id_estado
                JOIN tipos_usuarios ON usuario.id_tipo_usuario = tipos_usuarios.id_tipo_usuario ");
                $query->execute();
                $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

                foreach ($resultados as $fila) {
                    echo "<tr>";
                    echo "<td>{$fila['id_usuario']}</td>";
                    echo "<td>{$fila['nombre']}</td>";
                    echo "<td>{$fila['cargo']}</td>";
                    echo "<td>{$fila['estado']}</td>";
                    echo "<td>{$fila['correo']}</td>";
                    echo "<td>{$fila['tipo_usuario']}</td>";
                    echo "<td>{$fila['nit_empresa']}</td>";
                    echo "<td><a href='update_usuario.php?id={$fila['id_usuario']}' class='btn' onclick=\"window.open('update_usuario.php?id={$fila['id_usuario']}','','width=500,height=500,toolbar=NO'); return false;\">Actualizar</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
