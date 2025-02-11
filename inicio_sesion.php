<?php
include("con_db.php");

if (isset($_POST['login'])) {
    // Obtener y limpiar los valores del formulario
    $user = trim($_POST['user']);
    $password = trim($_POST['password']);

    if (empty($user) || empty($password)) {
        echo '<h3 class="bad">¡Por favor, complete todos los campos!</h3>';
    } else {
        // Preparar consulta SQL para evitar inyección SQL
        $stmt = $conex->prepare("SELECT user, contraseña FROM usuario WHERE user = ? AND contraseña = ?");
        $stmt->bind_param("ss", $user, $password);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            session_start();
            $_SESSION['user'] = $user; // Guardar sesión del usuario
            echo $user;
            echo $password;
            header("Location:inicio.php");
            exit();
        } else {
            echo '<h3 class="bad">¡Usuario o contraseña incorrectos!</h3>';
        }
    }
}
?>
