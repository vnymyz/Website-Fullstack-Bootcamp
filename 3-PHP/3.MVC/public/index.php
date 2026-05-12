<!-- ROUTING -->
 <?php

// get URL
$url = $_GET['url'] ?? '';

// remove slash
$url = trim($url, '/');

// HOME ROUTE
if ($url == '') {

    require_once '../app/controllers/HomeController.php';

    $controller = new HomeController();
    $controller->index();
}

// PRODUCT ROUTE
elseif ($url == 'products') {

    require_once '../app/controllers/ProductController.php';

    $controller = new ProductController();
    $controller->index();
}

// PAGE NOT FOUND
else {

    echo "<h1>404 Page Not Found</h1>";
}