<?php
session_start();

// Verifica si el usuario no ha iniciado sesión o no es administrador
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['es_administrador']) || $_SESSION['es_administrador'] != 1) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'ilerbank');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener todos los usuarios
$usuariosQuery = "SELECT * FROM usuarios";
$usuariosResult = $conn->query($usuariosQuery);

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<!-- Resto del código HTML y Bootstrap -->

<body>
    <header>
        <!-- Navbar -->
        <!-- ... -->
    </header>

    <main>
        <h2>Bienvenido, <?php echo $_SESSION['nombre_usuario']; ?> (Admin)</h2>

        <div style="padding: 16px;">
            <h2>Lista de Usuarios</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre de Usuario</th>
                        <th>Apellido</th>
                        <th>DNI</th>
                        <!-- Agregar más columnas según sea necesario -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($usuario = $usuariosResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$usuario['nombre_usuario']}</td>";
                        echo "<td>{$usuario['apellido']}</td>";
                        echo "<td>{$usuario['dni']}</td>";
                        // Agregar más columnas según sea necesario
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Resto del código HTML y Bootstrap -->

</body>

</html>
