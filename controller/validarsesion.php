<?php
session_start();
if (!isset($_SESSION['ID'])) {
    echo '
 <script>
        alert("Por favor inicie sesión e intente nuevamente");
        window.location = "../login.html";
    </script>
    ';
    session_destroy();
    die();
}
?>