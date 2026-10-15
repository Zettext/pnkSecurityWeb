<?php
include("setup/setup.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["carrito"]) || !is_array($_SESSION["carrito"])) {
    $_SESSION["carrito"] = array();
}

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case "1":
            insertar();
            break;
        case "2":
            eliminaritems();
            break;
        case "3":
            eliminartodo();
            break;
    }
}

function insertar()
{
    if (!isset($_POST['iditems']) || !filter_var($_POST['iditems'], FILTER_VALIDATE_INT)) {
        return;
    }

    $idItem = (int)$_POST['iditems'];
    $conexion = conectar();

    $sql = "SELECT id, nombre, precio FROM items WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $idItem);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($datos = mysqli_fetch_assoc($result)) {
            $pos = count($_SESSION["carrito"]) + 1;
            $producto = array(
                "posicion" => $pos,
                "id"       => (int)$datos['id'],
                "nombre"   => htmlspecialchars($datos['nombre'], ENT_QUOTES, 'UTF-8'),
                "precio"   => (float)$datos['precio']
            );
            $_SESSION["carrito"][$pos] = $producto;
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conexion);
}

function eliminaritems()
{
    if (isset($_POST['pos'])) {
        $pos = $_POST['pos'];
        if (isset($_SESSION["carrito"][$pos])) {
            unset($_SESSION["carrito"][$pos]);
        }
    }
}

function eliminartodo()
{
    $_SESSION["carrito"] = array();
}
?>