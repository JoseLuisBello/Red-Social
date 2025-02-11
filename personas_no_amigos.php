<?php
include("con_db.php");
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

// Función para agregar amigo
if (isset($_POST['agregar_amigo'])) {
    $amigo = mysqli_real_escape_string($conex, $_POST['amigo']);
    $id_user1 = mysqli_fetch_assoc(mysqli_query($conex, "SELECT id FROM usuario WHERE user = '$user'"))['id'];
    $id_user2 = mysqli_fetch_assoc(mysqli_query($conex, "SELECT id FROM usuario WHERE user = '$amigo'"))['id'];

    // Verificar que no sean ya amigos
    $verificar = mysqli_query($conex, "SELECT * FROM amigos WHERE (id_user1 = '$id_user1' AND id_user2 = '$id_user2') OR (id_user1 = '$id_user2' AND id_user2 = '$id_user1')");

    if (mysqli_num_rows($verificar) == 0) {
        mysqli_query($conex, "INSERT INTO amigos (id_user1, id_user2) VALUES ('$id_user1', '$id_user2')");
        mysqli_query($conex, "INSERT INTO amigos (id_user1, id_user2) VALUES ('$id_user2', '$id_user1')"); // Relación bidireccional
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personas que quizás conozcas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: rgb(60, 60, 60);
            color: #e0e0e0;
        }

        .navbar, .list-group-item, .card {
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

        .container {
            margin-top: 30px;
        }

        .sugerencia {
            background-color: rgb(50, 50, 50);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .sugerencia h3 {
            font-size: 1.5em;
        }

        .agregar-amigo-btn {
            background-color: #b8860b;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .agregar-amigo-btn:hover {
            background-color:rgb(130, 95, 6);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <div class="ms-auto">
            <a href="inicio.php" class="btn btn-outline-light me-2"><i class="fa fa-home"></i> Inicio</a>
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

            <div class="col-md-9">
                <h2 class="mb-4">Personas que quizás conozcas</h2>

                <?php
                $id_actual = mysqli_fetch_assoc(mysqli_query($conex, "SELECT id FROM usuario WHERE user = '$user'"))['id'];

                if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
                    $busqueda = mysqli_real_escape_string($conex, $_GET['buscar']);
                    $sugerencias = mysqli_query($conex, "SELECT id, user FROM usuario WHERE user LIKE '%$busqueda%' AND id != '$id_actual' AND id NOT IN (SELECT id_user2 FROM amigos WHERE id_user1 = '$id_actual')");
                } else {
                    $sugerencias = mysqli_query($conex, "SELECT id, user FROM usuario WHERE id != '$id_actual' AND id NOT IN (SELECT id_user2 FROM amigos WHERE id_user1 = '$id_actual') LIMIT 5");
                }

                if ($sugerencias && mysqli_num_rows($sugerencias) > 0) {
                    while ($sugerido = mysqli_fetch_assoc($sugerencias)) {
                ?>
                        <div class="sugerencia">
                            <h3><i class="fa fa-user"></i> <?php echo htmlspecialchars($sugerido['user']); ?></h3>
                            <form method="POST">
                                <input type="hidden" name="amigo" value="<?php echo htmlspecialchars($sugerido['user']); ?>">
                                <button type="submit" name="agregar_amigo" class="agregar-amigo-btn">Agregar Amigo</button>
                            </form>
                        </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center'>No hay sugerencias en este momento.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
