<?php
session_start();
require_once "../config/db.local.php";

// 1. Security Check
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Processing the POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? null;
    $name = trim($_POST["name"]);
    $price = $_POST["price"];

    if ($id && $name && $price) {
        $sql = "UPDATE products SET name = :name, price = :price WHERE id = :id";
        $stmt = $conn->prepare($sql);
        
        $params = [
            ":name" => $name,
            ":price" => $price,
            ":id" => $id
        ];

        if ($stmt->execute($params)) {
            // Set a temporary success message and redirect
            header("Location: index.php?status=updated");
            exit;
        } else {
            echo "Error updating record.";
        }
    }
} else {
    // If someone tries to access update.php directly via URL
    header("Location: index.php");
    exit;
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
    <title>Edit Asset | Business Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --accent-gold: #fbbf24;
            --accent-blue: #38bdf8;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: 
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.1), transparent 40%),
                radial-gradient(circle at bottom left, rgba(251, 191, 36, 0.05), transparent 40%);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Top Accent Bar */
        .glass-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-gold));
        }

        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 12px;
            padding: 12px 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2);
            color: white;
        }

        .btn-update {
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.4);
        }

        .status-overlay {
            display: <?php echo ($message === 'updated') ? 'flex' : 'none'; ?>;
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: var(--bg-dark);
            z-index: 10;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .price-preview {
            font-size: 0.8rem;
            color: var(--accent-gold);
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card">
                
                <div class="status-overlay text-center" id="statusBox">
                    <div class="spinner-border text-info mb-3" role="status"></div>
                    <h4 class="text-white">Writing to Database...</h4>
                    <p class="text-muted small">Changes are being synchronized</p>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0"><i class="fas fa-edit me-2 text-info"></i> Edit Asset</h2>
                    <span class="badge bg-dark border border-secondary text-muted">ID: #<?= $product['id'] ?></span>
                </div>

                <form method="POST">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">

                    <div class="mb-3">
                        <label class="small text-uppercase fw-bold opacity-50 mb-2">Resource Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="small text-uppercase fw-bold opacity-50 mb-2">Valuation (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted">$</span>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                        </div>
                        <span class="price-preview"><i class="fas fa-info-circle me-1"></i> Original: $<?= number_format($product['price'], 2) ?></span>
                    </div>

                    <div class="row g-2">
                        <div class="col-8">
                            <button type="submit" class="btn btn-update w-100 text-white">
                                Commit Changes <i class="fas fa-save ms-2"></i>
                            </button>
                        </div>
                        <div class="col-4">
                            <a href="index.php" class="btn btn-outline-secondary w-100 border-0 h-100 d-flex align-items-center justify-content-center" style="border-radius: 12px;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if ($message === 'updated'): ?>
        setTimeout(() => {
            window.location.href = "index.php";
        }, 1500);
    <?php endif; ?>
</script>

</body>
</html>