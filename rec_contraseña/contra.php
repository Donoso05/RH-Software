<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rh";

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar el correo electrónico enviado desde el formulario
    $id_usuario = $_POST["id_user"];
    $email = $_POST["email"];

    $query = "SELECT id_usuario, contrasena FROM usuario WHERE id_usuario = ? AND correo = ?";
    
    // Utilizar consultas preparadas para evitar inyecciones SQL
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "ss", $id_usuario, $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        // Verificar si se encontraron resultados
        if (mysqli_num_rows($result) > 0) {
            // El correo electrónico existe en la base de datos
            $fila = mysqli_fetch_assoc($result);
            $id = $fila['id_usuario'];
            $contrasena = $fila['contrasena'];

            // Enviar el correo electrónico con la contraseña para restablecer
            $subject = "Recuperación de Contraseña";
            $message = "Hola, Tu contraseña actual es: $contrasena\n\n";
            $headers = "From: kenner.lc90@gmail.com" . "\r\n" .
                       "Reply-To: $email" . "\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            // Envía el correo electrónico
            if (mail($email, $subject, $message, $headers)) {
                echo '<script>alert("Revisa tu correo y sigue con la recuperación.");</script>';
                echo '<script>window.location.href = "res_contra.php";</script>';
            } else {
                echo "Hubo un problema al enviar el correo electrónico. Por favor, inténtalo de nuevo más tarde.";
            }
        } else {
            // El correo electrónico no existe en la base de datos
            echo "El correo electrónico no existe en la base de datos.";
        }
    } else {
        echo "Error en la consulta: " . mysqli_error($conexion);
    }
}
?>


