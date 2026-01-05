<?php
session_start();
require_once __DIR__ . '/classes/Core.php';

$core = new Core();

// 1. Get the ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Fetch the specific product data
// We join with categories so we can show the category name too
$product = $core->readAll(
    "products p INNER JOIN categories c ON p.category_id = c.category_id",
    "p.product_id = $product_id",
    "p.*, c.category_name"
);

// 3. Redirect if product doesn't exist
if (empty($product)) {
    header("Location: products.php");
    exit();
}

// Get the first (and only) result
$item = $product[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['product_name']); ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: linear-gradient(90deg, #000000 0%, #16213e 100%) !important; }
        .product-img { width: 100%; max-width: 400px; border-radius: 8px; border: 1px solid #ddd; }
        .main-container { margin-top: 100px; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="products.php">← Back to Products</a>
        </div>
    </nav>

    <div class="container main-container">
        <div class="card shadow-sm p-4">
            <div class="row g-4">
                <div class="col-md-5 text-center">
                    <img src="<?php echo $item['image']; ?>" class="product-img" alt="Product Image">
                </div>

                <div class="col-md-7">
                    <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($item['category_name']); ?></span>
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></h1>
                    <h3 class="text-primary mb-4">$<?php echo number_format($item['price'], 2); ?></h3>
                    
                    <h5>Description</h5>
                    <p class="text-muted" style="white-space: pre-wrap;"><?php echo htmlspecialchars($item['description']); ?></p>

                    <div class="mt-5 d-flex gap-2">
                        <a href="todos.php?product_id=<?php echo $item['product_id']; ?>" class="btn btn-outline-dark px-4">Manage Tasks</a>
                        <button class="btn btn-primary px-4">Edit Product</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>