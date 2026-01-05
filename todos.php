<?php
session_start();
require_once __DIR__ . '/classes/Core.php';

$core = new Core();

/* -------------------------
   HANDLE ACTIONS
--------------------------*/

// ADD TASK (Direct)
if (isset($_POST['add_task'])) {
    $data = [
        "task_name" => $_POST['task_name'],
        "product_id" => !empty($_POST['product_id']) ? $_POST['product_id'] : null,
        "status" => "pending"
    ];
    $core->create("todos", $data);
    header("Location: todos.php");
    exit();
}

// COMPLETE / DELETE
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'complete') {
        $core->update("todos", ["status" => "completed"], "todo_id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $core->delete("todos", "todo_id = $id");
    }
    header("Location: todos.php");
    exit();
}

// Fetch Data
$todos = $core->readAll("todos t LEFT JOIN products p ON t.product_id = p.product_id", "1 ORDER BY t.status DESC, t.todo_id DESC", "t.*, p.product_name");
$products_list = $core->readAll("products", "1", "product_id, product_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todos - Bloombels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* No Animations */
        .collapsing { transition: none !important; display: none; }
        
        body { overflow-x: hidden; background-color: #f8f9fa; }
        .navbar { background: linear-gradient(90deg, #000000 0%, #16213e 100%) !important; }
        
        @media (min-width: 992px) {
            .sidebar { height: 100vh; position: fixed; top: 56px; left: 0; width: 250px; background: #f8f9fa; border-right: 1px solid #dee2e6; overflow-y: auto; }
            .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 20px; }
        }

        .add-task-area {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .todo-card { 
            background: #fff; 
            border: 1px solid #dee2e6; 
            border-radius: 4px; 
            padding: 12px 20px; 
            margin-bottom: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
        }

        .completed { opacity: 0.5; background-color: #fdfdfd; }
        .completed h6 { text-decoration: line-through; color: #888; }
        
        .product-link-text { font-size: 0.75rem; color: #6c757d; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="sun.png" alt="" style="height:40px;">
                <span>Bloombels</span>
            </a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><?php echo $_SESSION['username'] ?? "User"; ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar d-none d-lg-block">
        <div class="p-3">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-1"><a href="dashboard.php" class="nav-link text-secondary">Dashboard</a></li>
                <li class="nav-item mb-1"><a href="products.php" class="nav-link text-secondary">Browse All</a></li>
                <li class="nav-item mb-1"><a href="management.php" class="nav-link text-secondary">Management</a></li>
                <li class="nav-item mb-1"><a href="todos.php" class="nav-link active">Todos List</a></li>
            </ul>
        </div>
    </div>

    <main class="main-content mt-5 pt-4">
        <div class="container-fluid">
            
            <h2 class="h5 mb-3 fw-bold">Task Management</h2>

            <div class="add-task-area shadow-sm">
                <p class="small fw-bold text-uppercase text-muted mb-3">Create New Task</p>
                <form action="todos.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="task_name" class="form-control" placeholder="Task description..." required>
                        </div>
                        <div class="col-md-4">
                            <select name="product_id" class="form-select">
                                <option value="">Optional: Link to Product</option>
                                <?php foreach($products_list as $p): ?>
                                    <option value="<?php echo $p['product_id']; ?>"><?php echo $p['product_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="add_task" class="btn btn-primary w-100">Add Task</button>
                        </div>
                    </div>
                </form>
            </div>

            <hr class="mb-4">

            <div class="row">
                <div class="col-12">
                    <?php foreach ($todos as $todo): ?>
                        <div class="todo-card shadow-sm <?php echo ($todo['status'] == 'completed') ? 'completed' : ''; ?>">
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($todo['task_name']); ?></h6>
                                <?php if ($todo['product_name']): ?>
                                    <span class="product-link-text">Linked to: <?php echo htmlspecialchars($todo['product_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if ($todo['status'] !== 'completed'): ?>
                                    <a href="todos.php?action=complete&id=<?php echo $todo['todo_id']; ?>" class="btn btn-sm btn-success px-3">Done</a>
                                <?php endif; ?>
                                <a href="todos.php?action=delete&id=<?php echo $todo['todo_id']; ?>" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Delete?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>