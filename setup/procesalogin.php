<?php

include("setup.php");

$sql = "SELECT Id, nombre, email, password FROM usuarios WHERE email = ? AND password = ?";
$result=mysqli_query(conectar(),$sql);
$cont_login=mysqli_num_rows($result);
$datos=mysqli_fetch_array($result);

if($cont_login!=0)
{
    session_start();
    $_SESSION['id_usuario'] = $datos['Id'];
    header('Location:../index.php');
}else{
    header('Location:../index.php'); 
}

?>