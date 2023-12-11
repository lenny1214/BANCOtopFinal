<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    header('Location: login.php');
    exit();
}

// Crear la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilerbank";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el rol del usuario (asumir que hay una columna 'rol' en la tabla 'usuarios')
$nombreUsuario = $_SESSION['nombre_usuario'];
$sqlRol = "SELECT rol FROM usuarios WHERE nombre_usuario = '$nombreUsuario'";
$resultRol = $conn->query($sqlRol);

if ($resultRol->num_rows > 0) {
    $row = $resultRol->fetch_assoc();
    $rolUsuario = $row['rol'];

    if ($rolUsuario === 'admin') {
        // El usuario es un administrador, mostrar datos de todos los usuarios
        $sqlMostrarUsuarios = "SELECT * FROM usuarios";
        $resultMostrarUsuarios = $conn->query($sqlMostrarUsuarios);

        if ($resultMostrarUsuarios->num_rows > 0) {
            echo "<h2>Datos de todos los usuarios:</h2>";
            echo "<table border='1'>";
            echo "<tr><th>Nombre</th><th>Apellido</th><th>Email</th><th>...</th></tr>";

            while ($row = $resultMostrarUsuarios->fetch_assoc()) {
                echo "<tr><td>" . $row['nombre_usuario'] . "</td><td>" . $row['apellido'] . "</td><td>" . $row['email'] . "</td><td>...</td></tr>";
            }

            echo "</table>";
        } else {
            echo "No hay usuarios registrados.";
        }
    } else {
        // El usuario no es un administrador, redirigir a la página principal
        header('Location: indexadmin.php');
        exit();
    }
} else {
    echo "Error al obtener el rol del usuario.";
}

$conn->close();
?>
