<?php
session_start();
require_once __DIR__ . '/classes/Core.php';

$core = new Core();

/* -------------------------
   TOTAL PRODUCTS SUMMARY
--------------------------*/
$totalProducts = $core->readAll("products", "1", "COUNT(*) AS total")[0]['total'];

/* -------------------------
   LATEST 3 PRODUCTS
--------------------------*/
$products = $core->readAll(
    "products p INNER JOIN categories c ON p.category_id = c.category_id",
    "1 ORDER BY p.product_id DESC LIMIT 3",
    "p.product_id AS id,
     p.product_name AS product_name,
     p.image,
     p.price,
     c.category_name AS category_name"
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(90deg, #000000 0%, #16213e 100%) !important;
        }

        @media (min-width: 992px) {
            .sidebar {
                height: 100vh;
                position: fixed;
                top: 56px;
                left: 0;
                width: 250px;
                background: #f8f9fa;
                border-right: 1px solid #dee2e6;
                overflow-y: auto;
            }

            .main-content {
                margin-left: 250px;
                padding-top: 20px;
            }
        }

        /* Compact Horizontal Card Styling */
        .product-card-horizontal {
            max-width: 380px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 12px;
            display: flex;
            align-items: center;
            background-color: #fff;
            margin-bottom: 0.75rem;
        }

        .product-card-horizontal img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.25rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="sun.png" alt="" style="height:40px; width:auto;">
                <span>Bloombels</span>
            </a>

            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?php echo $_SESSION['username'] ?? "User"; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="offcanvas-lg offcanvas-start bg-light sidebar" id="sidebarMenu" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-3">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="#" class="nav-link active">Dashboard</a></li>
                <li><a href="products.php" class="nav-link text-secondary">Browse All</a></li>
                <li><a href="management.php" class="nav-link text-secondary">Management</a></li>
                <li><a href="todos.php" class="nav-link text-dark">Todos List</a></li>
            </ul>
        </div>
    </div>

    <main class="main-content container-fluid mt-5 pt-4">

        <h2 class="h4">Welcome to your Dashboard</h2>

        <div class="d-flex align-items-center mt-4 mb-3">
            <h3 class="h6 mb-0 fw-bold text-secondary text-uppercase tracking-wider">Latest Products</h3>
            <span class="badge rounded-pill bg-primary ms-2 px-3"><?php echo $totalProducts; ?> Total</span>
        </div>

        <div class="row">
            <div class="col-12">
                <?php foreach ($products as $product) { ?>
                    <div class="product-card-horizontal shadow-sm">
                        
                        <img src="<?php echo $product['image']; ?>" alt="Product">

                        <div class="d-flex flex-column">
                            <h5 class="mb-0 h6 fw-bold"><?php echo $product['product_name']; ?></h5>
                            <p class="mb-2 small fw-bold text-dark">$<?php echo number_format($product['price'], 2); ?></p>
                            
                            <div class="d-flex gap-1">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary btn-sm py-0 px-2" style="font-size: 0.75rem;">Details</a>

                                <a href="todos.php?product_id=<?php echo $product['id']; ?>" 
                                   class="btn btn-outline-dark btn-sm py-0 px-2" style="font-size: 0.75rem;">Todos</a>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>