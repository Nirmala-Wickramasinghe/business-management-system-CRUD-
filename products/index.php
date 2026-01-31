<?php
require_once "../config/db.local.php";

$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
session_start();

// 1. Check if logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Check if the user is an admin
if ($_SESSION["user_role"] !== 'admin') {
    // You can redirect with a message or stop execution
    echo "<script>alert('Access Denied: You do not have permission to view this page.'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --accent: #6366f1;
            --neon-blue: #00d2ff;
        }

        body {
            background-color: var(--bg-dark);
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(0, 210, 255, 0.1) 0%, transparent 40%);
            min-height: 100vh;
        }

        .dashboard-header {
            padding: 40px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* The Glass Table Container */
        .glass-container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .table {
            color: #cbd5e1;
            vertical-align: middle;
        }

        .table thead th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            color: var(--neon-blue);
        }

        .product-row {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .product-row:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: scale(1.01);
            color: white;
        }

        /* Cool Price Tag Styling */
        .price-badge {
            background: rgba(0, 210, 255, 0.1);
            color: var(--neon-blue);
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid rgba(0, 210, 255, 0.3);
            font-weight: 600;
        }

        /* Action Buttons */
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: 0.3s;
            margin: 0 3px;
            text-decoration: none;
        }

        .btn-edit { background: rgba(99, 102, 241, 0.2); color: var(--accent); }
        .btn-edit:hover { background: var(--accent); color: white; }

        .btn-delete { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .btn-delete:hover { background: #ef4444; color: white; }

        .btn-add {
            background: linear-gradient(135deg, var(--accent), #a855f7);
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
            transition: 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.5);
            color: white;
        }
    </style>
</head>
<body>

<div class="container pb-5">
    <div class="dashboard-header">
        <div>
            <h2 class="mb-1">Product Management</h2>
            <p class="text-muted small">Overview of your business stock and pricing.</p>
        </div>
        <a href="create.php" class="btn btn-add text-white">
            <i class="fas fa-plus me-2"></i> Add New Product
        </a>
    </div>

    <div class="glass-container">
        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="50%">Product Name</th>
                        <th width="20%">Price</th>
                        <th width="20%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                        <tr class="product-row">
                            <td><span class="text-muted">#<?= $product["id"] ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(255,255,255,0.05) !important; border: 1px solid rgba(255,255,255,0.1);">
                                        <i class="fas fa-box text-muted small"></i>
                                    </div>
                                    <strong><?= htmlspecialchars($product["name"]) ?></strong>
                                </div>
                            </td>
                            <td><span class="price-badge">$<?= number_format($product["price"], 2) ?></span></td>
                            <td class="text-center">
                                <a href="edit.php?id=<?= $product["id"] ?>" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="delete.php?id=<?= $product["id"] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Archive this product?')" 
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No products found in the system.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>