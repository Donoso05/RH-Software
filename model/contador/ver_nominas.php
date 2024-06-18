<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();



// Resto de la validación
$sql = $con->prepare("SELECT * FROM usuario ");
$sql->execute();
$fila = $sql->fetch(PDO::FETCH_ASSOC);




?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .table thead {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
    <h3 class="text-center text-secondary my-4">Nóminas</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Documento</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Tipo Usuario</th>
                            <th scope="col">Acciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta de armas
                        $consulta = "SELECT usuario.id_usuario, usuario.nombre, tipo_cargo.cargo AS tipo_cargo, estado.estado AS estado, usuario.correo, tipos_usuarios.tipo_usuario, usuario.contrasena, usuario.nit_empresa 
                 FROM usuario 
                 INNER JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo 
                 INNER JOIN tipos_usuarios ON usuario.id_tipo_usuario = tipos_usuarios.id_tipo_usuario 
                 INNER JOIN estado ON usuario.id_estado = estado.id_estado";
                        $resultado = $con->query($consulta);

                        while ($fila = $resultado->fetch()) {
                        ?>
                            <tr>
                                <td><?php echo $fila["id_usuario"]; ?></td>
                                <td><?php echo $fila["nombre"]; ?></td>
                                <td><?php echo $fila["tipo_cargo"]; ?></td>
                                <td><?php echo $fila["correo"]; ?></td>
                                <td><?php echo $fila["tipo_usuario"]; ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-start">
                                            <a href="liquidar.php?id_usuario=<?php echo $fila['id_usuario']; ?>" class="ms-2">
                                                <i class="btn btn-danger">Liquidar</i>
                                            </a>

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