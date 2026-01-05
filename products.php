<?php
session_start();
require_once __DIR__ . '/classes/Core.php';

$core = new Core();

// Fetch categories for the filter
$categories = $core->readAll("categories", "1", "category_name");

// Get category from URL
$filter = isset($_GET['category']) ? $_GET['category'] : 'all';

/* -------------------------
   FETCH PRODUCTS WITH FILTER
--------------------------*/
$condition = "1";
if ($filter !== 'all') {
    $condition = "c.category_name = '" . addslashes($filter) . "'";
}

$products = $core->readAll(
    "products p INNER JOIN categories c ON p.category_id = c.category_id",
    "$condition ORDER BY p.product_id DESC",
    "p.product_id AS id,
     p.product_name AS product_name,
     p.description,
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
    <title>Browse All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        /* RESTORED ORIGINAL NAVBAR GRADIENT */
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
                z-index: 1000;
            }

            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
                padding: 20px;
            }
        }

        /* COMPACT CARD */
        .product-card {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #fff;
            padding: 10px;
            display: flex;
            height: 165px;
            overflow: hidden; 
        }

        .action-side {
            width: 80px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .action-side img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .info-side {
            flex: 1;
            padding-left: 12px;
            display: flex;
            flex-direction: column;
            min-width: 0; 
        }

        .info-side h5 {
            font-size: 0.9rem;
            font-weight: bold;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .category-text {
            font-size: 0.65rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .desc-text {
            font-size: 0.75rem;
            color: #444;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
            margin-top: 4px;
        }

        .price-text {
            font-size: 0.95rem;
            font-weight: bold;
            margin-top: auto;
        }

        .btn-action {
            padding: 2px 0;
            font-size: 0.65rem;
            width: 100%;
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
        <div class="offcanvas-body d-flex flex-column p-3">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="dashboard.php" class="nav-link text-secondary">Dashboard</a></li>
                <li><a href="products.php" class="nav-link active">Browse All</a></li>
                <li><a href="management.php" class="nav-link text-secondary">Management</a></li>
                <li><a href="todos.php" class="nav-link text-dark">Todos List</a></li>
            </ul>
        </div>
    </div>

    <main class="main-content mt-5 pt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 m-0 fw-bold">Browse All Products</h2>
                <div class="btn-group btn-group-sm">
                    <a href="products.php?category=all" class="btn btn-outline-dark <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
                    <?php foreach ($categories as $cat) { ?>
                        <a href="products.php?category=<?php echo urlencode($cat['category_name']); ?>" 
                           class="btn btn-outline-dark <?php echo $filter === $cat['category_name'] ? 'active' : ''; ?>">
                            <?php echo $cat['category_name']; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="row g-3">
                <?php foreach ($products as $product) { ?>
                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="product-card shadow-sm">
                            <div class="action-side">
                                <img src="<?php echo $product['image']; ?>" alt="">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-action">Details</a>
                                <a href="todos.php?product_id=<?php echo $product['id']; ?>" class="btn btn-outline-dark btn-action">Todos</a>
                            </div>
                            <div class="info-side">
                                <div class="category-text"><?php echo htmlspecialchars($product['category_name']); ?></div>
                                <h5><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                <div class="desc-text">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </div>
                                <div class="price-text">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </div>
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