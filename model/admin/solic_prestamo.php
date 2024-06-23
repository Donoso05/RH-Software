<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
$con = $db->conectar();

$id_usuario = $_SESSION["id_usuario"];

// Obtener el nit_empresa del usuario con la sesión iniciada
$stmtNit = $con->prepare("SELECT nit_empresa FROM usuario WHERE id_usuario = :id_usuario");
$stmtNit->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmtNit->execute();
$nit_empresa = $stmtNit->fetch(PDO::FETCH_ASSOC)['nit_empresa'];

// Consultar los créditos del usuario con el mismo nit_empresa
$stmtCreditos = $con->prepare("
    SELECT sp.id_prestamo, sp.monto_solicitado, sp.cant_cuotas, sp.valor_cuotas, sp.mes, sp.anio, e.estado AS nombre_estado, u.id_usuario, u.nombre 
    FROM solic_prestamo sp
    JOIN estado e ON sp.id_estado = e.id_estado
    JOIN usuario u ON sp.id_usuario = u.id_usuario
    WHERE u.nit_empresa = :nit_empresa
");
$stmtCreditos->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
$stmtCreditos->execute();
$creditos = $stmtCreditos->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los usuarios de la misma empresa
$stmtUsuarios = $con->prepare("SELECT id_usuario, nombre FROM usuario WHERE nit_empresa = :nit_empresa");
$stmtUsuarios->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Préstamo</title>
    <link rel="stylesheet" href="css/presta.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <div class="col-12 col-md-3 p-3">
            <div class="">
                <h3 class="text-center text-primary">Solicitud de Préstamo</h3>
                <form id="creditoForm" method="post" action="procesar_credito.php">
                    <div class="form-group mb-3">
                        <label for="id_usuario" class="form-label">Documento:</label>
                        <select id="id_usuario" name="id_usuario" class="form-control" required>
                            <option value="">Seleccione un usuario</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>" data-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                                    <?php echo htmlspecialchars($usuario['id_usuario']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nombre_usuario" class="form-label">Nombre del Usuario:</label>
                        <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="monto" class="form-label">Monto Solicitado:</label>
                        <input type="number" id="monto" name="monto" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="cuotas" class="form-label">Cantidad de Cuotas:</label>
                        <input type="number" id="cuotas" name="cuotas" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="valorCuotas" class="form-label">Valor de cada Cuota:</label>
                        <span id="valorCuotas" class="form-control"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                    <input type="hidden" name="nit_empresa" value="<?php echo htmlspecialchars($nit_empresa); ?>">
                </form>
            </div>
        </div>
        <div class="col-12 col-md-9 p-3">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th scope="col">ID Usuario</th>
                            <th scope="col">Nombre Usuario</th>
                            <th scope="col">Monto Solicitado</th>
                            <th scope="col">Cantidad de Cuotas</th>
                            <th scope="col">Valor de Cuotas</th>
                            <th scope="col">Mes</th>
                            <th scope="col">Año</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($creditos as $credito): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($credito['id_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($credito['nombre']); ?></td>
                                <td><?php echo number_format($credito['monto_solicitado'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($credito['cant_cuotas']); ?></td>
                                <td><?php echo number_format($credito['valor_cuotas'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($credito['mes']); ?></td>
                                <td><?php echo htmlspecialchars($credito['anio']); ?></td>
                                <td><?php echo htmlspecialchars($credito['nombre_estado']); ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-start">
                                            <a href="update_prestamo.php?id=<?php echo $credito['id_prestamo']; ?>" onclick="window.open('./update/update_prestamo.php?id=<?php echo $credito['id_prestamo']; ?>','','width=500,height=500,toolbar=NO'); return false;" class="btn btn-primary">Editar</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/credito.js"></script>
    <script>
        document.getElementById('id_usuario').addEventListener('change', function() {
            var nombreUsuario = this.options[this.selectedIndex].getAttribute('data-nombre');
            document.getElementById('nombre_usuario').value = nombreUsuario;
        });
    </script>
</body>
</html>
