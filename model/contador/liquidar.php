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

// Calcular el salario diario
$salario_diario = 0;
if (count($result) > 0) {
    $salario_diario = $result[0]['salario_base'] / 30;
}
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
    <link rel="stylesheet" href="css/liquidar.css">
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
                <script>
    const salarioDiario = <?php echo $salario_diario; ?>;
    
    function calcularNomina() {
        const diasTrabajados = document.getElementById('dias_trabajados').value;
        
        if (diasTrabajados > 30) {
            document.getElementById('salario_total').innerText = "Número inválido!!!";
        } else {
            const salarioTotal = diasTrabajados * salarioDiario;
            document.getElementById('salario_total').innerText = salarioTotal.toLocaleString('es-ES', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    }
</script>

<div class="nomina-section">
    <h4>Nómina</h4>
    <form>
        <div class="mb-3">
            <label for="dias_trabajados" class="form-label"><strong>Días Trabajados</strong></label>
            <input type="number" id="dias_trabajados" name="dias_trabajados" class="form-control" min="1" max="30" required oninput="calcularNomina()">
        </div>
    </form>
    <div>
        <p><strong>Salario Total: </strong><span id="salario_total"></span></p>
    </div>
</div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                No se encontraron resultados para el ID Usuario: <?php echo htmlspecialchars($id_usuario); ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
// Cerrar la conexión
$conn = null;
?>
