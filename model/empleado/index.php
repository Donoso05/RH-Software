<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

// Obtener el id de usuario de la sesión
$id_usuario = $_SESSION["id_usuario"];

// Consultar información del usuario
$sql = "SELECT u.nombre, u.id_usuario, u.correo, u.nit_empresa, u.id_estado, e.estado, u.id_tipo_cargo, c.cargo, u.foto
        FROM usuario u
        INNER JOIN estado e ON u.id_estado = e.id_estado
        INNER JOIN tipo_cargo c ON u.id_tipo_cargo = c.id_tipo_cargo
        WHERE u.id_usuario = ?";

$stmt = $con->prepare($sql);
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $nombre = $result["nombre"];
    $id_usuario = $result["id_usuario"];
    $correo = $result["correo"];
    $nit_empresa = $result["nit_empresa"];
    $estado = $result["estado"];
    $cargo = $result["cargo"];
    $foto = $result["foto"];
} else {
    // Si no se encuentra el usuario, redirigir o manejar el error de alguna forma
    exit("Usuario no encontrado");
}

$stmt->closeCursor();

// Verificar si el usuario ha cambiado su contraseña
$sql = "SELECT COUNT(*) FROM triggers WHERE id_usuario = ?";
$stmt = $con->prepare($sql);
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$password_changed = $stmt->fetchColumn() > 0;
$stmt->closeCursor();
$con = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include("nav.php") ?>

    <div class="formulario">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo !empty($foto) ? $foto : 'img/user.webp'; ?>" class="img-fluid rounded" alt="Foto de perfil">
                            <form action="upload.php" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
                                <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
                                <br>
                                <br>
                                <input type="submit" class="btn btn-primary" value="Actualizar">
                            </form>
                        </div>
                        <div class="col-md-8">
                            <h2><?php echo $nombre; ?></h2>
                            <p><strong>ID de Usuario:</strong> <?php echo $id_usuario; ?></p>
                            <p><strong>Correo:</strong> <?php echo $correo; ?></p>
                            <p><strong>NIT de la Empresa:</strong> <?php echo $nit_empresa; ?></p>
                            <p><strong>Estado:</strong> <?php echo $estado; ?></p>
                            <p><strong>Cargo:</strong> <?php echo $cargo; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$password_changed): ?>
    <!-- Modal para cambiar la contraseña -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h3 class="modal-title" id="changePasswordModalLabel">Cambiar contraseña</h3>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="cambiar_contrasena.php" method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            // Show the modal if password has not been changed
            <?php if (!$password_changed): ?>
                $('#changePasswordModal').modal({ backdrop: 'static', keyboard: false });
                $('#changePasswordModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html>
