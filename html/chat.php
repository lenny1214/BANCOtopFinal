<?php
session_start();

// Verifica si se ha enviado el formulario de cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    // Cierra la sesión
    session_destroy();
    header('Location: login.php');
    exit();
}


// Tu código de conexión a la base de datos debe ir aquí
$servername = "localhost";
$username = "root";
$password = "Ign@fervig12";
$dbname = "ilerbank";

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verifica si se ha enviado el formulario para enviar mensajes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensaje'])) {
    $destinatario = $_POST['destinatario'];
    $mensaje = $_POST['mensaje'];

    // Insertar el mensaje en la base de datos
    $stmt_insertar_mensaje = $conexion->prepare("INSERT INTO mensajes (remitente, destinatario, mensaje) VALUES (?, ?, ?)");
    $stmt_insertar_mensaje->bind_param("sss", $_SESSION['nombre_usuario'], $destinatario, $mensaje);
    $stmt_insertar_mensaje->execute();
    $stmt_insertar_mensaje->close();

    // Obtener el ID del mensaje insertado
    $mensaje_id = $conexion->insert_id;

    // Registrar el mensaje recibido en la tabla mensajes_recibidos
    $stmt_insertar_recibido = $conexion->prepare("INSERT INTO mensajes_recibidos (destinatario, remitente, mensaje_id) VALUES (?, ?, ?)");
    $stmt_insertar_recibido->bind_param("ssi", $destinatario, $_SESSION['nombre_usuario'], $mensaje_id);
    $stmt_insertar_recibido->execute();
    $stmt_insertar_recibido->close();
}

// Cargar lista de usuarios
$query_usuarios = "SELECT nombre_usuario FROM usuarios";
$result_usuarios = $conexion->query($query_usuarios);
$destinatario_seleccionado = isset($_POST['destinatario']) ? $_POST['destinatario'] : '';

// Cargar mensajes entre el usuario actual y el destinatario seleccionado
$query_mensajes = "SELECT remitente, destinatario, mensaje FROM mensajes 
                   WHERE (remitente = '{$_SESSION['nombre_usuario']}' AND destinatario = '$destinatario_seleccionado') 
                      OR (remitente = '$destinatario_seleccionado' AND destinatario = '{$_SESSION['nombre_usuario']}')
                   ORDER BY fecha_envio";  // Ordenar por fecha de envío para mostrar los mensajes en orden cronológico
$result_mensajes = $conexion->query($query_mensajes);

// Marcar mensajes como leídos al cargar la página
if (!empty($destinatario_seleccionado)) {
    $stmt_marcar_leidos = $conexion->prepare("UPDATE mensajes_recibidos SET leido = 1 WHERE destinatario = ? AND remitente = ?");
    $stmt_marcar_leidos->bind_param("ss", $_SESSION['nombre_usuario'], $destinatario_seleccionado);
    $stmt_marcar_leidos->execute();
    $stmt_marcar_leidos->close();
}

?>
<!doctype html>
<html lang="en">

<head>
    <title>Chat - IlerBank</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>

<body>
    <header>
       <!-- Navbar -->
       <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="versaldo.php">
                    <img src="../img/logoBanco.png" alt="Logo del Banco" height="40" class="d-inline-block align-text-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="anadiringa.php">Añadir Ingreso/Gasto</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="generariban.php">Generar IBAN</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="prestamo.php">Pedir Préstamo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="chat.php">Chat</a>
                        </li>
                    </ul>
                </div>
                <form class="d-flex" method="post" action="">
                    <input class="btn btn-outline-danger" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
                </form>
            </div>
        </nav>
    </header>
    <main class="container mt-3">
        <form method="post" action="" class="mb-3">
            <div class="input-group">
                <select name="destinatario" class="form-select" aria-label="Seleccione un destinatario" required>
                    <?php
                    while ($row = $result_usuarios->fetch_assoc()) {
                        echo "<option>{$row['nombre_usuario']}</option>";
                    }
                    ?>
                </select>
                <input type="text" name="mensaje" class="form-control" placeholder="Escribe tu mensaje" required>
                <button type="submit" name="enviar_mensaje" class="btn btn-primary">Enviar</button>
            </div>
        </form>
        <div class="row">
            <div class="col-md-4">
                <h5>Usuarios</h5>
                <ul class="list-group">
                    <?php
                    $result_usuarios->data_seek(0);
                    while ($row = $result_usuarios->fetch_assoc()) {
                        echo "<li class='list-group-item'>{$row['nombre_usuario']}</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-8">
                <h5>Conversación</h5>
                <div class="border p-3" style="height: 300px; overflow-y: auto;">
                    <?php
                    while ($row = $result_mensajes->fetch_assoc()) {
                        echo "<p><strong>{$row['remitente']}</strong>: {$row['mensaje']}</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    <footer class="mt-4 bg-light text-center p-3">
    <div class="container">
        <p class="mb-0">
            &copy; <?php echo date('Y'); ?> Banco Ilerbank. Todos los derechos reservados.
        </p>
        <p class="mb-0">
            <a href="terminos_y_condiciones.php" class="text-decoration-none">Términos y Condiciones</a>
        </p>
    </div>
</footer>
</body>

</html>
