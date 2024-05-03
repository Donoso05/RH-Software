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
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Interfaz Administrador</title>
</head>
<body>

<?php include("sidebar.php") ?>

<div id="groupedBarChartContainer">
    <canvas id="groupedBarChart"></canvas>
</div>

<style>
    #groupedBarChartContainer {
        width: 80%;
        margin: 230px;
        margin-top: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctxGroupedBar = document.getElementById('groupedBarChart').getContext('2d');
        var groupedBarChart = new Chart(ctxGroupedBar, {
            type: 'bar',
            data: {
                labels: ['Noviembre', 'Diciembre', 'Enero', 'Febrero', 'Marzo'],
                datasets: [{
                    label: 'Préstamos Aprobados',
                    data: [8, 15, 2, 4, 1],
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderWidth: 1
                }, {
                    label: 'Préstamos Rechazados',
                    data: [2, 4, 1, 1, 1],
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

</body>
</html>

