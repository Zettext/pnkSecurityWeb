<?php

function cargar_env($ruta_env = __DIR__ . '/../.env')
{
    if (!file_exists($ruta_env)) {
        return;
    }

    $lineas = file($ruta_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }

        if (strpos($linea, '=') !== false) {
            list($nombre, $valor) = explode('=', $linea, 2);
            $nombre = trim($nombre);
            $valor = trim($valor);

            if (!array_key_exists($nombre, $_SERVER) && !array_key_exists($nombre, $_ENV)) {
                putenv(sprintf('%s=%s', $nombre, $valor));
                $_ENV[$nombre] = $valor;
                $_SERVER[$nombre] = $valor;
            }
        }
    }
}

function conectar()
{
    cargar_env();

    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: '';
    $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $db   = getenv('DB_NAME') ?: '';

    $con = mysqli_connect($host, $user, $pass, $db);

    if (!$con) {
        error_log("Error en conexión a BD: " . mysqli_connect_error());
        die("Error de conexión al servidor de base de datos.");
    }

    mysqli_set_charset($con, "utf8mb4");
    return $con;
}

function quitarespacios($titulo)
{
    $titulo = str_replace(" ", "", $titulo);
    $cadena = str_replace("ñ", "", $titulo);
    $cadena = str_replace("Ñ", "", $cadena);
    return $cadena;
}

function moneda_chilena($numero)
{
    return '$ ' . number_format((float)$numero, 0, ',', '.');
}

?>