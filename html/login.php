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
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>


  <link rel="stylesheet" type="text/css" href="..//css/style.css">


</head>


<body>
  <header>
    <!-- place navbar here -->
  </header>
  <div class="container login-container">



    <main>


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
      }
      session_start();


      if (isset($_SESSION['nombre_usuario'])) {
        header('Location: login.php');
        exit();
      }


      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn = new mysqli('localhost', 'root', '', 'ilerbank');


        if ($conn->connect_error) {
          die("Error de conexión: " . $conn->connect_error);
        }


        $nombre_usuario = $conn->real_escape_string($_POST['nombre_usuario']);
        $contrasena = $conn->real_escape_string($_POST['contrasena']);


        $query = "SELECT * FROM usuarios WHERE nombre_usuario = '$nombre_usuario' AND contrasena = '$contrasena'";
        $result = $conn->query($query);


        if ($result && $result->num_rows > 0) {
          $_SESSION['nombre_usuario'] = $nombre_usuario;
          header('Location: versaldo.php');
          exit();
        } else {
          $error_message = "Credenciales incorrectas.";
        }


        $conn->close();
      }
      ?>


      <h2 class="text-center">Iniciar Sesión</h2>
      <?php if (isset($error_message)): ?>
        <p style="color: red;">
          <?php echo $error_message; ?>
        </p>
      <?php endif; ?>
      <form method="post" action="">
        <div class="mb-3">
          <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
          <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
        </div>


        <div class="mb-3">
          <label for="contrasena" class="form-label">Contraseña:</label>
          <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>


        <div class="mb-3 text-center">
          <input type="submit" class="btn btn-primary" value="Iniciar Sesión">
        </div>


        <p class="text-center">¿No tienes una cuenta? <a href="crearCuenta.php">Crear cuenta</a></p>
      </form>
  </div>
  </main>
  <footer>
    <!-- place footer here -->
  </footer>

</body>


</html>