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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }

        .card {
            margin-top: 20px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .formulario {
            padding: 2rem 0;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }
    </style>
</head>
<body>
    <?php include("nav.php") ?>

    <div class="formulario">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="profile-container">
                                <div class="profile-img-wrapper">
                                <img src="<?php echo !empty($foto) ? $foto : 'img/user.webp'; ?>" class="rounded-0" alt="Foto de perfil" style="width:50%"  >
                                </div>
                                <form action="upload.php" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
                                    <div class="file-input-wrapper">
                                        <label class="file-input">
                                            <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
                                            Actualizar Foto
                                        </label>
                                    </div>
                                    <br>
                                    <input type="submit" class="btn btn-primary mt-3" value="Actualizar">
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h2><?php echo $nombre; ?></h2>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Documento:</strong></div>
                                <div class="col-sm-8"><?php echo $id_usuario; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Correo:</strong></div>
                                <div class="col-sm-8"><?php echo $correo; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>NIT de la Empresa:</strong></div>
                                <div class="col-sm-8"><?php echo $nit_empresa; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Estado:</strong></div>
                                <div class="col-sm-8"><?php echo $estado; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Cargo:</strong></div>
                                <div class="col-sm-8"><?php echo $cargo; ?></div>
                            </div>
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
                    <form id="changePasswordForm" action="cambiar_contrasena.php" method="POST">
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

            // Validate the password before submitting the form
            $('#changePasswordForm').on('submit', function(e) {
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{10,}$/;

                if (!passwordPattern.test(password)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La contraseña debe ser alfanumérica y tener al menos 10 caracteres.'
                    });
                } else if (password !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Las contraseñas no coinciden.'
                    });
                }
            });
        });
    </script>
</body>
</html>
