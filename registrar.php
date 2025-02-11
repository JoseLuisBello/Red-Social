<?php
session_start(); // Iniciar sesión al registrar un nuevo usuario
include("con_db.php");

if (isset($_POST['register'])) {
    if (strlen($_POST['name']) >= 1 && strlen($_POST['user']) >= 1 && strlen($_POST['email']) >= 1 && strlen($_POST['password']) >= 1) {
        $name = mysqli_real_escape_string($conex, trim($_POST['name']));
        $user = mysqli_real_escape_string($conex, trim($_POST['user']));
        $email = mysqli_real_escape_string($conex, trim($_POST['email']));
        $password = mysqli_real_escape_string($conex, trim($_POST['password']));
        $genero = mysqli_real_escape_string($conex, trim($_POST['genero']));

        $consulta = "INSERT INTO usuario(nombre, user, correo, contraseña, genero) 
                     VALUES ('$name','$user','$email','$password','$genero')";
        
        $resultado = mysqli_query($conex, $consulta);

        if ($resultado) {
            $_SESSION['user'] = $user;
            header("Location: configurar_perfil.php");
            exit();
        } else {
            echo "<h3 class='text-danger text-center'>¡Ups, ha ocurrido un error!</h3>";
        }
    } else {
        echo "<h3 class='text-warning text-center'>¡Por favor, completa todos los campos!</h3>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <!-- Enlace a Bootstrap 4 y Font Awesome -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(38, 38, 38),rgb(0, 0, 0));
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        .input-group-text {
            background: #b8860b;
            border: none;
            color: #fff;
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
            background-color:rgb(148, 108, 8);
        }

        a.text-light:hover {
            text-decoration: underline;
            color: #d1e0ff !important;
        }

        .icon-color {
            color: #fff !important;
        }
    </style>
</head>
<body class="bg-dark text-white">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form action="registrar.php" method="POST" class="bg-gradient-primary p-4 rounded shadow-lg w-100" style="max-width: 400px;">
            <h1 class="text-center mb-4">Registrar Nuevo Usuario</h1>
            <div class="form-group">
                <label for="name">Nombre:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="name" class="form-control bg-dark text-white" placeholder="Nombre completo" required>
                </div>
            </div>

            <div class="form-group">
                <label for="user">Nombre de usuario:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                    </div>
                    <input type="text" name="user" class="form-control bg-dark text-white" placeholder="Usuario" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="email" name="email" class="form-control bg-dark text-white" placeholder="Correo" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" class="form-control bg-dark text-white" placeholder="Contraseña" required>
                </div>
            </div>

            <div class="form-group">
                <label for="genero">Género:</label>
                <select name="genero" class="form-control bg-dark text-white">
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                </select>
            </div>

            <button type="submit" name="register" class="btn btn-primary btn-block">Registrar</button>
            <div class="text-center mt-3">
                <a href="index2.php" class="text-light">&#191;Ya tienes una cuenta? Iniciar sesión</a>
            </div>
        </form>
    </div>

    <!-- Enlace a los scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
