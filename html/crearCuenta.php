<?php
// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    $host = 'localhost';
    $dbname = 'ilerbank';
    $username = 'root';
    $password = 'Ign@fervig12';
    $port = 3306;

    $conn = new mysqli($host, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Escapar las entradas del formulario para evitar inyecciones SQL
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre_usuario'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $codigo_postal = $_POST['codigo_postal'];
    $ciudad = $_POST['ciudad'];
    $provincia = $_POST['provincia'];
    $contrasena = $_POST['contrasena']; // No se utiliza hash aquí

    // Obtener la información de la foto
    $foto_perfil = $_FILES['foto_perfil']['name'];
    $foto_temporal = $_FILES['foto_perfil']['tmp_name'];

    // Obtener la ruta absoluta del directorio actual
$directorio_actual = __DIR__;

// Ruta completa donde se guardarán las fotos (carpeta img dentro del proyecto)
$ruta_foto = $directorio_actual . '/img/' . basename($foto_perfil);

// Mover la foto a la ubicación deseada
move_uploaded_file($foto_temporal, $ruta_foto);

    // Consulta preparada para insertar el nuevo usuario con la foto
    $insertQuery = "INSERT INTO usuarios (dni, nombre_usuario, apellido, email, fecha_nacimiento, direccion, codigo_postal, ciudad, provincia, contrasena, foto_perfil)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('sssssssssss', $dni, $nombre, $apellido, $email, $fecha_nacimiento, $direccion, $codigo_postal, $ciudad, $provincia, $contrasena, $ruta_foto);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Registro exitoso, redirigir a la página de inicio de sesión
        header('Location: login.php');
        exit();
    } else {
        // Error al registrar
        $error_message = "Error al registrar. Por favor, inténtelo de nuevo.";
    }

    // Cierra la declaración preparada
    $stmt->close();

    // Cierra la conexión
    $conn->close();
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="..//css/style.css">
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="contenedor">
            <div class="container login-container">
                <h2 class="text-center">Crear cuenta</h2>
                <?php if (isset($error_message)): ?>
                <p style="color: red;">
                    <?php echo $error_message; ?>
                </p>
                <?php endif; ?>
                <form method="post" action="" enctype="multipart/form-data">
                    <!-- Agregado enctype="multipart/form-data" para manejar archivos -->
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI:</label>
                        <input type="text" class="form-control" id="dni" name="dni" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label">Nombre Usuario:</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento:</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>
                    <div class="mb-3">
                        <label for="codigo_postal" class="form-label">Código Postal:</label>
                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
                    </div>
                    <div class="mb-3">
                        <label for="ciudad" class="form-label">Ciudad:</label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                    </div>
                    <div class="mb-3">
                        <label for="provincia" class="form-label">Provincia:</label>
                        <input type="text" class="form-control" id="provincia" name="provincia" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="mb-3">
                        <label for="foto_perfil" class="form-label">Foto de perfil:</label>
                        <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/*">
                    </div>
                    <input type="submit" class="btn btn-primary" value="Registrar">
                    <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
</body>

</html>
