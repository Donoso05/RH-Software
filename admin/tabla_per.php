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
$sql = $con->prepare("SELECT * FROM tram_permiso,tipo_permiso,estado WHERE tram_permiso.id_tipo_permiso = tipo_permiso.id_tipo_permiso 
AND tram_permiso.id_estado = estado.id_estado ");
$sql->execute();
$resultado1 = $sql->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../css/ingreso2.css">
	<title>Document</title>


	<!--JQUERY-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- FRAMEWORK BOOTSTRAP para el estilo de la pagina-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

	<!-- Los iconos tipo Solid de Fontawesome-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/solid.css">
	<script src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>

	<!-- Nuestro css-->
	<link rel="stylesheet" type="text/css" href="../css/ingreso2.css" th:href="@{/css/ingreso2.css}">
	<!-- DATA TABLE -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

	<script type="text/javascript">
		$(document).ready(function() {
			//Asegurate que el id que le diste a la tabla sea igual al texto despues del simbolo #
			$('#userList').DataTable();
		});
	</script>
</head>

<body>
	<div class="container">
		<a href="../admin/admin.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Regresar</a>
		<div class="mx-auto col-sm-8 main-section" id="myTab" role="tablist">
			<ul class="nav nav-tabs justify-content-end">
				<li class="nav-item">
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
					<div class="card">
						<div class="card-header">
							<h4>Usuario</h4>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table id="userList" class="table table-bordered table-hover table-striped">
									<thead class="thead-light">
										<tr>
											<th scope="col">Id Permiso</th>
											<th scope="col">Documento</th>
											<th scope="col">Tipo Permiso</th>
											<th scope="col">Fecha Inicio</th>
											<th scope="col">Fecha Fin</th>
											<th scope="col">Estado</th>
											<th scope="col">Incapacidad</th>
											<th></th>
										</tr>
									</thead>

									<tbody>
										<tr>
											<?php
											$query = $con->prepare("SELECT * FROM tram_permiso,tipo_permiso,estado WHERE tram_permiso.id_tipo_permiso = tipo_permiso.id_tipo_permiso AND tram_permiso.id_estado = estado.id_estado");
											$query->execute();
											$resultados = $query->fetchAll(PDO::FETCH_ASSOC);
											?>
											<?php foreach ($resultados as $resul) { ?>
										<tr>
											<td> <?php echo  $resul['id_permiso'];  ?> </td>
											<td> <?php echo $resul['id_usuario']; ?></td>
											<td> <?php echo $resul['tipo_permiso']; ?></td>
											<td> <?php echo $resul['fecha_inicio']; ?> </td>
											<td> <?php echo $resul['fecha_fin']; ?> </td>
											<td> <?php echo $resul['estado']; ?> </td>
											<td> <?php echo $resul['incapacidad']; ?> </td>
											<td>
												<a href="?id=<?php echo $resul['id_permiso'] ?>" class="btn btn-primary" onclick="window.open('admin/update_tram.php?id=<?php echo $resul['id_permiso'] ?>','','width= 700,height=700, toolbar=NO');void(null);">
													<i class="fas fa-edit"></i></a>
												<a href="delete_per.php?id_permiso=<?php echo $resul["id_permiso"]; ?>" class="btn btn-primary" onclick="return confirm('¿Estás seguro de que quieres eliminar este dato?');">
													<i class="fas fa-user-times"></i>
												</a>
											</td>
										</tr>
									<?php } ?>

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</body>

</html>