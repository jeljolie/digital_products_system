<?php
session_start();
require_once __DIR__ . '/classes/Core.php';

$core = new Core();

/* ---------------------------------
   FETCH CATEGORIES FOR DROPDOWN
----------------------------------*/
$categories = $core->readAll("categories", "1");

/* ---------------------------------
   DELETE PRODUCT
----------------------------------*/
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $core->delete("products", "product_id = $id");
    header("Location: management.php");
    exit;
}

/* ---------------------------------
   EDIT / RENAME / CHANGE CATEGORY
----------------------------------*/
$editMode = false;
$editProduct = null;
$modeLabel = "Add Product";

if (isset($_GET['edit']) || isset($_GET['rename']) || isset($_GET['category'])) {
    $editMode = true;
    $id = isset($_GET['edit']) ? $_GET['edit'] : (isset($_GET['rename']) ? $_GET['rename'] : $_GET['category']);
    $id = (int) $id;
    $editProduct = $core->readById("products", $id, "product_id");

    if (isset($_GET['edit'])) {
        $modeLabel = "Edit Product";
    } elseif (isset($_GET['rename'])) {
        $modeLabel = "Rename Product";
    } elseif (isset($_GET['category'])) {
        $modeLabel = "Change Category";
    }
} else {
    $modeLabel = "Add Product";
}

/* ---------------------------------
   HANDLE ADD / UPDATE SUBMISSION
----------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = isset($_POST['product_id']) && $_POST['product_id'] !== "" ? (int) $_POST['product_id'] : null;
    $imageName = $_POST['existing_image'] ?? "";

    if (!empty($_FILES['image']['name'])) {
        if (!is_dir("uploads")) {
            mkdir("uploads");
        }
        $imageName = "uploads/" . time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imageName);
    }

    $data = [
        "product_name" => $_POST['product_name'],
        "category_id"  => $_POST['category_id'],
        "description"  => $_POST['description'],
        "price"        => $_POST['price'],
        "image"        => $imageName
    ];

    if ($id) {
        $core->update("products", $data, "product_id = $id");
    } else {
        $core->create("products", $data);
    }

    header("Location: management.php");
    exit;
}

/* ---------------------------------
   FETCH ALL PRODUCTS FOR TABLE
----------------------------------*/
$products = $core->readAll(
    "products p INNER JOIN categories c ON p.category_id = c.category_id",
    "1 ORDER BY p.product_id DESC",
    "p.product_id AS id,
     p.product_name,
     p.price,
     p.image,
     c.category_name"
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
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
            }

            .main-content {
                margin-left: 250px;
                padding-top: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">

            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- LOGO + TITLE -->
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

    <!-- SIDEBAR -->
    <div class="offcanvas-lg offcanvas-start bg-light sidebar" id="sidebarMenu" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-3">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-secondary">Dashboard</a>
                </li>
                <li><a href="products.php" class="nav-link text-secondary">Browse All</a></li>
                <li><a href="management.php" class="nav-link active">Management</a></li>
                <li><a href="todos.php" class="nav-link text-dark">Todos List</a></li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="main-content container-fluid mt-5 pt-4">

        <h2>Product Management</h2>

        <!-- ADD / EDIT FORM -->
        <div class="card mb-4 mt-3">
            <div class="card-header bg-dark text-white">
                <?php echo htmlspecialchars($modeLabel); ?>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="product_id" value="<?php echo $editProduct['product_id'] ?? ""; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $editProduct['image'] ?? ""; ?>">

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input
                            type="text"
                            name="product_name"
                            class="form-control"
                            value="<?php echo $editProduct['product_name'] ?? ""; ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Description</label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="3"
                        ><?php echo $editProduct['description'] ?? ""; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach ($categories as $cat) { ?>
                                <option
                                    value="<?php echo $cat['category_id']; ?>"
                                    <?php
                                        if ($editMode && isset($editProduct['category_id']) &&
                                            $cat['category_id'] == $editProduct['category_id']) {
                                            echo "selected";
                                        }
                                    ?>
                                >
                                    <?php echo $cat['category_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price ($)</label>
                        <input
                            type="number"
                            name="price"
                            class="form-control"
                            step="0.01"
                            value="<?php echo $editProduct['price'] ?? ""; ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label><br>
                        <?php if ($editMode && !empty($editProduct['image'])) { ?>
                            <img src="<?php echo $editProduct['image']; ?>" style="height:80px; width:80px; object-fit:cover;"><br><br>
                        <?php } ?>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <button class="btn btn-primary">
                        <?php echo $editMode ? "Save Changes" : "Add Product"; ?>
                    </button>

                    <?php if ($editMode) { ?>
                        <a href="management.php" class="btn btn-secondary">Cancel</a>
                    <?php } ?>

                </form>
            </div>
        </div>

        <!-- PRODUCT LIST TABLE -->
        <h3>All Products</h3>

        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price ($)</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p) { ?>
                    <tr>
                        <td><?php echo $p['product_name']; ?></td>
                        <td><?php echo $p['category_name']; ?></td>
                        <td><?php echo $p['price']; ?></td>
                        <td>
                            <a href="management.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">
                                Edit
                            </a>
                            <a href="management.php?rename=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">
                                Rename
                            </a>
                            <a href="management.php?category=<?php echo $p['id']; ?>" class="btn btn-sm btn-info">
                                Category
                            </a>
                            <a
                                href="management.php?delete=<?php echo $p['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this product?');"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>