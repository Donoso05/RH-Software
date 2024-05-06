<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sidebar.css">
    <title>Sidebar</title>
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        <a href="tabla_usuarios.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name"> REGISTROS DE USUARIOS</span> </a>

        <a href="tabla_tipo_usu.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">TIPOS DE USUARIOS</span> </a>

        <a href="tabla_permisos.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">PERMISOS</span> </a>

        <a href="tabla_prestamos.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">SOLIT. DE PRESTAMO</span> </a>

        <a href="tabla_cargo.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">TIPOS DE CARGO</span> </a>

        <a href="tabla_per.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">TRAM. PERMISO</span> </a>

        <a href="tabla_aux.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">AUX. TRANSPORTE</span> </a>

        <a href="tabla_arl.php" class="nav_link">
            <i class='bx bx-user nav_icon'></i>
            <span class="nav_name">ARL</span> </a>


    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div> <a href="#" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i>
                    <span class="nav_logo-name">A.K.A </span> </a>
                <div class="nav_list"> <a href="../admin/admin.php" class="nav_link active">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Inicio</span></a>

                    <a class="nav_link">
                        <i class='bx bx-user nav_icon'></i>
                        <span class="nav_name">-Formularios-</span> </a>


                    <a href="../admin/usuario.php" class="nav_link">
                        <i class='bx bx-user nav_icon'></i>
                        <span class="nav_name">Agregar Usuarios</span> </a>


                    <a href="../admin/tipos_usuario.php" class="nav_link">
                        <i class='bx bx-message-square-detail nav_icon'></i>
                        <span class="nav_name">Agregar Tipos Usuario</span> </a>


                    <a href="../admin/tipo_permiso.php" class="nav_link">
                        <i class='bx bx-bookmark nav_icon'></i>
                        <span class="nav_name">Permisos</span> </a>


                    <a href="../admin/solic_prestamo.php" class="nav_link">
                        <i class='bx bx-folder nav_icon'></i>
                        <span class="nav_name">Solicitud de Prestamos</span> </a>

                    <a href="../admin/tipo_cargo.php" class="nav_link">
                        <i class='bx bx-folder nav_icon'></i>
                        <span class="nav_name">Tipo Cargo</span> </a>

                    <a href="../admin/tram_permiso.php" class="nav_link">
                        <i class='bx bx-folder nav_icon'></i>
                        <span class="nav_name">Tramite Permiso</span> </a>


                </div>
            </div>
            <a href="../controller/cerrarcesion.php" class="nav_link">
                <i class='bx bx-log-out nav_icon'>Cerrar Sesion
                </i>
            </a>
        </nav>
    </div>

</html>