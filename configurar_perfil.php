<?php
session_start(); // Iniciar sesión

include("con_db.php"); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header("Location: index.php"); // Redirigir al login si no hay sesión activa
    exit(); // Finaliza el script si no hay sesión activa
}

$user = $_SESSION['user']; // Obtener el nombre de usuario de la sesión

// Obtener el id del usuario
$query  = "SELECT id FROM usuario WHERE user = ?";
$stmt = mysqli_prepare($conex, $query);
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_user); // Recuperar el id del usuario
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $biografia = trim($_POST['biografia']);
    $imagen_perfil = $_FILES['imagen']['name'];

    // Verificar si se subió una imagen
    if (empty($imagen_perfil)) {
        // Usar imagen por defecto si no se subió ninguna
        $imagen_perfil = "default-profile.jpeg";
    } else {
        // Subir la imagen al servidor
        $target_dir = "imagenes_perfil/"; // Directorio donde se guardará la imagen
        $target_file = $target_dir . basename($imagen_perfil);
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            // Éxito al mover la imagen
        } else {
            echo "Error al subir la imagen.";
            exit();
        }
    }

    // Insertar los datos en la tabla perfil
    $query = "INSERT INTO perfil (id_user, biografia, imagen) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "iss", $id_user, $biografia, $imagen_perfil);
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        header("Location: inicio.php"); // Redirigir a inicio después de guardar los datos
        exit();
    } else {
        echo "Error al guardar los datos: " . mysqli_error($conex);
    }

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Perfil</title>
    <!-- Enlace a Bootstrap 4 y Font Awesome -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, rgb(50, 50, 50), rgb(41, 41, 41));
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .profile-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
            transition: transform 0.3s ease;
        }

        .profile-container:hover {
            transform: translateY(-5px);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            outline: none;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #b8860b;
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background-color:rgb(128, 93, 5);
        }

        .input-group-text {
            background: #b8860b;
            border: none;
            color: #fff;
        }

        a.text-light:hover {
            text-decoration: underline;
            color: #d1e0ff !important;
        }

        .icon-color {
            color: #fff !important;  /* Iconos en blanco */
        }
    </style>
</head>
<body class="bg-dark text-white">

    <header class="text-center my-4">
        <h1>Configura tu Perfil</h1>
    </header>

    <main>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <form action="configurar_perfil.php" method="POST" enctype="multipart/form-data" class="bg-gradient-primary p-4 rounded shadow-lg w-100" style="max-width: 500px;">
                <h2 class="text-center mb-4">Completa tu biografía y agrega una imagen de perfil</h2>

                <div class="form-group">
                    <label for="biografia">Biografía:</label>
                    <textarea name="biografia" rows="5" cols="50" placeholder="Escribe tu biografía aquí..." required class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen de perfil:</label>
                    <input type="file" name="imagen" accept="image/*" class="form-control-file">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Guardar</button>

                <div class="text-center mt-3">
                    <a href="inicio.php" class="text-light">&#171; Regresar al inicio</a>
                </div>
            </form>
        </div>
    </main>

    <!-- Enlace a los scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
