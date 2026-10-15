<?php
include("setup/setup.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["carrito"]) || !is_array($_SESSION["carrito"])) {
    $_SESSION["carrito"] = array();
}

$key = isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT) ? (int)$_GET['id'] : 0;

$keyid = isset($_GET['keyid']) && filter_var($_GET['keyid'], FILTER_VALIDATE_INT) ? (int)$_GET['keyid'] : $key;

$datos_restorant = null;

if ($key > 0) {
    $conexion = conectar();

    $sql_restorant = "SELECT direcciones.calle, direcciones.numero, direcciones.comuna, direcciones.region, 
                             restautantes.nombre, restautantes.id, restautantes.fono, restautantes.email, restautantes.foto 
                      FROM restautantes 
                      INNER JOIN direcciones ON restautantes.direcciones_id = direcciones.id 
                      WHERE restautantes.id = ?";
    
    $stmt = mysqli_prepare($conexion, $sql_restorant);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $key);
        mysqli_stmt_execute($stmt);
        $result_restorant = mysqli_stmt_get_result($stmt);
        $datos_restorant = mysqli_fetch_assoc($result_restorant);
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conexion);
}

$nombreRestaurante = $datos_restorant ? htmlspecialchars($datos_restorant['nombre'], ENT_QUOTES, 'UTF-8') : 'Restaurante';
$fotoRestaurante   = $datos_restorant ? htmlspecialchars($datos_restorant['foto'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo $nombreRestaurante; ?></title>

  <link rel="stylesheet" href="vendors/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="vendors/themify-icons/themify-icons.css">
  <link rel="stylesheet" href="vendors/owl-carousel/owl.theme.default.min.css">
  <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">

  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<section>
    <div class="container contendor">
      <div class="row">
        <div class="col-lg-4">
          <div style="text-align: center;">
            <div class="media">
              <img class="logosintituciones" src="img/logo.png" width="170px" alt="Logo">
            </div><br><br>
          </div>  
        </div>
        <div class="col-lg-4">
          <div style="text-align: center;">
            <?php if (!empty($fotoRestaurante)): ?>
              <img class="logosintituciones" src="../imagenes/cod<?php echo (int)$key; ?>/<?php echo $fotoRestaurante; ?>" width="170px" alt="<?php echo $nombreRestaurante; ?>">
            <?php else: ?>
              <img class="logosintituciones" src="img/logo_empresa_comodin.png" width="170px" alt="Comodín">
            <?php endif; ?>
          </div>  
        </div>
        <div class="col-lg-4">
          <div class="carro">
            <div class="media float-right">
              <a class="volver" href="index.php?id=<?php echo (int)$keyid; ?>">
                Volver a la Carta
              </a>&nbsp;&nbsp;
              <a class="limpiar" href="#">
                Eliminar Todo
              </a>
            </div>
          </div>  
        </div>
    </div>
  </section>

  <section>
    <div class="container contendor">
      <div class="row">
        <div class="col-lg-12">
         <h4 class="intro-title" style="text-align: center">Productos Agregados para Solicitar</h4>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container contendor">
      <div class="row">
        <div class="col-lg-12">
            <table class="table table-hover table-lg">
              <thead>
                 <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Precios</th>
                    <th scope="col">Acción</th>
                 </tr>
              </thead>
              <tbody>
                 <?php
                 $total = 0;
                 if (!empty($_SESSION["carrito"])) {
                     foreach ($_SESSION["carrito"] as $value) {
                         $prodId     = isset($value["id"]) ? (int)$value["id"] : 0;
                         $prodNombre = isset($value["nombre"]) ? htmlspecialchars($value["nombre"], ENT_QUOTES, 'UTF-8') : '';
                         $prodPrecio = isset($value["precio"]) ? (float)$value["precio"] : 0;
                         $prodPos    = isset($value["posicion"]) ? htmlspecialchars($value["posicion"], ENT_QUOTES, 'UTF-8') : '';
                 ?>
                 <tr>
                    <th scope="row"><?php echo $prodId; ?></th>
                    <td><?php echo $prodNombre; ?></td>
                    <td><?php echo moneda_chilena($prodPrecio); ?></td>
                    <td>
                      <a class="elim" id="<?php echo $prodPos; ?>" href="#">
                        <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-trash-fill text-danger" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z"/>
                        </svg>
                      </a>
                    </td>
                 </tr>
                 <?php
                         $total += $prodPrecio;
                     }
                 } else {
                 ?>
                 <tr>
                    <td colspan="4" class="text-center text-muted">El carrito está vacío.</td>
                 </tr>
                 <?php } ?>
              </tbody>
            </table>
            <div>
              <hr><br>
              <h3 class="price-tag text-center">¡¡¡Si estas listo con tu pedido, llama al garzón para solicitarlo!!!</h3>
            </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="container contendor"><br>
    <hr>
    <div class="container">
      <div class="footer-bottom row align-items-center text-center">
        <p class="footer-text m-0 col-md-12">
            Copyright 2020, Todos los Derechos reservados PNK, <br>
            Sitio desarrollado para la Explotación de Vulnerabilidades Web, Laboratorio de Programación Segura I<br><b>Área Tecnologías de Información y Ciberseguridad - Inacap Sede La Serena</b><br><br>
        </p>
      </div>
    </div>
  </footer>

  <script src="vendors/jquery/jquery-3.2.1.min.js"></script>
  <script src="vendors/bootstrap/bootstrap.bundle.min.js"></script>
  <script src="vendors/owl-carousel/owl.carousel.min.js"></script>
  <script src="vendors/nice-select/jquery.nice-select.min.js"></script>
  <script src="vendors/Magnific-Popup/jquery.magnific-popup.min.js"></script>
  <script src="js/jquery.ajaxchimp.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/controladorajax.js"></script>
</body>
</html>