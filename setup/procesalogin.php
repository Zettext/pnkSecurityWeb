<?php
session_start();
include("setup.php");

// 1. Recibir y sanitizar los datos del formulario (Evidencia H-01)
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$password = $_POST['password'];

$conn = conectar();

// 2. Consulta parametrizada real (Evidencia H-01)
$sql = "SELECT Id, nombre, email, password FROM usuarios WHERE email = ? AND password = ?";
$stmt = mysqli_prepare($conn, $sql);

// Vinculamos los parámetros ("ss" significa dos strings)
mysqli_stmt_bind_param($stmt, "ss", $email, $password);
mysqli_stmt_execute($stmt);

// Obtenemos el resultado
$result = mysqli_stmt_get_result($stmt);
$cont_login = mysqli_num_rows($result);
$datos = mysqli_fetch_array($result);

if($cont_login != 0) {
    $_SESSION['id_usuario'] = $datos['Id'];
    header('Location: ../index.php');
} else {
    // Redirigir si falla el login
    header('Location: ../index.php'); 
}
?>