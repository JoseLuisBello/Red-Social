<?php
include("con_db.php");
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

// Obtener los datos del usuario (incluyendo la imagen de perfil)
$consulta = "SELECT * FROM usuario WHERE user = '$user'";
$resultado = mysqli_query($conex, $consulta);
$datos_usuario = mysqli_fetch_assoc($resultado);

// Obtener el ID del usuario
$id_usuario = $datos_usuario['id'];

// Verificar si se ha enviado una nueva publicación
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el contenido de la publicación
    $contenido = mysqli_real_escape_string($conex, $_POST['contenido']);
    
    // Manejar la imagen cargada (si existe)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        // Definir el directorio donde se guardarán las imágenes
        $directorio_imagenes = "uploads/";
        // Obtener la información de la imagen
        $imagen_temp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_ruta = $directorio_imagenes . basename($imagen_nombre);
        
        // Mover la imagen al directorio de destino
        if (move_uploaded_file($imagen_temp, $imagen_ruta)) {
            // Guardar la publicación con la imagen
            $consulta_publicacion = "INSERT INTO publicacion (contenido, imagen, id_perfil) VALUES ('$contenido', '$imagen_ruta', '$id_usuario')";
        }
    } else {
        // Si no se ha subido ninguna imagen, guardar solo el contenido
        $consulta_publicacion = "INSERT INTO publicacion (contenido, id_perfil) VALUES ('$contenido', '$id_usuario')";
    }
    
    // Ejecutar la consulta de inserción
    if (mysqli_query($conex, $consulta_publicacion)) {
        // Redirigir para evitar el reenvío de formulario
        header("Location: perfil.php");
        exit();
    } else {
        echo "<p class='text-danger'>Error al guardar la publicación: " . mysqli_error($conex) . "</p>";
    }
}

// Obtener las publicaciones del usuario filtradas por el ID
$consulta_publicaciones = "SELECT publicacion.contenido, publicacion.imagen, usuario.user AS usuario
                            FROM publicacion
                            JOIN usuario ON publicacion.id_perfil = usuario.id
                            WHERE publicacion.id_perfil = '$id_usuario'
                            ORDER BY publicacion.id DESC";
$resultado_publicaciones = mysqli_query($conex, $consulta_publicaciones);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($user); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: rgb(60, 60, 60);
            color: rgb(63, 63, 63);
        }

        .navbar,
        .list-group-item,
        .card {
            background-color: rgb(127, 127, 127);
            border: none;
        }

        .list-group-item a {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        a {
            color: rgb(0, 0, 0);
        }

        a:hover {
            color: #ffffff;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .profile-header {
            background-color: #343a40;
            color: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        
        .btn-primary {
            background-color: #b8860b;
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background-color:rgb(148, 108, 8);
        }

        .profile-header img {
            max-width: 120px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .profile-header h2 {
            margin-bottom: 10px;
        }

        .post {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .post h5 {
            margin-bottom: 10px;
            font-size: 1.25rem;
        }

        .post p {
            margin-top: 10px;
            font-size: 1.1rem;
        }

        .post img {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 15px;
        }

        .container {
            margin-top: 30px;
        }

        .modal-body {
            background-color: rgb(240, 240, 240);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#"><i class="fa fa-user"></i> Perfil</a>
        <div class="ms-auto">
            <a href="inicio.php" class="btn btn-outline-light me-2"><i class="fa fa-home"></i> Inicio</a>
            <a href="logout.php" class="btn btn-outline-danger"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Perfil del Usuario -->
            <div class="col-md-3">
                <div class="profile-header">
                    <?php 
                    // Verificar si el usuario tiene imagen de perfil
                    $imagen_perfil = !empty($datos_usuario['imagen']) ? $datos_usuario['imagen'] : "default-profile.jpeg";
                    ?>
                    <!-- <img src="<?php echo htmlspecialchars($imagen_perfil); ?>" alt="Foto de perfil"> -->
                    <img src="./imagenes_perfil/default-profile.jpeg" alt="Foto de perfil">
                    <h2><?php echo htmlspecialchars($datos_usuario['user']); ?></h2>
                </div>
            </div>

            <!-- Publicaciones del Usuario -->
            <div class="col-md-9">
                <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#nuevaPublicacionModal">
                    <i class="fa fa-plus"></i> Nueva Publicación
                </button>

                <div class="modal fade" id="nuevaPublicacionModal" tabindex="-1" aria-labelledby="nuevaPublicacionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="nuevaPublicacionModalLabel">Crear Nueva Publicación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="contenido" class="form-label">Contenido</label>
                                        <textarea class="form-control" id="contenido" name="contenido" rows="4" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Cargar Imagen</label>
                                        <input class="form-control" type="file" id="imagen" name="imagen">
                                    </div>
                                    <button type="submit" class="btn btn-success">Publicar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                if ($resultado_publicaciones && mysqli_num_rows($resultado_publicaciones) > 0) {
                    while ($publicacion = mysqli_fetch_assoc($resultado_publicaciones)) { 
                        $usuario = htmlspecialchars($publicacion['usuario']);
                        $contenido = nl2br(htmlspecialchars($publicacion['contenido']));
                        $imagen_url = htmlspecialchars($publicacion['imagen']);
                ?>
                    <div class="post">
                        <h5><strong><?php echo $usuario; ?></strong></h5>
                        <p><?php echo $contenido; ?></p>
                        <?php if (!empty($imagen_url)) { ?>
                            <img src="<?php echo $imagen_url; ?>" alt="Imagen de la publicación">
                        <?php } ?>
                    </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center'>No has realizado ninguna publicación aún.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
