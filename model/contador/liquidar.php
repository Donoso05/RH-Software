<?php
require '../../conexion/conexion.php'; // Ajusta la ruta según la ubicación de tu archivo Database.php

// Crear una instancia de la clase Database y establecer la conexión
$database = new Database();
$con = $database->conectar();

// Obtener el id_usuario de la URL
$id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : 0;

// Consulta para obtener la información del usuario y del cargo
$sql = "SELECT usuario.id_usuario, usuario.nombre, tipo_cargo.cargo, tipo_cargo.salario_base 
        FROM usuario
        INNER JOIN tipo_cargo ON usuario.id_tipo_cargo = tipo_cargo.id_tipo_cargo 
        WHERE usuario.id_usuario = :id_usuario";

$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquidación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .card-body{
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            text-align: center;
        }
    </style>

</head>
<body>
<?php include("nav.php") ?>
    <div class="container mt-5">
        <?php if (count($result) > 0): ?>
            <?php foreach ($result as $row): ?>
                <div class="card">
                    <div class="card-header">
                        Cédula <?php echo $row['id_usuario']; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                        <p class="card-text"><strong><?php echo $row['cargo']; ?></strong></p>
                        <p class="card-text"><strong>$</strong> <?php echo number_format($row['salario_base'], 0, '.', ','); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                No se encontraron resultados para el ID Usuario: <?php echo htmlspecialchars($id_usuario); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión
$conn = null;
?>