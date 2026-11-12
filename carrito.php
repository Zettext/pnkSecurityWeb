<?php
session_start();
include("setup/setup.php");

// Asegurar que la sesión del carrito exista y sea un array
if (!isset($_SESSION["carrito"]) || !is_array($_SESSION["carrito"])) {
    $_SESSION["carrito"] = array();
}

// Validar la operación solicitada de forma segura
$op = isset($_POST['op']) ? $_POST['op'] : '';

switch($op)
{
    case "1": insertar();
        break;
    case "2": eliminaritems();
        break;
    case "3": eliminartodo();
        break;
}

function insertar()
{
    // Sanitizar el ID del producto que viene por POST (Evidencia de seguridad)
    $id_item = filter_var($_POST['iditems'], FILTER_VALIDATE_INT);
    if ($id_item === false) {
        return;
    }

    $conn = conectar();
    
    // Consulta parametrizada para evitar Inyección SQL
    $sql = "SELECT id, nombre, precio FROM items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_item);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_array($result);

    if ($datos) {
        $pos = count($_SESSION["carrito"]) + 1;
        $productos = array(
            "posicion" => $pos,
            "id" => $datos['id'], 
            "nombre" => $datos['nombre'],
            "precio" => $datos['precio']
        );
        $_SESSION["carrito"][$pos] = $productos;
    }
}

function eliminaritems()
{
    $pos = filter_var($_POST['pos'], FILTER_VALIDATE_INT);
    if ($pos !== false && isset($_SESSION["carrito"][$pos])) {
        unset($_SESSION["carrito"][$pos]);
    }
}

function eliminartodo()
{
    unset($_SESSION["carrito"]);
}
?>