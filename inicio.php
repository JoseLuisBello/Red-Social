<?php
include("con_db.php");
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color:rgb(60, 60, 60);
            color: #e0e0e0;
        }

        .navbar, .list-group-item, .card {
            background-color:rgb(127, 127, 127);
            border: none;
        }

        .list-group-item a {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        a {
            color:rgb(0, 0, 0);
        }

        a:hover {
            color: #ffffff;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#"><i class="fa fa-home"></i> Inicio</a>
        <div class="ms-auto">
            <a href="perfil.php" class="btn btn-outline-light me-2"><i class="fa fa-user"></i> Perfil</a>
            <a href="logout.php" class="btn btn-outline-danger"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <aside class="col-md-3 mb-4">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="personas_no_amigos.php"><i class="fa fa-user-friends"></i> Personas que quizás conozcas</a>
                    </li>
                    <li class="list-group-item">
                        <a href="amigo.php"><i class="fa fa-users"></i> Amigos</a>
                    </li>
                    <li class="list-group-item">
                        <a href="chat.php"><i class="fa fa-comments"></i> Mensajes</a>
                    </li>
                    <li class="list-group-item">
                        <a href="#"><i class="fa fa-bell"></i> Notificaciones</a>
                    </li>
                </ul>
            </aside>

            <section class="col-md-9">
                <?php
                $consulta = "SELECT publicacion.contenido, publicacion.imagen, usuario.user AS usuario
                             FROM publicacion
                             JOIN usuario ON publicacion.id_perfil = usuario.id
                             ORDER BY publicacion.id DESC";

                $resultado = mysqli_query($conex, $consulta);

                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        $usuario = htmlspecialchars($fila['usuario']);
                        $contenido = htmlspecialchars($fila['contenido']);
                        $imagen_url = htmlspecialchars($fila['imagen']);
                ?>
                        <div class="card mb-4 p-3">
                            <h5 class="card-title"><i class="fa fa-user-circle"></i> <?php echo $usuario; ?></h5>
                            <p class="card-text"> <?php echo $contenido; ?> </p>
                            <?php if (!empty($imagen_url)) { ?>
                                <img src="<?php echo $imagen_url; ?>" alt="Imagen de <?php echo $usuario; ?>">
                            <?php } ?>
                        </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center'>No hay publicaciones aún. ¡Sé el primero en publicar!</p>";
                }
                ?>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
