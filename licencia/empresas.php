<?php
session_start();
require_once("../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();
?>
<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {

	$nit = $_POST['nit'];
	$nombre = $_POST['nombre'];
	$correo = $_POST['correo'];

	$sql = $con -> prepare ("SELECT * FROM empresas");
	$sql -> execute();
	$fila = $sql -> fetchAll(PDO::FETCH_ASSOC);

	if($nit=="" || $nombre=="" || $correo=="")
	{
		echo '<script>alert ("EXISTEN DATOS VACIOS"); </script>';
	}
	else
	{
	  $insertSQL = $con->prepare ("INSERT INTO empresas(nit_empresa, nombre, correo) 
	  VALUES ('$nit','$nombre', '$correo')");
	  $insertSQL->execute();
	  echo '<script>alert ("Empresa creada con exito"); </script>';
	  echo '<script>window.location="empresas.php"</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Empresas</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />

	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">

	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">

	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">

	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">

	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">

	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">

	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">

	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">

	<meta name="robots" content="noindex, follow">
	<script nonce="dc8a67d4-237a-40d4-b065-2401d3e468ef">
		try {
			(function(w, d) {
				! function(j, k, l, m) {
					j[l] = j[l] || {};
					j[l].executed = [];
					j.zaraz = {
						deferred: [],
						listeners: []
					};
					j.zaraz.q = [];
					j.zaraz._f = function(n) {
						return async function() {
							var o = Array.prototype.slice.call(arguments);
							j.zaraz.q.push({
								m: n,
								a: o
							})
						}
					};
					for (const p of ["track", "set", "debug"]) j.zaraz[p] = j.zaraz._f(p);
					j.zaraz.init = () => {
						var q = k.getElementsByTagName(m)[0],
							r = k.createElement(m),
							s = k.getElementsByTagName("title")[0];
						s && (j[l].t = k.getElementsByTagName("title")[0].text);
						j[l].x = Math.random();
						j[l].w = j.screen.width;
						j[l].h = j.screen.height;
						j[l].j = j.innerHeight;
						j[l].e = j.innerWidth;
						j[l].l = j.location.href;
						j[l].r = k.referrer;
						j[l].k = j.screen.colorDepth;
						j[l].n = k.characterSet;
						j[l].o = (new Date).getTimezoneOffset();
						if (j.dataLayer)
							for (const w of Object.entries(Object.entries(dataLayer).reduce(((x, y) => ({
									...x[1],
									...y[1]
								})), {}))) zaraz.set(w[0], w[1], {
								scope: "page"
							});
						j[l].q = [];
						for (; j.zaraz.q.length;) {
							const z = j.zaraz.q.shift();
							j[l].q.push(z)
						}
						r.defer = !0;
						for (const A of [localStorage, sessionStorage]) Object.keys(A || {}).filter((C => C.startsWith("_zaraz_"))).forEach((B => {
							try {
								j[l]["z_" + B.slice(7)] = JSON.parse(A.getItem(B))
							} catch {
								j[l]["z_" + B.slice(7)] = A.getItem(B)
							}
						}));
						r.referrerPolicy = "origin";
						r.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(j[l])));
						q.parentNode.insertBefore(r, q)
					};
					["complete", "interactive"].includes(k.readyState) ? zaraz.init() : j.addEventListener("DOMContentLoaded", zaraz.init)
				}(w, d, "zarazData", "script");
			})(window, document)
		} catch (e) {
			throw fetch("/cdn-cgi/zaraz/t"), e;
		};
	</script>
</head>

<body>
	<div class="container-login100" style="background-image: url('img/rh1.jpg');">
		<div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
			<form class="login100-form validate-form" method="post">
				<span class="login100-form-title p-b-37">
					Crear Empresas
				</span>
				<div class="wrap-input100 validate-input m-b-20" data-validate="nit">
					<input class="input100" type="text" name="nit" id="nit" placeholder="NIT" id="nit">
					<span class="focus-input100"></span>
				</div>
				<div class="wrap-input100 validate-input m-b-25" data-validate="nombre">
					<input class="input100" type="text" name="nombre" id="nombre" placeholder="Nombre Empresa" id="nombre">
					<span class="focus-input100"></span>
				</div>
				<div class="wrap-input100 validate-input m-b-25" data-validate="correo">
					<input class="input100" type="text" name="correo" id="correo" placeholder="Correo Electronico" id="correo">
					<span class="focus-input100"></span>
				</div>
				<br>

				<div class="container-login100-form-btn">
					<input type="submit" name="validar" value="Registrar" class="login100-form-btn">
					<input type="hidden" name="MM_insert" value="formreg">
				</div>
				<div class="text-center">
				</div>
			</form>
		</div>
	</div>
	<div id="dropDownSelect1"></div>

	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>

	<script src="vendor/animsition/js/animsition.min.js"></script>

	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

	<script src="vendor/select2/select2.min.js"></script>

	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>

	<script src="vendor/countdowntime/countdowntime.js"></script>

	<script src="js/main.js"></script>

	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'UA-23581568-13');
	</script>
	<script defer src="https://static.cloudflareinsights.com/beacon.min.js/v84a3a4012de94ce1a686ba8c167c359c1696973893317" integrity="sha512-euoFGowhlaLqXsPWQ48qSkBSCFs3DPRyiwVu3FjR96cMPx+Fr+gpWRhIafcHwqwCqWS42RZhIudOvEI+Ckf6MA==" data-cf-beacon='{"rayId":"859371590beb0975","version":"2024.2.1","token":"cd0b4b3a733644fc843ef0b185f98241"}' crossorigin="anonymous"></script>
</body>

</html>