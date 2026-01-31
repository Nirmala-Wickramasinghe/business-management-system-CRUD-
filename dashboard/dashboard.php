<?php
session_start();
require_once "../config/db.local.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_name = $_SESSION["user_name"] ?? 'User';
$user_role = $_SESSION["user_role"] ?? 'user';

// Fetch Products
$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mock Stats (You can replace these with real SQL COUNT queries)
$total_products = count($products);
$total_value = array_sum(array_column($products, 'price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Business Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --bg-main: #f8f9fa;
            --bg-secondary: #ffffff;
            --sidebar-bg: #1a1d29;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --accent-primary: #4f46e5;
            --accent-hover: #4338ca;
            --border-color: #e2e8f0;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Layout Structure */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            padding: 0;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-primary), #6366f1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .logo-text {
            font-size: 16px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.3px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 12px;
            overflow-y: auto;
        }

        .nav-section-title {
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-tertiary);
            margin-top: 20px;
            margin-bottom: 8px;
        }

        .nav-section-title:first-child {
            margin-top: 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 4px;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .nav-link.active {
            background: rgba(79, 70, 229, 0.15);
            color: white;
            font-weight: 600;
        }

        .nav-link.active i {
            color: var(--accent-primary);
        }

        .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: white;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 32px 40px;
            max-width: 100%;
        }

        /* Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.primary { color: var(--accent-primary); background: #eef2ff; }
        .stat-icon.success { color: var(--success-color); background: #ecfdf5; }
        .stat-icon.warning { color: #f59e0b; background: #fef3c7; }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Main Card */
        .main-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .card-actions {
            display: flex;
            gap: 8px;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            font-size: 14px;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-secondary {
            background: var(--bg-main);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: var(--text-primary);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-icon {
            padding: 8px;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: var(--bg-main);
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .table tbody td {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: var(--bg-main);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-icon {
            width: 40px;
            height: 40px;
            background: var(--bg-main);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 16px;
        }

        .product-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .product-id {
            font-size: 13px;
            color: var(--text-tertiary);
            font-family: 'Monaco', 'Courier New', monospace;
        }

        .price-value {
            font-weight: 600;
            color: var(--success-color);
            font-size: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 4px;
            justify-content: flex-end;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .action-btn:hover {
            background: var(--bg-main);
        }

        .action-btn.edit {
            color: var(--accent-primary);
        }

        .action-btn.delete {
            color: var(--danger-color);
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-admin {
            background: #eef2ff;
            color: var(--accent-primary);
        }

        .badge-user {
            background: #f1f5f9;
            color: var(--text-secondary);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-tertiary);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
            }
            
            .main-content {
                margin-left: 240px;
                padding: 24px 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="logo-text">CoreOS</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Main Menu</div>
            <a href="#" class="nav-link active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-file-invoice"></i>
                <span>Reports</span>
            </a>

            <div class="nav-section-title">Settings</div>
            <a href="#" class="nav-link">
                <i class="fas fa-gear"></i>
                <span>Settings</span>
            </a>
            <a href="../auth/logout.php" class="nav-link">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($user_name) ?></div>
                    <div class="user-role"><?= $user_role ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Welcome back, <?= htmlspecialchars($user_name) ?>! Here's what's happening today.</p>
                </div>
                <?php if ($user_role === 'admin'): ?>
                    <a href="../products/create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add Product</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon primary">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="stat-value"><?= $total_products ?></div>
                <div class="stat-label">Total Products</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value">$<?= number_format($total_value, 0) ?></div>
                <div class="stat-label">Total Value</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-value">Active</div>
                <div class="stat-label">Inventory Status</div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="main-card">
            <div class="card-header">
                <h2 class="card-title">Products Inventory</h2>
                <div class="card-actions">
                    <button class="btn btn-secondary btn-sm">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <button class="btn btn-secondary btn-sm">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <div class="table-container">
                <?php if (count($products) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Price</th>
                                <?php if ($user_role === 'admin'): ?>
                                    <th style="text-align: right;">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <span class="product-id">#<?= str_pad($product["id"], 4, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td>
                                    <div class="product-info">
                                        <div class="product-icon">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <span class="product-name"><?= htmlspecialchars($product["name"]) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="price-value">$<?= number_format($product["price"], 2) ?></span>
                                </td>
                                
                                <?php if ($user_role === 'admin'): ?>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit.php?id=<?= $product["id"] ?>" class="action-btn edit" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="delete.php?id=<?= $product["id"] ?>" 
                                               onclick="return confirm('Are you sure you want to delete this product?')" 
                                               class="action-btn delete" 
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No products found</h3>
                        <p>Get started by adding your first product to the inventory.</p>
                        <?php if ($user_role === 'admin'): ?>
                            <a href="../products/create.php" class="btn btn-primary" style="margin-top: 16px;">
                                <i class="fas fa-plus"></i>
                                <span>Add Product</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>