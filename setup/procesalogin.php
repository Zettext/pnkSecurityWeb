<?php
include("setup.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['frmusuario'], $_POST['frmpassword'])) {
    $usuario = trim($_POST['frmusuario']);
    $password = trim($_POST['frmpassword']);

    if (empty($usuario) || empty($password)) {
        header('Location: ../index.php?error=campos_vacios');
        exit();
    }

    $conexion = conectar();

    $sql = "SELECT id_usuario, nombre, email, password FROM usuarios WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $usuario, $password);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        if ($datos = mysqli_fetch_assoc($result)) {
            session_start();
            session_regenerate_id(true);
            
            $_SESSION['nombre'] = $datos['nombre'];
            $_SESSION['id_usuario'] = $datos['id_usuario'];
            
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            
            header('Location: ../index.php');
            exit();
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conexion);
}

header('Location: ../index.php?error=login_invalido');
exit();
?>