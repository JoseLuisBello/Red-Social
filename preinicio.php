<?php
session_start(); // Iniciar la sesión

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
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header>
        <h1>Configura tu Perfil</h1>
    </header>

    <main>
        <div class="profile-container">
            <h2>Completa tu biografía y agrega una imagen de perfil</h2>

            <form action="configurar_perfil.php" method="POST" enctype="multipart/form-data">
                <label for="biografia">Biografía:</label>
                <textarea name="biografia" rows="5" cols="50" placeholder="Escribe tu biografía aquí..." required></textarea>
                <br>

                <label for="imagen">Imagen de perfil:</label>
                <input type="file" name="imagen" accept="image/*">
                <br>

                <button type="submit">Guardar</button>
            </form>
        </div>
    </main>
</body>
</html>
