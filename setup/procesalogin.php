<?php
include("setup.php");
$con = conectar();
$sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ss", $_POST['frmusuario'], $_POST['frmpassword']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cont_login = mysqli_num_rows($result);

if($cont_login != 0) {
    $datos = mysqli_fetch_array($result);
    session_start();
    $_SESSION['nombre'] = $datos['nombre'];
    header('Location:../index.php?id=1'); 
} else {
    header('Location:../index.php'); 
}
?>