<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Préstamo</title>
    <link rel="stylesheet" href="css/presta.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">
        <h2>Solicitud de Préstamo</h2>
        <form id="creditoForm" method="post" action="procesar_credito.php">
            <div class="form-group">
                <label for="monto">Monto Solicitado:</label>
                <input type="number" id="monto" name="monto" required>
            </div>
            <div class="form-group">
                <label for="cuotas">Cantidad de Cuotas:</label>
                <input type="number" id="cuotas" name="cuotas" required>
            </div>
            <div class="form-group">
                <label for="valorCuotas">Valor de cada Cuota:</label>
                <span id="valorCuotas"></span>
            </div>
            <button type="submit">Enviar Solicitud</button>
        </form>
    </div>
    <script src="js/credito.js"></script>
</body>

</html>