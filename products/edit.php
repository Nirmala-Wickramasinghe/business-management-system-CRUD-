<?php
require_once "../config/db.local.php";

// Fetch the existing product data
$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}
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
    <title>Edit Resource | Business Suite</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #3b82f6;
            --accent: #fbbf24;
            --bg-dark: #0f172a;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(251, 191, 36, 0.1) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .edit-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Top Progress Indicator Decor */
        .edit-card::after {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .input-group-custom {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 8px 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .input-group-custom:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            background: rgba(15, 23, 42, 0.8);
        }

        .input-group-custom i {
            color: #475569;
            margin-right: 12px;
        }

        .form-control-custom {
            background: transparent;
            border: none;
            color: white;
            width: 100%;
            outline: none;
            font-weight: 500;
        }

        .btn-update {
            background: linear-gradient(135deg, var(--primary), #2563eb);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            color: white;
            transition: 0.3s;
            margin-top: 1rem;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(59, 130, 246, 0.5);
        }

        .badge-id {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="edit-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 mb-0">Modify Asset</h3>
                    <span class="badge-id">REF: #<?= $product["id"] ?></span>
                </div>

                <form method="POST" action="update.php">
                    <input type="hidden" name="id" value="<?= $product["id"] ?>">

                    <div class="mb-4">
                        <label class="form-label">Asset Descriptor</label>
                        <div class="input-group-custom">
                            <i class="fas fa-pen-nib"></i>
                            <input type="text" name="name" class="form-control-custom" 
                                   value="<?= htmlspecialchars($product["name"]) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Global Valuation (USD)</label>
                        <div class="input-group-custom">
                            <i class="fas fa-dollar-sign"></i>
                            <input type="number" step="0.01" name="price" class="form-control-custom" 
                                   value="<?= $product["price"] ?>" required>
                        </div>
                        <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;">
                            <i class="fas fa-clock me-1"></i> Original entry: $<?= number_format($product["price"], 2) ?>
                        </small>
                    </div>

                    <div class="row g-2">
                        <div class="col-8">
                            <button type="submit" class="btn btn-update w-100">
                                Commit Update <i class="fas fa-check-double ms-2"></i>
                            </button>
                        </div>
                        <div class="col-4">
                            <a href="index.php" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center" 
                               style="border-radius: 12px; border-color: rgba(255,255,255,0.1); color: #64748b;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>